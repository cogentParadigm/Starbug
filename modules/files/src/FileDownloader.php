<?php
namespace Starbug\Files;

use Starbug\Core\DatabaseInterface;
use Starbug\Http\ResponseInterface;
use League\Flysystem\MountManager;

class FileDownloader {
  public function __construct(DatabaseInterface $db, MountManager $filesystems, ResponseInterface $response) {
    $this->db = $db;
    $this->filesystems = $filesystems;
    $this->response = $response;
  }
  public function download($file) {
    if (!is_array($file)) $file = $this->getFile($file);
    $filesystem = $this->filesystems->getFilesystem($file["location"]);
    $path = $file["id"]."_".$file["filename"];
    $this->response->setContentType("application/octet-stream");
    $this->response->setHeader("Content-Description", "File Transfer");
    $this->response->setHeader("Content-Disposition", "attachment; filename=".str_replace(",", "", $file["filename"]));
    $this->response->setHeader("Content-Length", $filesystem->getSize($path));
    $this->response->setCallable(function () use (&$filesystem, $path) {
      $stream = $filesystem->readStream($path);
      if (is_object($stream)) {
        while (!$stream->eof()) {
          echo $stream->read(1024);
        }
      } elseif (is_resource($stream)) {
        fpassthru($stream);
        if (is_resource($stream)) {
          fclose($stream);
        }
      }
    });
  }
  public function getFile($id) {
    return $this->db->get("files", $id);
  }
}
