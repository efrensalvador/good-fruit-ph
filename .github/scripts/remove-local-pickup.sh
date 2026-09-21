#!/usr/bin/env bash
set -euo pipefail
cd /home/negwrhnd/public_html
test "$(wp option get home --skip-plugins --skip-themes)" = 'https://goodfruitph.com'
php -l "$HOME/remove-local-pickup.php"
export GFE_BACKUP="$HOME/gfe-shipping-backups/$(date -u +%Y%m%dT%H%M%S)-$$"
mkdir -p "$GFE_BACKUP"
wp eval-file "$HOME/remove-local-pickup.php" --skip-themes
wp cache flush --skip-plugins --skip-themes
if wp help litespeed-purge >/dev/null 2>&1; then wp litespeed-purge all; fi
rm -f "$HOME/remove-local-pickup.php"
echo "Local pickup removal complete. Backup: $GFE_BACKUP"
