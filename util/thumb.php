<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file util/thumb.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup thumb
 */
/**
 * @defgroup thumb
 * provides thumbnailing functions via phpThumb
 * @ingroup util
 */
$sb->provide("util/thumb");
/**
 * get a dynamic url for an image at a specified with
 * @ingroup thumb
 * @param string $current_file the location of the original image
 * @param int $max_width the width of the thumbnail to be generated
 */
function get_thumb($current_file, $max_width) {
	$loc = uri("app/public/php/phpthumb/phpThumb.php");
	$loc .= "?w=$max_width&src=".$current_file;
	return $loc;
}
?>
