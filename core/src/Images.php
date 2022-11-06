<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;

class Images implements ImagesInterface {
  protected $filesystems;
  protected $base_directory;
  public function __construct(MountManager $filesystems, $base_directory) {
    $this->filesystems = $filesystems;
    $this->base_directory = $base_directory;
  }
  public function info($file) {
    if (!is_file($file)) {
      return false;
    }

    $details = false;
    $data = @getimagesize($file);
    $file_size = @filesize($file);

    if (isset($data) && is_array($data)) {
      $extensions = ['1' => 'gif', '2' => 'jpg', '3' => 'png'];
      $extension = array_key_exists($data[2], $extensions) ?  $extensions[$data[2]] : '';
      $details = ['width'=> $data[0],
                      'height' => $data[1],
                      'extension' => $extension,
                      'file_size' => $file_size,
                      'mime_type' => $data['mime']];
    }

    return $details;
  }
  public function create($width, $height) {
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
  public function open($path) {
    if (class_exists("Imagick")) {
      $image = new Imagick();
      $image->readImage($path);
      return $image;
    } else {
      if ($format == "auto") {
        $format = end(explode(".", $path));
      }
      $format = str_replace('jpg', 'jpeg', $format);
      $open_func = 'imageCreateFrom'. $format;
      if (!function_exists($open_func)) {
        return false;
      }
      return $open_func($path);
    }
  }
  public function save($image, $path, $format = "auto") {
    if ($format == "auto") {
      $format = end(explode(".", $path));
    }
    $format = str_replace('jpg', 'jpeg', $format);
    switch (gettype($image)) {
      case "object":
        $image->setImageFormat($format);
        return $image->writeImage($path);
      case "resource":
        $close_func = 'image'. $format;
        if (!function_exists($close_func)) {
          return false;
        }
        if ($format == 'jpeg') {
          return $close_func($image, $path, 100);
        } else {
          return $close_func($image, $path);
        }
    }
  }
  public function thumb($url, $dimensions = [], $absolute = false) {
    list($filesystem, $filename) = explode("://", $url);
    $dimensions = array_merge(['w' => 0, 'h' => 0, 'a' => false, 'f' => false], $dimensions);
    $dir = $dimensions['w']."x".$dimensions['h']."a".$dimensions['a'];
    $target = $dir."/".$filename;
    if (!$this->filesystems->has($filesystem."://thumbnails/".$target) || $dimensions['f']) {
      $this->filesystems->put("tmp://images/".$filename, $this->filesystems->read($url));
      $thumb = new \PHPThumb\GD($this->base_directory."/var/tmp/images/".$filename);
      if (function_exists("exif_read_data")) {
        $exif = @exif_read_data($this->base_directory."/var/tmp/images/".$filename);
        if (!empty($exif['Orientation'])) {
          if ($exif['Orientation'] === 3) {
            $thumb->rotateImageNDegrees(180);
          } elseif ($exif['Orientation'] === 6) {
            $thumb->rotateImageNDegrees(-90);
          } elseif ($exif['Orientation'] === 8) {
            $thumb->rotateImageNDegrees(90);
          }
        }
      }
      if ($dimensions['a']) {
        $thumb->adaptiveResize($dimensions['w'], $dimensions['h']);
      } else {
        $thumb->resize($dimensions['w'], $dimensions['h']);
      }
      if (!$this->filesystems->has("tmp://thumbnails/".$dir)) {
        $this->filesystems->createDir("tmp://thumbnails/".$dir);
      }
      $thumb->save($this->base_directory."/var/tmp/thumbnails/".$target);
      $this->filesystems->move("tmp://thumbnails/".$target, $filesystem."://thumbnails/".$target);
    }
    return $this->filesystems->getFilesystem($filesystem)->getUrl("thumbnails/".$target, $absolute);
  }
  public function composite($dest, $composite, $x, $y) {
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
