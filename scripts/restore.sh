#!/usr/bin/env bash
set -euo pipefail

# Restore PostgreSQL from a Cloudinary raw backup created by scripts/backup.sh
# Usage: ./scripts/restore.sh backups/pg_YYYYmmdd_HHMMSS.dump
# (note: do NOT include the .gz extension; the script will resolve it via Cloudinary)
#
# Env required:
#   INTERNAL_DATABASE_URL, CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET

: "${INTERNAL_DATABASE_URL:?Missing INTERNAL_DATABASE_URL}"
: "${CLOUDINARY_CLOUD_NAME:?Missing CLOUDINARY_CLOUD_NAME}"
: "${CLOUDINARY_API_KEY:?Missing CLOUDINARY_API_KEY}"
: "${CLOUDINARY_API_SECRET:?Missing CLOUDINARY_API_SECRET}"

if [ $# -lt 1 ]; then
  echo "Usage: $0 backups/pg_YYYYMMDD_HHMMSS.dump" >&2
  exit 1
fi

PUBLIC_ID="$1"  # e.g., backups/pg_20251119_031500.dump
# Normalize: ensure no .gz suffix in public_id for signed download
if [[ "$PUBLIC_ID" == *.gz ]]; then
  PUBLIC_ID="${PUBLIC_ID%.gz}"
fi
TMP_FILE="/tmp/restore.dump.gz"

# Build Cloudinary signed download URL for raw asset
TS=$(date +%s)
TO_SIGN="public_id=${PUBLIC_ID}&timestamp=${TS}"
SIG=$(printf "%s" "${TO_SIGN}${CLOUDINARY_API_SECRET}" | sha1sum | awk '{print $1}')

# Try direct delivery with HTTP Basic Auth (works regardless of access mode)
DELIVERY_URL="https://res.cloudinary.com/${CLOUDINARY_CLOUD_NAME}/raw/upload/${PUBLIC_ID}.gz"
echo "Downloading (auth) from: ${DELIVERY_URL}"
HTTP_CODE=$(curl -sSL -u "${CLOUDINARY_API_KEY}:${CLOUDINARY_API_SECRET}" -w "%{http_code}" -o "$TMP_FILE" "$DELIVERY_URL")
if [ "$HTTP_CODE" != "200" ]; then
  echo "Authenticated download failed with HTTP $HTTP_CODE for public_id: ${PUBLIC_ID}" >&2
  exit 2
fi

# Optional: put app in maintenance mode if running from web dyno
# php artisan down || true

echo "Dropping schema and restoring…"
psql "$INTERNAL_DATABASE_URL" -v ON_ERROR_STOP=1 -c "DROP SCHEMA IF EXISTS public CASCADE; CREATE SCHEMA public;"

gunzip -c "$TMP_FILE" | pg_restore \
  --no-owner --no-privileges --disable-triggers --clean --if-exists \
  --dbname="$INTERNAL_DATABASE_URL"

rm -f "$TMP_FILE"
# php artisan up || true

echo "Restore finished."