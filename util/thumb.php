<?php
function get_thumb($current_file, $max_width) {
	$loc = Etc::WEBSITE_URL."core/public/php/phpthumb/phpThumb.php";
	$loc .= "?w=$max_width&src=../../../../".$current_file;
	return $loc;
}
?>
