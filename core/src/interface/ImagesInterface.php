<?php
namespace Starbug\Core;

interface ImagesInterface {
  /**
   * Get details about an image.
   *
   * @param string $file the file path
   *
   * @return array containing information about the image
   *      'width': image's width in pixels
   *      'height': image's height in pixels
   *      'extension': commonly used extension for the image
   *      'mime_type': image's MIME type ('image/jpeg', 'image/gif', etc.)
   *      'file_size': image's physical size (in bytes)
   */
  public function info($file);
  /**
   * Create a new image resource
   *
   * @param int $width the width of the image to create
   * @param int $height the height of the image to create
   *
   * @return mixed a new image resource
   */
  public function create($width, $height);
  /**
   * Open an image
   *
   * @param string $path the file path
   */
  public function open($path);
  /**
   * Save an image
   *
   * @param mixed $image the image object or resource
   * @param string $path the file path
   */
  public function save($image, $path, $format = "auto");
  /**
   * Get a thumbnail image URL
   *
   * @param string $current_file the path to the original
   * @param array $dimensions the desired width or height (or both to constrain) in pixels
   *
   * @return string an absolute URL to the thumbnail
   */
  public function thumb($current_file, $dimensions, $flags = "");
  /**
   * Composite one image onto another
   *
   * @param mixed $dest the image to copose onto
   * @param mixed $composite the image to be composed onto $dest
   * @param int $x place $composite at this x co-ordinate on $dest
   * @param int $y place $composite at this y co-ordinate on $dest
   *
   * @return bool TRUE on success, FALSE on failure
   */
  public function composite($dest, $composite, $x, $y);
}
