<?php
function get_thumb($current_file, $max_width) {

	$base_dir = dirname(__file__)."/../";
	
	$current_size = getimagesize($base_dir.$current_file);
	$current_img_width = $current_size[0];
	$current_img_height = $current_size[1];
	$image_base = explode('.', $current_file);
	
	$image_basename = end(split("/", $image_base[0]));
	$image_ext = $image_base[1];
	$thumb_name = "public/thumbnails/".$image_basename.'.'.$image_ext;

	if ($current_img_width > $max_width) {
		$too_big_diff_ratio = $current_img_width/$max_width;
		$new_img_width = $max_width;
		$new_img_height = round($current_img_height/$too_big_diff_ratio);
		$make_magick = exec("convert -geometry $new_img_width x $new_img_height $base_dir$current_file $base_dir$thumb_name", $retval);

		if (!($retval)) return uri($thumb_name);
		else return 'Error: Please try again.';

	} else return uri($current_file);
}
?>
