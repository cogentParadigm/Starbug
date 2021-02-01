<?php
namespace Starbug\Files;

use League\Flysystem\MountManager;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\ApiController;
use Starbug\Core\ImagesInterface;

class ApiFilesController extends ApiController {
  public $model = "files";
  public function __construct(SessionHandlerInterface $session, ImagesInterface $images, MountManager $filesystems) {
    $this->session = $session;
    $this->images = $images;
    $this->filesystems = $filesystems;
  }
  public function admin() {
    return $this->api->render("AdminFiles");
  }
  public function select() {
    return $this->api->render("FilesSelect");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
  public function filterRow($collection, $file) {
    if (in_array($file["mime_type"], ["image/gif", "image/jpeg", "image/png"])) {
      $file["thumbnail"] = (string) $this->images->thumb($file["location"]."://".$file['id']."_".$file['filename'], ["w" => 100, "h" => 100, "a" => 1]);
    }
    $file["url"] = (string) $this->filesystems->getFilesystem($file["location"])->getUrl($file["id"]."_".$file["filename"]);
    return $file;
  }
}
