<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
$sb->provide("util/thumb");
function get_thumb($current_file, $max_width) {
	$loc = Etc::WEBSITE_URL."core/app/public/php/phpthumb/phpThumb.php";
	$loc .= "?w=$max_width&src=../../../../".$current_file;
	return $loc;
}
?>
