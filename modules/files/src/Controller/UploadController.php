<?php
namespace Starbug\Files\Controller;

use Starbug\Db\DatabaseInterface;
use Exception;
use GuzzleHttp\Psr7\Utils;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;
use League\Flysystem\MountManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\ImagesInterface;
use Starbug\Files\FileUploaderInterface;

class UploadController extends Controller {
  protected $db;
  protected $filesystems;
  protected $images;
  protected $session;
  protected $response;
  public function __construct(
    DatabaseInterface $db,
    FileUploaderInterface $uploader,
    MountManager $filesystems,
    ImagesInterface $images,
    SessionHandlerInterface $session,
    ResponseFactoryInterface $response
  ) {
    $this->db = $db;
    $this->uploader = $uploader;
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
      try {
        if ($record = $this->uploader->upload($record, $file)) {
          $_post['id'] = $record["id"];
          $_post['filename'] = $record["filename"];
          $_post['name'] = $record["id"]."_".$record["filename"];
          $_post['url'] = (string) $this->filesystems->getFilesystem($record["location"])->getUrl($_post["name"]);
          $_post['mime_type'] = $record["mime_type"];
          $image = in_array($record["mime_type"], ["image/gif", "image/jpeg", "image/png"]);
          if ($image) {
            $_post['thumbnail'] = (string) $this->images->thumb($record["location"]."://".$_post['name'], ["w" => 100, "w" => 100]);
          }
          $_post['width'] = $width;
          $_post['height'] = $height;
          $_post['type'] = pathinfo($_post['name'], PATHINFO_EXTENSION);
          $_post['size'] = $this->filesystems->getFilesystem($record["location"])->getSize($_post['name']);
          $_post['image'] = $image;
          $_post['owner'] = $this->session->getUserId();
          $htmldata[] = $_post;
        } else {
          $htmldata[] = ["ERROR" => "File could not be moved: ".$file->getClientFilename()];
        }
      } catch (Exception $e) {
        $htmldata[] = ["ERROR" => "File could not be moved: ".$file->getClientFilename()];
      }
    }
    return $this->response->createResponse()
      ->withHeader("Content-Type", "application/json")
      ->withBody(Utils::streamFor(json_encode($htmldata)));
  }
}
