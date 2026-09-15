set -eu
cd /home/u955006780/domains/codesblock.io/public_html
wpcli() { php -d disable_functions= /usr/local/bin/wp "$@" --skip-plugins --skip-themes; }
wpcli option update cbsecurity_blocked_users '[8]' --format=json
wpcli cron event delete mnx_daily_cron_event
php -l /home/u955006780/security-20260912/codesblock-security.php
cp /home/u955006780/security-20260912/codesblock-security.php wp-content/mu-plugins/codesblock-security.php
chmod 644 wp-content/mu-plugins/codesblock-security.php
php -d disable_functions= /usr/local/bin/wp eval '$u=apply_filters("authenticate",get_user_by("id",8),"",""); echo is_wp_error($u) ? "Blocked account denied\n" : "FAIL\n"; echo apply_filters("determine_current_user",8) === 0 ? "Blocked cookies denied\n" : "FAIL\n"; $m=apply_filters("xmlrpc_methods",array("pingback.ping"=>1,"wp.getUsersBlogs"=>2)); echo !isset($m["pingback.ping"]) && isset($m["wp.getUsersBlogs"]) ? "Pingback disabled, other XML-RPC retained\n" : "FAIL\n";'
php -d disable_functions= /usr/local/bin/wp cache flush
php -d disable_functions= /usr/local/bin/wp litespeed-purge all || true
