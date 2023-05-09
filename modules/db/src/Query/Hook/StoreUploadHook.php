<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Files\FileUploader;

class StoreUploadHook extends ExecutorHook {
  protected $db;
  protected $request;
  protected $files;
  public function __construct(DatabaseInterface $db, FileUploader $uploader, ServerRequestInterface $request) {
    $this->db = $db;
    $this->uploader = $uploader;
    $this->request = $request;
  }
  public function emptyValidate($query, $column, $argument) {
    $files = $this->request->getUploadedFiles();
    if (!empty($files[$column]["name"])) {
      $query->set($column, $this->store($query, $column, "", $column, $argument));
    }
  }
  public function store($query, $key, $value, $column, $argument) {
    if ($query->getMeta("{$column}.upload.uploaded", false)) {
      return $value;
    } else {
      $query->setMeta("{$column}.upload.uploaded", true);
    }
    $files = $this->request->getUploadedFiles();
    $record = ["filename" => "", "mime_type" => "", "caption" => "{$query->model} {$column}"];
    if (!empty($value) && is_numeric($value)) {
      $record['id'] = $value;
    }
    $file = $files[$column];
    if (!empty($file['name'])) {
      $record = $this->uploader->upload($record, $file);
      if (!$this->db->errors("files")) {
        $value = (empty($record['id'])) ? $this->db->getInsertId("files") : $record['id'];
      } else {
        foreach ($this->db->errors("files.filename", true) as $type => $message) {
          $this->db->error($message, $column, $query->model);
        }
      }
    }
    return $value;
  }
}
