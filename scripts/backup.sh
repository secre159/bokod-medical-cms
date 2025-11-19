#!/usr/bin/env bash
set -euo pipefail

# Backup PostgreSQL via pg_dump, gzip it, and upload to Cloudinary as a raw asset (signed upload).
# Requirements (already present in your Docker image): php, curl, gzip, postgresql-client.
# Env needed:
#   INTERNAL_DATABASE_URL  - postgres://user:pass@host:5432/dbname (use Render internal URL in worker)
#   CLOUDINARY_CLOUD_NAME  - your Cloudinary cloud name
#   CLOUDINARY_API_KEY     - key
#   CLOUDINARY_API_SECRET  - secret
# Optional:
#   BACKUP_FOLDER          - folder path in Cloudinary raw (default: backups)

: "${INTERNAL_DATABASE_URL:?Missing INTERNAL_DATABASE_URL}"
: "${CLOUDINARY_CLOUD_NAME:?Missing CLOUDINARY_CLOUD_NAME}"
: "${CLOUDINARY_API_KEY:?Missing CLOUDINARY_API_KEY}"
: "${CLOUDINARY_API_SECRET:?Missing CLOUDINARY_API_SECRET}"
BACKUP_FOLDER="${BACKUP_FOLDER:-backups}"

TS="$(date +%Y%m%d_%H%M%S)"
BASE="pg_${TS}.dump"           # base name without .gz
PUBLIC_ID="${BASE}"            # let Cloudinary folder param place it in backups/
FILE="/tmp/${BASE}.gz"         # local file name to upload

echo "Creating pg_dump -> ${FILE}"
pg_dump --no-owner --no-privileges --format=custom "$INTERNAL_DATABASE_URL" | gzip -9 > "$FILE"

# Signed upload to Cloudinary raw/upload
EPOCH=$(date +%s)
# Include all params (alphabetically) used in the request for signature
# folder, overwrite, public_id, timestamp
STRING_TO_SIGN="folder=${BACKUP_FOLDER}&overwrite=true&public_id=${PUBLIC_ID}&timestamp=${EPOCH}"
SIG=$(php -r 'echo sha1($argv[1].$argv[2]);' "$STRING_TO_SIGN" "$CLOUDINARY_API_SECRET")

UPLOAD_URL="https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/raw/upload"
echo "Uploading to Cloudinary raw: ${PUBLIC_ID}"
curl -sS -X POST "$UPLOAD_URL" \
  -F "file=@${FILE}" \
  -F "api_key=${CLOUDINARY_API_KEY}" \
  -F "timestamp=${EPOCH}" \
  -F "public_id=${PUBLIC_ID}" \
  -F "folder=${BACKUP_FOLDER}" \
  -F "overwrite=true" \
  -F "signature=${SIG}" > /tmp/upload.json

# Print resulting URL (without requiring jq)
SECURE_URL=$(php -r '($r=json_decode(file_get_contents("/tmp/upload.json"),true))&&isset($r["secure_url"])&&print($r["secure_url"]);')
if [ -n "${SECURE_URL:-}" ]; then
  echo "Backup complete: ${SECURE_URL}"
else
  echo "Upload response:" && cat /tmp/upload.json && echo "" >&2
  echo "Backup finished, but could not parse secure_url." >&2
fi
