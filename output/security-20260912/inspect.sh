set -u
cd /home/u955006780
umask 077
mkdir -p security-20260912
tar -czf security-20260912/restored-before-repair.tar.gz -C domains/codesblock.io public_html
cp .logs/error_log_codesblock_io security-20260912/
cd domains/codesblock.io/public_html
ls -la
printf '\nCONFIG STRUCTURE (values redacted)\n'
sed -E "s/(define\([[:space:]]*'[^']+'[[:space:]]*,).*/\1 REDACTED);/" wp-config.php
printf '\nCORE CHECKSUMS\n'
php -d disable_functions= /usr/local/bin/wp core verify-checksums --include-root --skip-plugins --skip-themes
printf '\nPHP IN UPLOADS\n'
find wp-content/uploads -type f \( -iname '*.php' -o -iname '*.phtml' -o -iname '*.phar' \)
printf '\nMU PLUGINS AND DROPINS\n'
find wp-content -maxdepth 2 -type f
printf '\nSERVER ERROR LOG\n'
tail -80 /home/u955006780/.logs/error_log_codesblock_io
