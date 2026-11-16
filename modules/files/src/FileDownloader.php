<?php
namespace Starbug\Files;

use Starbug\Db\DatabaseInterface;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\MountManager;
use Psr\Http\Message\ResponseFactoryInterface;

class FileDownloader {
  public function __construct(
    protected DatabaseInterface $db,
    protected MountManager $filesystems,
    protected ResponseFactoryInterface $responses
  ) {
    $this->db = $db;
    $this->filesystems = $filesystems;
    $this->responses = $responses;
  }
  public function download($file) {
    if (!is_array($file)) {
      $file = $this->getFile($file);
    }
    $filesystem = $this->filesystems->getFilesystem($file["location"]);
    $path = $file["id"]."_".$file["filename"];
    return $this->responses->createResponse()
      ->withHeader("content-type", "application/octet-stream")
      ->withHeader("content-description", "File Transfer")
      ->withHeader("content-disposition", "attachment; filename=".str_replace(",", "", $file["filename"]))
      ->withHeader("content-length", $filesystem->fileSize($path))
      ->withBody(Utils::streamFor($filesystem->readStream($path)));
  }
  public function getFile($id) {
    return $this->db->get("files", $id);
  }
}
