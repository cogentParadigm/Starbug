<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/images.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup images
 */
/**
 * @defgroup images
 * global functions
 * @ingroup global
 */
/**
 * Get details about an image.
 * @ingroup images
 * @param string $file the file path
 * @return array containing information about the image
 *      'width': image's width in pixels
 *      'height': image's height in pixels
 *      'extension': commonly used extension for the image
 *      'mime_type': image's MIME type ('image/jpeg', 'image/gif', etc.)
 *      'file_size': image's physical size (in bytes)
 */
function image_info($file) {
  if (!is_file($file)) {
    return FALSE;
  }

  $details = FALSE;
  $data = @getimagesize($file);
  $file_size = @filesize($file);

  if (isset($data) && is_array($data)) {
    $extensions = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
    $extension = array_key_exists($data[2], $extensions) ?  $extensions[$data[2]] : '';
    $details = array('width'     => $data[0],
                     'height'    => $data[1],
                     'extension' => $extension,
                     'file_size' => $file_size,
                     'mime_type' => $data['mime']);
  }

  return $details;
}
/**
 * Get a thumbnail image URL
 * @ingroup images
 * @param string $current_file the path to the original
 * @param int $max_width the desired width in pixels
 * @return string an absolute URL to the thumbnail
 */
function image_thumb($current_file, $max_width) {
	$loc = uri("app/public/php/phpthumb/phpThumb.php");
	$loc .= "?w=$max_width&src=".$current_file;
	return $loc;
}
?>
