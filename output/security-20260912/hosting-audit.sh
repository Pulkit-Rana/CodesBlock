set -eu
cd /home/u955006780/domains/codesblock.io/public_html
wpcli() { php -d disable_functions= /usr/local/bin/wp "$@" --skip-plugins --skip-themes; }
printf '\nDB PRIVILEGES (AUTH MATERIAL REDACTED)\n'
wpcli db query 'SHOW GRANTS FOR CURRENT_USER' | sed -E "s/IDENTIFIED.*/[AUTH REDACTED]/"
printf '\nWORLD WRITABLE FILES\n'
find . -type f -perm -0002 -print | head -30
printf '\nSYMLINKS\n'
find . -type l -print
printf '\nAUTOMATIC UPDATE CONFIG\n'
wpcli config get WP_AUTO_UPDATE_CORE --type=constant || true
printf '\nACTIVE HOOK FOR STALE CRON\n'
php -d disable_functions= /usr/local/bin/wp eval 'echo has_action("mnx_daily_cron_event") ? "registered" : "orphaned";'
printf '\nDEPLOYED COMMIT\n'
git -C wp-content/themes/codesblock rev-parse HEAD
printf '\nADMIN SESSION INVALIDATION\n'
wpcli user session destroy 1 --all
