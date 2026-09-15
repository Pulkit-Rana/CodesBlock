set -eu
cd /home/u955006780/domains/codesblock.io/public_html
quarantine=/home/u955006780/security-20260912/quarantine
mkdir -p "$quarantine/root-theme-files"
for file in .git .gitignore archive-course.php assets comments.php footer.php front-page.php functions.php header.php inc.php index.php page-articles.php page.php single-course.php single.php style.css template-parts; do
  if [ -e "$file" ]; then mv "$file" "$quarantine/root-theme-files/"; fi
done
php -d disable_functions= /usr/local/bin/wp core download --version=7.1 --skip-content --force
chmod 644 index.php
cat > .htaccess <<'HTACCESS'
Options -Indexes
<FilesMatch "(?i)^(wp-config\.php|\.env|\.user\.ini|.*\.(sql|sql\.gz|tar|tar\.gz|zip|bak|old|log))$">
Require all denied
</FilesMatch>
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule (^|/)\.git(/|$) - [F,L]
RewriteRule ^wp-content/uploads/.*\.(php[0-9]?|phtml|phar)(/|$) - [F,L,NC]
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
HTACCESS
chmod 644 .htaccess
php -d disable_functions= /usr/local/bin/wp core verify-checksums --include-root
printf '\nPLUGIN CHECKSUMS\n'
php -d disable_functions= /usr/local/bin/wp plugin verify-checksums --all --strict --skip-plugins --skip-themes || true
printf '\nPLUGIN INVENTORY\n'
php -d disable_functions= /usr/local/bin/wp plugin list --skip-plugins --skip-themes --format=json
printf '\nTHEME INVENTORY\n'
php -d disable_functions= /usr/local/bin/wp theme list --skip-plugins --skip-themes --format=json
printf '\nUSER INVENTORY\n'
php -d disable_functions= /usr/local/bin/wp user list --fields=ID,user_login,roles,user_registered --skip-plugins --skip-themes --format=json
printf '\nCRON INVENTORY\n'
php -d disable_functions= /usr/local/bin/wp cron event list --fields=hook,recurrence --skip-plugins --skip-themes --format=json
