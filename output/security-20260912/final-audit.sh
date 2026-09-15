set -eu
cd /home/u955006780/domains/codesblock.io/public_html
wpcli() { php -d disable_functions= /usr/local/bin/wp "$@" --skip-plugins --skip-themes; }
wpcli db query "SELECT option_name FROM wp_options WHERE option_value REGEXP 'mrnewjibon|contack-us|zetgifari|eval[(]|gzinflate[(]'"
wpcli db query "SELECT ID,post_type,post_status FROM wp_posts WHERE post_content REGEXP 'mrnewjibon|contack-us|zetgifari|<script|<iframe'"
printf '\nUPLOAD EXECUTABLE CONTENT\n'
grep -rlIa '<?php' wp-content/uploads | head -30 || true
printf '\nUSER INI FILES\n'
find /home/u955006780/domains -type f \( -name .user.ini -o -name php.ini \)
printf '\nPOST COUNTS\n'
wpcli db query 'SELECT post_type,post_status,COUNT(*) count FROM wp_posts GROUP BY post_type,post_status'
printf '\nPHP LINT\n'
find wp-content/themes/codesblock wp-content/plugins/codesblock-core wp-content/plugins/codesblock-commerce -name '*.php' -print0 | xargs -0 -n1 php -l | tail -5
printf '\nPLUGIN VERIFICATION AFTER UPDATES\n'
wpcli plugin verify-checksums --all --strict || true
printf '\nADMIN SESSION COUNTS\n'
wpcli user session list 1 --format=count
printf '\nHOSTING CONFIG METADATA\n'
ls -la /home/u955006780/.cl.selector
