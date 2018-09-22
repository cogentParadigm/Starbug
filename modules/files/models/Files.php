<?php
namespace Starbug\Files;

use Starbug\Core\FilesModel;

class Files extends FilesModel {

  public function create($record) {
    $this->upload($record, $_FILES['file']);
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

  public function upload($record, $file, $remote = false) {
    if (!empty($file['name'])) {
      if ($file["error"] > 0) $this->error($file["error"], "filename");
      $record['filename'] = str_replace(" ", "_", $file['name']);
      $record['mime_type'] = $this->get_mime($file['tmp_name']);
      $record['size'] = filesize($file['tmp_name']);
      if (empty($record['category'])) $record['category'] = "files_category uncategorized";
      $this->store($record);
      if ((!$this->errors()) && (!empty($record['filename']))) {
        $id = (empty($record['id'])) ? $this->insert_id : $record['id'];
        $stream = fopen($file["tmp_name"], "r+");
        $success = $this->filesystem->writeStream($id."_".$record["filename"], $stream);
        if (is_resource($stream)) fclose($stream);
        if ($success) {
          if (reset(explode("/", $record['mime_type'])) == "image") $this->images->thumb("default://".$id."_".$record['filename'], ["w" => 100, "h" => 100, "a" => 1]);
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

  public function get_mime($file_path) {
    $output = exec("file --mime-type -b {$file_path}");
    return $output;
  }
}
