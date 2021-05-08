<?php
namespace Starbug\Core;

use Psr\Http\Message\ServerRequestInterface;

class StoreUploadHook extends QueryHook {
  protected $db;
  protected $request;
  protected $files;
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, ServerRequestInterface $request) {
    $this->db = $db;
    $this->request = $request;
    $this->files = $models->get("files");
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
      $this->files->upload($record, $file);
      if (!$this->files->errors()) {
        $value = (empty($record['id'])) ? $this->db->getInsertId("files") : $record['id'];
      } else {
        foreach ($this->files->errors("filename", true) as $type => $message) {
          $this->db->error($message, $column, $query->model);
        }
      }
    }
    return $value;
  }
}
