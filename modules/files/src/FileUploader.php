<?php
namespace Starbug\Files;

use Exception;
use League\Flysystem\MountManager;
use Mimey\MimeTypes;
use Psr\Http\Message\UploadedFileInterface;

class FileUploader implements FileUploaderInterface {
  protected $extensions = [
    "gpg" => "application/pgp"
  ];
  protected $allowed = [
    "text/csv" => ["text/plain"],
    "application/pgp" => ["application/octet-stream"]
  ];
  public function __construct(
    protected FilesRepository $repository,
    protected MountManager $filesystems,
    protected ImagesInterface $images,
    protected MimeTypes $mimeTypes
  ) {
  }
  public function upload($record, UploadedFileInterface $file): ?array {
    $filename = $file->getClientFilename();
    $tmpName = $file->getStream()->getMetadata("uri");
    $error = $file->getError();
    if (empty($filename)) {
      throw new Exception("Filename empty");
    }
    if ($error > 0) {
      throw new Exception("Uploaded file error: ".$error);
    }
    $record["filename"] = preg_replace("/[^a-zA-Z0-9\-_\\.]/", "", str_replace(" ", "_", $filename));
    $record["mime_type"] = $this->getMimeFromExtension($record["filename"]);
    if (!$this->mimeIsValid($tmpName, $record["mime_type"])) {
      throw new Exception("Incorrect file type. Expected {$record["mime_type"]}, but detected {$this->getMimeFromFile($tmpName)}.");
    }
    $record["size"] = filesize($tmpName);
    if ($record = $this->repository->save($record)) {
      $fs = $this->filesystems->getFilesystem($record["location"]);
      $writePath = $record["id"]."_".$record["filename"];
      if ($fs->has($writePath)) {
        $fs->delete($writePath);
      }
      $stream = fopen($tmpName, "r+");
      try {
        $fs->writeStream($writePath, $stream);
      } finally {
        if (is_resource($stream)) {
          fclose($stream);
        }
      }
      if (in_array($record['mime_type'], ["image/gif", "image/jpeg", "image/png"])) {
        $this->images->thumb($record["location"]."://".$record["id"]."_".$record['filename'], ["w" => 100, "h" => 100]);
      }
      return $record;
    }
    return null;
  }
  protected function mimeIsValid($path, $type) {
    $detected = $this->getMimeFromFile($path);
    if ($detected == $type) {
      return true;
    }
    if (isset($this->allowed[$type]) && in_array($detected, $this->allowed[$type])) {
      return true;
    }
    return false;
  }
  protected function getMimeFromFile($path) {
    $typeinfo = finfo_open(FILEINFO_MIME_TYPE);
    return finfo_file($typeinfo, $path);
  }
  protected function getMimeFromExtension($path) {
    $ext = $this->getExtension($path);
    if (isset($this->extensions[$ext])) {
      return $this->extensions[$ext];
    }
    return $this->mimeTypes->getMimeType($ext);
  }
  protected function getExtension($path) {
    return pathinfo($path, PATHINFO_EXTENSION);
  }
}
