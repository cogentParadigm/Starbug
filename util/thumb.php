<?php
$sb->provide("util/thumb");
function get_thumb($current_file, $max_width) {
	$loc = Etc::WEBSITE_URL."core/app/public/php/phpthumb/phpThumb.php";
	$loc .= "?w=$max_width&src=../../../../".$current_file;
	return $loc;
}
?>
