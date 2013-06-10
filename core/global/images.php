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
	$format = str_replace('jpg', 'jpeg', $format);
	switch (gettype($image)) {
		case "object":
			$image->setImageFormat($format);
			return $image->writeImage($path);
		case "resource":
			$close_func = 'image'. $format;
			if (!function_exists($close_func)) return FALSE;
			if ($format == 'jpeg') return $close_func($image, $path, 100);
			else return $close_func($image, $path);
	}
}
/**
 * Get a thumbnail image URL
 * @ingroup images
 * @param string $current_file the path to the original
 * @param star $dimensions the desired width or height (or both to constrain) in pixels
 * @return string an absolute URL to the thumbnail
 */
function image_thumb($current_file, $dimensions, $options=array()) {
	import("thumb");
	$dimensions = array_merge(array('w' => 0, 'h' => 0, 'a' => false), star($dimensions));
	$options = star($options);
	$filename = basename($current_file);
	$dir = "var/public/thumbnails/".$dimensions['w']."x".$dimensions['h']."a".$dimensions['a'];
	$target = $dir."/".$filename;
	if (!file_exists(BASE_DIR."/".$target) || $dimensions['f']) {
		if (!file_exists(BASE_DIR."/".$dir)) mkdir(BASE_DIR."/".$dir);
		$thumb = PhpThumbFactory::create(BASE_DIR."/".$current_file);
		if ($dimensions['a']) $thumb->adaptiveResize($dimensions['w'], $dimensions['h']);
		else $thumb->resize($dimensions['w'], $dimensions['h']);
		$thumb->save(BASE_DIR."/".$target);
	}
	return uri($target);
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
