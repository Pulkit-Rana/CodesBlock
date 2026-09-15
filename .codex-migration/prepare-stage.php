<?php
$db = new mysqli('127.0.0.1', 'root', 'root', 'codesblock_prod_stage', 10004);
if ($db->connect_error) {
    fwrite(STDERR, $db->connect_error . PHP_EOL);
    exit(1);
}
$row = $db->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins' LIMIT 1")->fetch_assoc();
$plugins = unserialize($row['option_value'], ['allowed_classes' => false]);
$excluded = [
    'google-site-kit/google-site-kit.php',
    'mailpoet/mailpoet.php',
];
$plugins = array_values(array_diff($plugins, $excluded));
$value = serialize($plugins);
$stmt = $db->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
$stmt->bind_param('s', $value);
$stmt->execute();
echo 'active_plugins=' . count($plugins) . PHP_EOL;
