set -eu
cd /home/u955006780/domains/codesblock.io/public_html
wpcli() { php -d disable_functions= /usr/local/bin/wp "$@" --skip-plugins --skip-themes; }
umask 077
wpcli db export /home/u955006780/security-20260912/database-before-hardening.sql
wpcli user meta list 8 --format=json > /home/u955006780/security-20260912/user-8-meta.json
wpcli user set-role 8 ''
wpcli user session destroy 8 --all
wpcli user application-password delete 8 --all
wpcli plugin deactivate wp-file-manager
mv wp-content/plugins/wp-file-manager /home/u955006780/security-20260912/quarantine/wp-file-manager
wpcli config shuffle-salts
wpcli config set DISALLOW_FILE_EDIT true --raw
wpcli config set WP_ENVIRONMENT_TYPE production
chmod 600 wp-config.php
cat > wp-content/uploads/.htaccess <<'HTACCESS'
Options -Indexes
<FilesMatch "(?i)\.(php[0-9]?|phtml|phar)(\.|$)">
Require all denied
</FilesMatch>
HTACCESS
chmod 644 wp-content/uploads/.htaccess
wpcli plugin update mailpoet microsoft-clarity google-site-kit
printf '\nPERSISTENCE INDICATORS\n'
grep -RlnE 'mnx_daily_cron_event|mrnewjibon|zetgifari|contack-us|GIF89a' wp-content --include='*.php' || true
printf '\nUSER ACCESS AFTER CONTAINMENT\n'
wpcli user list --fields=ID,user_login,roles --format=json
printf '\nAPPLICATION PASSWORD COUNTS\n'
wpcli user application-password list 1 --fields=uuid,name,created,last_used --format=json
printf '\nPHP AUTO-PREPEND\n'
php -i | grep -E 'auto_prepend_file|auto_append_file'
