#!/usr/bin/env bash
set -euo pipefail
cd /home/negwrhnd/public_html
test "$(wp option get home --skip-plugins --skip-themes)" = 'https://goodfruitph.com'
stage=$(mktemp -d "$HOME/gfe-stage.XXXXXX")
trap 'rm -rf -- "$stage"' EXIT
tar -xzf "$HOME/gfe-industrial.tgz" -C "$stage"
find "$stage/gfe-industrial" -name '*.php' -exec php -l '{}' \;
php -l "$stage/configure-product.php"
export GFE_BACKUP="$HOME/gfe-product-backups/$(date -u +%Y%m%dT%H%M%S)-$$"
mkdir -p "$GFE_BACKUP"
if test -d wp-content/plugins/gfe-industrial; then
  cp -a wp-content/plugins/gfe-industrial "$GFE_BACKUP/"
fi
wp plugin is-active gfe-industrial > /dev/null 2>&1 && echo active > "$GFE_BACKUP/plugin-status.txt" || echo inactive > "$GFE_BACKUP/plugin-status.txt"
wp eval-file "$stage/configure-product.php" --skip-themes
mkdir -p wp-content/plugins/gfe-industrial
cp "$stage/gfe-industrial/product.css" wp-content/plugins/gfe-industrial/
cp "$stage/gfe-industrial/product.php" wp-content/plugins/gfe-industrial/
cp "$stage/gfe-industrial/gfe-industrial.php" wp-content/plugins/gfe-industrial/
wp plugin activate gfe-industrial --skip-themes
wp cache flush --skip-plugins --skip-themes
if wp help litespeed-purge >/dev/null 2>&1; then wp litespeed-purge all; fi
echo "Template installed. Backup: $GFE_BACKUP"
