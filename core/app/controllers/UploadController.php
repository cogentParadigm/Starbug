<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;
use Exception;

class UploadController extends Controller {
  function __construct(ModelFactoryInterface $models, MountManager $filesystems, ImagesInterface $images, IdentityInterface $user) {
    $this->models = $models;
    $this->filesystems = $filesystems;
    $this->images = $images;
    $this->user = $user;
  }
  function default_action() {
    $_post = array();
    $htmldata = array();
    $record = array("filename" => "", "mime_type" => "", "caption" => "uploaded file", "category" => $this->request->getPost('category'));
    $files = array();

    foreach ($this->request->getFiles()->get('uploadedfiles') as $key => $arr) {
      foreach ($arr as $idx => $value) {
        $files[$idx][$key] = $value;
      }
    }

    foreach ($files as $file) {
      if (!empty($file['category'])) $record['category'] = $file['category'];
      $moved = $this->models->get("files")->upload($record, $file);
      if ($moved) {
        $id = $this->models->get('files')->insert_id;
        $_post['id'] = $id;
        $_post['original_name'] = str_replace(" ", "_", $file['name']);
        $_post['name'] = $id."_".$_post['original_name'];
        $_post['url'] = $this->filesystems->getFilesystem("default")->getURL($_post['name']);
        $_post['mime_type'] = $this->models->get("files")->get_mime($file['tmp_name']);
        try{
          list($width, $height) = getimagesize($file['tmp_name']);
          $image = true;
          $_post['thumbnail'] = $this->images->thumb("default://".$_post['name'], ["w" => 100, "w" => 100, "a" => 1]);
        } catch(Exception $e) {
          $width=0;
          $height=0;
          $image = false;
        }
        $_post['width'] = $width;
        $_post['height'] = $height;
        $_post['type'] = end(explode(".", $_post['name']));
        $_post['size'] = filesize($file['tmp_name']);
        $_post['image'] = $image;
        $_post['owner'] = $this->user->userinfo("id");
        $htmldata[] = $_post;
      } else {
        $htmldata[] = array("ERROR" => "File could not be moved: ".$file['name']);
      }
    }
    $this->response->content = $htmldata;
    $this->response->setTemplate("json.json");
  }
}
