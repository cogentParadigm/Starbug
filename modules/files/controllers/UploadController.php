<?php
namespace Starbug\Files;

use Exception;
use GuzzleHttp\Psr7\Utils;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;
use League\Flysystem\MountManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ImagesInterface;

class UploadController extends Controller {
  protected $models;
  protected $db;
  protected $filesystems;
  protected $images;
  protected $session;
  protected $response;
  public function __construct(
    ModelFactoryInterface $models,
    DatabaseInterface $db,
    MountManager $filesystems,
    ImagesInterface $images,
    SessionHandlerInterface $session,
    ResponseFactoryInterface $response
  ) {
    $this->models = $models;
    $this->db = $db;
    $this->filesystems = $filesystems;
    $this->images = $images;
    $this->session = $session;
    $this->response = $response;
  }
  public function __invoke(ServerRequestInterface $request) {
    $htmldata = [];
    $files = $request->getUploadedFiles()["uploadedfiles"];

    $bodyParams = $request->getParsedBody();

    foreach ($files as $file) {
      $_post = [];
      $record = [
        "filename" => "",
        "mime_type" => "",
        "caption" => "uploaded file",
        "category" => $bodyParams["category"] ?? "",
        "location" => $bodyParams["location"] ?? "default"
      ];
      try {
        list($width, $height) = getimagesize($file->getStream()->getMetadata("uri"));
      } catch (Exception $e) {
        $width = 0;
        $height = 0;
      }
      $moved = $this->models->get("files")->upload($record, $file);
      if ($moved) {
        $id = $this->db->getInsertId("files");
        $record = $this->models->get("files")->load($id);
        $_post['id'] = $id;
        $_post['filename'] = $record["filename"];
        $_post['name'] = $id."_".$record["filename"];
        $_post['url'] = (string) $this->filesystems->getFilesystem($record["location"])->getUrl($_post['name']);
        $_post['mime_type'] = $record["mime_type"];
        $image = in_array($record["mime_type"], ["image/gif", "image/jpeg", "image/png"]);
        if ($image) {
          $_post['thumbnail'] = $this->images->thumb($record["location"]."://".$_post['name'], ["w" => 100, "w" => 100, "a" => 1]);
        }
        $_post['width'] = $width;
        $_post['height'] = $height;
        $_post['type'] = pathinfo($_post['name'], PATHINFO_EXTENSION);
        $_post['size'] = $this->filesystems->getFilesystem($record["location"])->getSize($_post['name']);
        $_post['image'] = $image;
        $_post['owner'] = $this->session->getUserId();
        $htmldata[] = $_post;
      } else {
        $htmldata[] = ["ERROR" => "File could not be moved: ".$file['name']];
      }
    }
    return $this->response->createResponse()
      ->withHeader("Content-Type", "application/json")
      ->withBody(Utils::streamFor(json_encode($htmldata)));
  }
}
