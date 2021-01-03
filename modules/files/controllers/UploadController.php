<?php
namespace Starbug\Files;

use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;
use League\Flysystem\MountManager;
use Starbug\Core\ImagesInterface;
use Starbug\Core\IdentityInterface;
use Exception;

class UploadController extends Controller {
  public function __construct(ModelFactoryInterface $models, MountManager $filesystems, ImagesInterface $images, IdentityInterface $user) {
    $this->models = $models;
    $this->filesystems = $filesystems;
    $this->images = $images;
    $this->user = $user;
  }
  public function defaultAction() {
    $htmldata = [];
    $files = [];

    $bodyParams = $this->request->getParsedBody();

    foreach ($this->request->getUploadedFiles()["uploadedfiles"] as $key => $arr) {
      foreach ($arr as $idx => $value) {
        $files[$idx][$key] = $value;
      }
    }

    foreach ($files as $file) {
      $_post = [];
      $record = [
        "filename" => "",
        "mime_type" => "",
        "caption" => "uploaded file",
        "category" => $bodyParams["category"],
        "location" => $bodyParams["location"] ?? "default"
      ];
      try {
        list($width, $height) = getimagesize($file['tmp_name']);
      } catch (Exception $e) {
        $width = 0;
        $height = 0;
      }
      if (!empty($file['category'])) $record['category'] = $file['category'];
      $moved = $this->models->get("files")->upload($record, $file);
      if ($moved) {
        $id = $this->models->get('files')->insert_id;
        $record = $this->models->get("files")->load($id);
        $_post['id'] = $id;
        $_post['filename'] = $record["filename"];
        $_post['name'] = $id."_".$record["filename"];
        $_post['url'] = $this->filesystems->getFilesystem($record["location"])->getUrl($_post['name']);
        $_post['mime_type'] = $record["mime_type"];
        $image = in_array($record["mime_type"], ["image/gif", "image/jpeg", "image/png"]);
        if ($image) {
          $_post['thumbnail'] = $this->images->thumb($record["location"]."://".$_post['name'], ["w" => 100, "w" => 100, "a" => 1]);
        }
        $_post['width'] = $width;
        $_post['height'] = $height;
        $_post['type'] = end(explode(".", $_post['name']));
        $_post['size'] = $this->filesystems->getFilesystem($record["location"])->getSize($_post['name']);
        $_post['image'] = $image;
        $_post['owner'] = $this->user->userinfo("id");
        $htmldata[] = $_post;
      } else {
        $htmldata[] = ["ERROR" => "File could not be moved: ".$file['name']];
      }
    }
    $this->response->setTemplate("json.json");
    $this->response->setContent($htmldata);
  }
}
