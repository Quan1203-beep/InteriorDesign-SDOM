#!/usr/bin/env bash
set -euo pipefail
mkdir -p backups
stamp="$(date +%Y%m%d_%H%M%S)"
: "${DB_HOST:=127.0.0.1}"
: "${DB_PORT:=3306}"
: "${DB_DATABASE:=interior_design}"
: "${DB_USERNAME:=root}"
: "${DB_PASSWORD:=}"
export MYSQL_PWD="$DB_PASSWORD"
mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" --single-transaction --routines --triggers "$DB_DATABASE" > "backups/${DB_DATABASE}_${stamp}.sql"
unset MYSQL_PWD
echo "Backup created: backups/${DB_DATABASE}_${stamp}.sql"
