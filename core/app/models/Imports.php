<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemerInterface;
use League\Flysystem\MountManager;

class Imports extends Table {

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, SchemerInterface $schemer, MountManager $filesystems) {
    parent::__construct($db, $models, $schemer);
    $this->filesystems = $filesystems;
  }

  public function create($import) {
    $this->store($import);
  }

  public function run($import) {
    $index = $created = $updated = 0;
    $errors = [];
    $import = $this->db->query("imports")->condition("id", $import['id'])->one();
    if (empty($import['action'])) $import['action'] = "create";
    $file = $this->db->query("files")->condition("id", $import['source'])->one();
    $fields = $this->db->query("imports_fields")->condition("imports_id", $import['id'])->sort("position")->all();
    $keys = $head = [];
    foreach ($fields as $field) {
      if ($field['update_key']) $keys[] = $field['destination'];
    }
    if (false !== ($handle = $this->filesystems->readStream($file["location"]."://".$file['id']."_".$file['filename']))) {
      $row = fgetcsv($handle);
      foreach ($row as $idx => $column) $head[$column] = $idx;
      while (false !== ($row = fgetcsv($handle))) {
        $index++;
        $record = [];
        $updating = false;
        foreach ($fields as $field) {
          $record[$field['destination']] = $row[$head[$field['source']]];
        }
        if (!empty($keys)) {
          $query = $this->db->query($import['model']);
          foreach ($keys as $key) $query->condition($import['model'].".".$key, $record[$key]);
          $exists = $query->one();
          if ($exists) {
            $record['id'] = $exists['id'];
            $updating = true;
          }
        }
        $this->models->get($import['model'])->{$import['action']}($record);
        if ($this->models->get($import['model'])->errors()) {
          $errors[$index] = $this->models->get($import['model'])->errors(false, true);
          $this->db->errors->set([]);
        } elseif ($updating) {
          $updated++;
        } else {
          $created++;
        }
      }
    }
  }
}
