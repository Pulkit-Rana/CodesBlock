set -eu
cd /home/u955006780
mkdir -p security-20260912/official-pmpro
curl -fsSL https://api.github.com/repos/strangerstudios/paid-memberships-pro/tarball/3.8.5 -o security-20260912/pmpro-3.8.5.tar.gz
tar -xzf security-20260912/pmpro-3.8.5.tar.gz --strip-components=1 -C security-20260912/official-pmpro
printf '\nPMPRO COMPARISON\n'
diff -qr security-20260912/official-pmpro domains/codesblock.io/public_html/wp-content/plugins/paid-memberships-pro || true
cd domains/codesblock.io/public_html
wpcli() { php -d disable_functions= /usr/local/bin/wp "$@" --skip-plugins --skip-themes; }
printf '\nDB INDICATOR ROW NAMES\n'
wpcli db query "SELECT option_name FROM wp_options WHERE option_value REGEXP 'mrnewjibon|contack-us|zetgifari|eval\\(|gzinflate\\('" || true
wpcli db query "SELECT ID,post_type,post_status FROM wp_posts WHERE post_content REGEXP 'mrnewjibon|contack-us|zetgifari|<script|<iframe'" || true
printf '\nREGISTRATION SETTINGS\n'
wpcli option get default_role
wpcli option get users_can_register
printf '\nSTALE CRON DEFINITION\n'
wpcli cron event list --fields=hook,args --format=json
printf '\nTHEME CHECKSUM\n'
wpcli theme verify-checksums twentytwentyfive
printf '\nUPLOAD EXECUTABLE CONTENT\n'
grep -rlIa '<?php' wp-content/uploads | head -30 || true
printf '\nUSER INI FILES\n'
find /home/u955006780/domains -type f \( -name .user.ini -o -name php.ini \)
printf '\nPOST COUNTS\n'
wpcli db query 'SELECT post_type,post_status,COUNT(*) count FROM wp_posts GROUP BY post_type,post_status'
