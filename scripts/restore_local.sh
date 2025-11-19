#!/usr/bin/env bash
set -euo pipefail

: "${INTERNAL_DATABASE_URL:?Missing INTERNAL_DATABASE_URL}"
BACKUP_DIR="${BACKUP_DIR:-/var/data/backups}"

if [ $# -lt 1 ]; then
  echo "Usage: $0 pg_YYYYmmdd_HHMMSS.dump.gz" >&2
  exit 1
fi
NAME="$1" # e.g., pg_20251119_031500.dump.gz
FILE="${BACKUP_DIR}/${NAME}"

if [ ! -f "$FILE" ]; then
  echo "Local backup not found: $FILE" >&2
  exit 2
fi

echo "Restoring from local file: $FILE"
psql "$INTERNAL_DATABASE_URL" -v ON_ERROR_STOP=1 -c "DROP SCHEMA IF EXISTS public CASCADE; CREATE SCHEMA public;"

gunzip -c "$FILE" | pg_restore \
  --no-owner --no-privileges --disable-triggers --clean --if-exists \
  --dbname="$INTERNAL_DATABASE_URL"

echo "Local restore finished."