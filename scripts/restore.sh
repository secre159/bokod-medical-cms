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
export PUBLIC_ID

LIST_URL="https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/resources/raw/upload?prefix=${PUBLIC_ID}&max_results=100"
TMP_JSON="/tmp/cloud_list.json"

echo "Querying Cloudinary for ${PUBLIC_ID}"
curl -sS -u "${CLOUDINARY_API_KEY}:${CLOUDINARY_API_SECRET}" -G "$LIST_URL" > "$TMP_JSON"

SECURE_URL=$(php -r '
  $json = json_decode(file_get_contents(getenv("TMP_JSON")), true);
  $id = getenv("PUBLIC_ID");
  foreach (($json["resources"] ?? []) as $r) {
    if (($r["public_id"] ?? "") === $id) { echo $r["secure_url"] ?? ""; exit; }
  }
' )

if [ -z "${SECURE_URL:-}" ]; then
  # Try deterministic delivery URL
  FALLBACK_URL="https://res.cloudinary.com/${CLOUDINARY_CLOUD_NAME}/raw/upload/${PUBLIC_ID}.gz"
  echo "Admin API did not return a match. Trying fallback URL: $FALLBACK_URL" >&2
  CODE=$(curl -sI -o /dev/null -w "%{http_code}" "$FALLBACK_URL")
  if [ "$CODE" = "200" ]; then
    SECURE_URL="$FALLBACK_URL"
  else
    echo "Backup not found for public_id: ${PUBLIC_ID}" >&2
    echo "Response:" && cat "$TMP_JSON" && echo "" >&2
    exit 2
  fi
fi

TMP_FILE="/tmp/restore.dump.gz"
echo "Downloading: ${SECURE_URL}"
curl -sS -L "$SECURE_URL" -o "$TMP_FILE"

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