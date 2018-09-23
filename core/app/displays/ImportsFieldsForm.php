<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;

class ImportsFieldsForm extends FormDisplay {
  public $source_keys = [];
  public $source_values = [];
  public $model = "imports_fields";
  public $cancel_url = "admin/imports_fields";
  public function setFilesystems(MountManager $filesystems) {
    $this->filesystems = $filesystems;
  }
  public function buildDisplay($options) {
    $data = $this->request->getPost();
    if ($this->success("create") && empty($data['imports_fields']['id'])) {
      $this->request->setPost('imports_fields', 'id', $this->models->get($this->model)->insert_id);
    }
    $this->parseSource($options['source']);
    $this->add(["source", "input_type" => "select", "options" => $this->source_values]);
    $model = $this->models->get($options['model']);
    $dest_ops = ["destination", "input_type" => "select"];
    if (method_exists($model, "import_fields")) {
      $dest_ops = array_merge($dest_ops, $model->import_fields($options));
    } else {
      $dest_ops['options'] = array_keys($this->models->get($options['model'])->column_info());
    }
    $this->add($dest_ops);
    $this->add(["update_key", "input_type" => "checkbox", "label" => "Use this field as a key to update records"]);
  }
  protected function parseSource($id) {
    $file = $this->models->get("files")->load($id);
    $head = [];
    if (false !== ($handle = $this->filesystems->readStream($file["location"]."://".$file["id"]."_".$file["filename"]))) {
      $head = fgetcsv($handle);
    }
    $this->source_keys = array_keys($head);
    $this->source_values = array_values($head);
  }
}
