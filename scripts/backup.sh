#!/usr/bin/env bash
set -euo pipefail
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-interior_design}"
DB_USERNAME="${DB_USERNAME:-root}"
BACKUP_DIR="${BACKUP_DIR:-backups}"
mkdir -p "$BACKUP_DIR"
STAMP="$(date +%Y%m%d_%H%M%S)"
mysqldump --single-transaction -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p "$DB_DATABASE" > "$BACKUP_DIR/${DB_DATABASE}_${STAMP}.sql"
echo "Backup created in $BACKUP_DIR"
