set -eu
site=/home/u955006780/domains/codesblock.io/public_html
case "$site" in /home/u955006780/domains/codesblock.io/public_html) ;; *) exit 1;; esac
cd "$site"
umask 077
mkdir -p /home/u955006780/security-20260912/quarantine
mv 'wp-admin/mediia 2.php' /home/u955006780/security-20260912/quarantine/mediia-2.php.inert
sha256sum /home/u955006780/security-20260912/quarantine/mediia-2.php.inert
printf '\nMU PLUGINS\n'
cat wp-content/mu-plugins/*.php
printf '\nPLUGIN SUSPICIOUS FUNCTIONS (filenames only)\n'
grep -rlE 'eval[[:space:]]*\(|gzinflate[[:space:]]*\(|raw.githubusercontent.com|base64_decode[[:space:]]*\(' wp-content --include='*.php' | head -80
printf '\nBOOTSTRAP FREE CORE VERSION\n'
php -d disable_functions= /usr/local/bin/wp core version
