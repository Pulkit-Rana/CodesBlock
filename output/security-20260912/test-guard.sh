set -eu
cd /home/u955006780/domains/codesblock.io/public_html
php -d disable_functions= /usr/local/bin/wp eval 'global $wp_filter; foreach ($wp_filter["determine_current_user"]->callbacks as $p=>$c) { echo $p," "; foreach($c as $v) { echo is_string($v["function"])?$v["function"]:get_debug_type($v["function"])," "; } echo "\n"; } var_dump(apply_filters("determine_current_user",8));'
