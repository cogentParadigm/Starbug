<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;
use Starbug\Db\Schema\SchemerInterface;

class ImportsFieldsForm extends FormDisplay {
  public $source_keys = [];
  public $source_values = [];
  public $model = "imports_fields";
  protected $layoutDisplay = "ModalFormLayout";
  public function setFilesystems(MountManager $filesystems) {
    $this->filesystems = $filesystems;
  }
  public function setModels(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function setSchema(SchemerInterface $schemer) {
    $this->schema = $schemer->getSchema();
  }
  public function setDatabase(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function buildDisplay($options) {
    $data = $this->getPost();
    if ($this->success("create") && empty($data["id"])) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    $this->parseSource($options['source']);
    $this->add(["source", "input_type" => "select", "options" => $this->source_values]);
    $model = $this->models->get($options['model']);
    $dest_ops = ["destination", "input_type" => "select"];
    if (method_exists($model, "importFields")) {
      $dest_ops = array_merge($dest_ops, $model->importFields($options));
    } else {
      $dest_ops['options'] = array_keys($this->schema->getColumn($options['model']));
    }
    $this->add($dest_ops);
    $this->add(["update_key", "input_type" => "checkbox", "value" => "1", "label" => "Use this field as a key to update records"]);
    $this->actions->attributes["class"] = "modal-footer flex flex-row-reverse";
    $this->actions->add(["cancel", "class" => "cancel mr2 btn-default"]);
  }
  protected function parseSource($id) {
    $file = $this->db->get("files", $id);
    $head = [];
    if (false !== ($handle = $this->filesystems->readStream($file["location"]."://".$file["id"]."_".$file["filename"]))) {
      $head = fgetcsv($handle);
    }
    $this->source_keys = array_keys($head);
    $this->source_values = array_values($head);
  }
}
