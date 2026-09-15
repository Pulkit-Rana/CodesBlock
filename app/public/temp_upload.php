<?php
require_once('wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

$high_res_file = 'C:/Users/pulki/.gemini/antigravity/brain/799d593c-9268-4b78-afaa-80eb07501df9/.user_uploaded/media_1789226377007.jpg';
$upload_dir = wp_upload_dir();
$image_data = file_get_contents($high_res_file);
$filename = 'system-design-thumbnail-highres.jpg';
if(wp_mkdir_p($upload_dir['path'])) {
	$file_dest = $upload_dir['path'] . '/' . $filename;
} else {
	$file_dest = $upload_dir['basedir'] . '/' . $filename;
}
file_put_contents($file_dest, $image_data);
$wp_filetype = wp_check_filetype($filename, null);
$attachment = array(
	'post_mime_type' => $wp_filetype['type'],
	'post_title' => 'Crack the System Design Interview',
	'post_content' => '',
	'post_status' => 'inherit'
);
$attach_id = wp_insert_attachment($attachment, $file_dest);
$attach_data = wp_generate_attachment_metadata($attach_id, $file_dest);
wp_update_attachment_metadata($attach_id, $attach_data);

$courses = get_posts(['post_type' => 'course', 'posts_per_page' => -1]);
foreach ($courses as $course) {
	if (stripos($course->post_title, 'system design') !== false || stripos($course->post_name, 'system-design') !== false) {
		set_post_thumbnail($course->ID, $attach_id);
		echo 'Successfully set thumbnail ID ' . $attach_id . ' for course ' . $course->ID . "\n";
	}
}
echo "Done.";
?>
