<?php
namespace Starbug\Files;

use Starbug\Db\DatabaseInterface;
use GuzzleHttp\Psr7\Utils;
use Starbug\Http\ResponseBuilderInterface;
use League\Flysystem\MountManager;

class FileDownloader {
  public function __construct(
    protected DatabaseInterface $db,
    protected MountManager $filesystems,
    protected ResponseBuilderInterface $response
  ) {
    $this->db = $db;
    $this->filesystems = $filesystems;
    $this->response = $response;
  }
  public function download($file) {
    if (!is_array($file)) {
      $file = $this->getFile($file);
    }
    $filesystem = $this->filesystems->getFilesystem($file["location"]);
    $path = $file["id"]."_".$file["filename"];
    $this->response
      ->withHeader("content-type", "application/octet-stream")
      ->withHeader("content-description", "File Transfer")
      ->withHeader("content-disposition", "attachment; filename=".str_replace(",", "", $file["filename"]))
      ->withHeader("content-length", $filesystem->getSize($path))
      ->withBody(Utils::streamFor($filesystem->readStream($path)));
  }
  public function getFile($id) {
    return $this->db->get("files", $id);
  }
}
