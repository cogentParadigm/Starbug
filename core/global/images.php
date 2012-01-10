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
  if (!is_file($file)) return FALSE;

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
 * Create a new image resource
 * @ingroup images
 * @param int $width the width of the image to create
 * @param int $height the height of the image to create
 * @return mixed a new image resource
 */
function image_create($width, $height) {
	if (class_exists("Imagick")) {
		$im = new Imagick();
		$im->newImage($width, $height, "none");
		return $im;
	} else {
		$im = imagecreatetruecolor($width, $height);
		// apply PNG 24-bit transparency to background
		$transparency = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagealphablending($im, FALSE);
		imagefilledrectangle($im, 0, 0, $width, $height, $transparency);
		imagealphablending($im, TRUE);
		imagesavealpha($im, TRUE);
		return $im;
	}
}
/**
 * Open an image
 * @ingroup images
 * @param string $path the file path
 */
function image_open($path) {
	if (class_exists("Imagick")) {
		$image = new Imagick();
		$image->readImage($path);
		return $image;
	} else {
		if ($format == "auto") $format = end(explode(".", $path));
		$format = str_replace('jpg', 'jpeg', $format);
		$open_func = 'imageCreateFrom'. $format;
		if (!function_exists($open_func)) return FALSE;
		return $open_func($file);
	}
}
/**
 * Save an image
 * @ingroup images
 * @param mixed $image the image object or resource
 * @param string $path the file path
 */
function image_save($image, $path, $format="auto") {
	if ($format == "auto") $format = end(explode(".", $path));
	switch (gettype($image)) {
		case "object":
			$image->setImageFormat($format);
			$image->writeImage($path);
		case "resource":
			image_gd_close($image, $path, $format);
			break;
	}
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
/**
 * Composite one image onto another
 * @ingroup images
 * @param mixed $dest the image to copose onto
 * @param mixed $composite the image to be composed onto $dest
 * @param int $x place $composite at this x co-ordinate on $dest
 * @param int $y place $composite at this y co-ordinate on $dest
 * @return bool TRUE on success, FALSE on failure
 */
function image_composite($dest, $composite, $x, $y) {
	switch (gettype($dest)) {
		case "object":
			return $dest->compositeImage($composite, imagick::COMPOSITE_DEFAULT, $x, $y);
		case "resource":
			$width = imagesx($composite);
			$height = imagesy($composite);
			return imagecopyresampled($dest, $composite, $x, $y, 0, 0, $width, $height, $width, $height);
	}
	return false;
}
?>
