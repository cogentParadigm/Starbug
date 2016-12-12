<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/Images.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
class Images implements ImagesInterface {
	protected $url;
	protected $base_directory;
	public function __construct(URLInterface $url, $base_directory) {
		$this->url = $url;
		$this->base_directory = $base_directory;
	}
	function info($file) {
		if (!is_file($file)) return FALSE;

		$details = FALSE;
		$data = @getimagesize($file);
		$file_size = @filesize($file);

		if (isset($data) && is_array($data)) {
			$extensions = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
			$extension = array_key_exists($data[2], $extensions) ?  $extensions[$data[2]] : '';
			$details = array('width'=> $data[0],
											'height' 		=> $data[1],
											'extension' => $extension,
											'file_size' => $file_size,
											'mime_type' => $data['mime']);
		}

		return $details;
	}
	function create($width, $height) {
		if (class_exists("Imagick")) {
			$image = new Imagick();
			$image->newImage($width, $height, "none");
			return $image;
		} else {
			$image = imagecreatetruecolor($width, $height);
			// apply PNG 24-bit transparency to background
			$transparency = imagecolorallocatealpha($image, 0, 0, 0, 127);
			imagealphablending($image, false);
			imagefilledrectangle($image, 0, 0, $width, $height, $transparency);
			imagealphablending($image, true);
			imagesavealpha($image, true);
			return $image;
		}
	}
	function open($path) {
		if (class_exists("Imagick")) {
			$image = new Imagick();
			$image->readImage($path);
			return $image;
		} else {
			if ($format == "auto") $format = end(explode(".", $path));
			$format = str_replace('jpg', 'jpeg', $format);
			$open_func = 'imageCreateFrom'. $format;
			if (!function_exists($open_func)) return false;
			return $open_func($path);
		}
	}
	function save($image, $path, $format = "auto") {
		if ($format == "auto") $format = end(explode(".", $path));
		$format = str_replace('jpg', 'jpeg', $format);
		switch (gettype($image)) {
			case "object":
				$image->setImageFormat($format);
				return $image->writeImage($path);
			case "resource":
				$close_func = 'image'. $format;
				if (!function_exists($close_func)) return false;
				if ($format == 'jpeg') return $close_func($image, $path, 100);
				else return $close_func($image, $path);
		}
	}
	function thumb($current_file, $dimensions = array(), $absolute = false) {
		$dimensions = array_merge(array('w' => 0, 'h' => 0, 'a' => false), $dimensions);
		$filename = basename($current_file);
		$dir = "var/public/thumbnails/".$dimensions['w']."x".$dimensions['h']."a".$dimensions['a'];
		$target = $dir."/".$filename;
		if (!file_exists($this->base_directory."/".$target) || $dimensions['f']) {
			if (!file_exists($this->base_directory."/".$dir)) mkdir($this->base_directory."/".$dir);
			$thumb = new \PHPThumb\GD($this->base_directory."/".$current_file);
			if (function_exists("exif_read_data")) {
				$exif = @exif_read_data($this->base_directory."/".$current_file);
				if (!empty($exif['Orientation'])) {
					if ($exif['Orientation'] === 3) {
						$thumb->rotateImageNDegrees(180);
					} else if ($exif['Orientation'] === 6) {
						$thumb->rotateImageNDegrees(-90);
					} else if ($exif['Orientation'] === 8) {
						$thumb->rotateImageNDegrees(90);
					}
				}
			}
			if ($dimensions['a']) $thumb->adaptiveResize($dimensions['w'], $dimensions['h']);
			else $thumb->resize($dimensions['w'], $dimensions['h']);
			$thumb->save($this->base_directory."/".$target);
		}
		return $this->url->build($target, $absolute);
	}
	function composite($dest, $composite, $x, $y) {
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
}
?>
