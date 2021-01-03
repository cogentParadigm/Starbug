<?php
namespace Starbug\Core;

use Psr\Http\Message\ServerRequestInterface;

class StoreUploadHook extends QueryHook {
  protected $uploaded = false;
  protected $request;
  protected $models;
  protected $files;
  public function __construct(ModelFactoryInterface $models, ServerRequestInterface $request) {
    $this->request = $request;
    $this->models = $models;
    $this->files = $models->get("files");
  }
  public function emptyValidate($query, $column, $argument) {
    $files = $this->request->getUploadedFiles();
    if (!empty($files[$column]["name"])) $query->set($column, $this->store($query, $column, "", $column, $argument));
  }
  public function store($query, $key, $value, $column, $argument) {
    if ($this->uploaded) return $value;
    else $this->uploaded = true;
    $files = $this->request->getUploadedFiles();
    $record = ["filename" => "", "mime_type" => "", "caption" => "$name $field"];
    if (!empty($value) && is_numeric($value)) $records['id'] = $value;
    $file = $files[$column];
    if (!empty($file['name'])) {
      $this->files->upload($record, $file);
      if (!$this->files->errors()) {
        $value = (empty($record['id'])) ? $this->files->insert_id : $record['id'];
      } else {
        foreach ($this->files->errors("filename", true) as $type => $message) $this->models->get($query->model)->error($message, $column);
      }
    }
    return $value;
  }
}
