<?php
namespace Starbug\Files;

use Psr\Http\Message\UploadedFileInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;
use League\Flysystem\MountManager;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ImagesInterface;
use Starbug\Core\ModelFactoryInterface;

class Files extends Table {

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, SchemerInterface $schemer, MountManager $filesystems, ImagesInterface $images, ServerRequestInterface $request) {
    parent::__construct($db, $models, $schemer);
    $this->filesystems = $filesystems;
    $this->images = $images;
    $this->request = $request;
  }

  public function create($record) {
    $file = $this->request->getUploadedFiles()["file"];
    $this->upload($record, $file);
  }

  public function update($record) {
    $original = $this->get($record['id']);
    $this->store($record);
    if (!$this->errors()) {
      if ($record['filename'] != $original['filename']) {
        // rename file
        if ($this->filesystem->rename($record['id']."_".$original['filename'], $record['id']."_".$record['filename'])) {
          $this->store(["id" => $record['id'], "filename" => $original['filename']]);
        }
      }
    }
  }

  public function upload($record, UploadedFileInterface $file, $remote = false) {
    $filename = $file->getClientFilename();
    $tmpName = $file->getStream()->getMetadata("uri");
    $error = $file->getError();
    if (!empty($filename)) {
      if ($error > 0) {
        $this->error("Error ".$error, "filename");
      }
      $record['filename'] = str_replace(" ", "_", $filename);
      $record['mime_type'] = $this->getMime($tmpName);
      $record['size'] = filesize($tmpName);
      if (empty($record['category'])) {
        $record['category'] = "files_category uncategorized";
      }
      if (empty($record["location"])) {
        $record["location"] = "default";
      }
      $this->store($record);
      if ((!$this->errors()) && (!empty($record['filename']))) {
        $id = (empty($record['id'])) ? $this->db->getInsertId("files") : $record['id'];
        $stream = fopen($tmpName, "r+");
        $success = $this->filesystems->getFilesystem($record["location"])->writeStream($id."_".$record["filename"], $stream);
        if (is_resource($stream)) {
          fclose($stream);
        }
        if ($success) {
          if (in_array($record['mime_type'], ["image/gif", "image/jpeg", "image/png"])) {
            $this->images->thumb($record["location"]."://".$id."_".$record['filename'], ["w" => 100, "h" => 100, "a" => 1]);
          }
          return true;
        } else {
          return false;
        }
      }
    } else {
      $record['filename'] = "";
      $this->store($record);
    }
  }


  public function prepare() {
    $this->create(["caption" => "Pre Uploaded File"]);
  }

  public function delete($file) {
    $this->remove(["id" => $file['id']]);
    return [];
  }

  public function getMime($file_path) {
    $output = exec("file --mime-type -b {$file_path}");
    return $output;
  }
}
