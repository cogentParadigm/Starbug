<?php
namespace Starbug\Files\Controller;

use League\Flysystem\MountManager;
use Starbug\Core\ApiRequest;
use Starbug\Core\Controller\CollectionController;
use Starbug\Core\ImagesInterface;

class ApiFilesController extends CollectionController {
  protected $model = "files";
  public function __construct(ApiRequest $api, ImagesInterface $images, MountManager $filesystems) {
    parent::__construct($api);
    $this->images = $images;
    $this->filesystems = $filesystems;
  }
  public function filterRow($collection, $file) {
    if (in_array($file["mime_type"], ["image/gif", "image/jpeg", "image/png"])) {
      $file["thumbnail"] = (string) $this->images->thumb($file["location"]."://".$file['id']."_".$file['filename'], ["w" => 100, "h" => 100, "a" => 1]);
    }
    $file["url"] = (string) $this->filesystems->getFilesystem($file["location"])->getUrl($file["id"]."_".$file["filename"]);
    return $file;
  }
}
