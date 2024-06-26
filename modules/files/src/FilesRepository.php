<?php
namespace Starbug\Files;

use Starbug\Db\DatabaseInterface;

class FilesRepository {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function save($record) {
    if (empty($record["id"])) {
      if (empty($record['category'])) {
        $record['category'] = "uncategorized";
      }
      if (empty($record["location"])) {
        $record["location"] = "default";
      }
    }
    $this->db->store("files", $record);
    if (!$this->db->errors()) {
      $id = (empty($record['id'])) ? $this->db->getInsertId("files") : $record['id'];
      return $this->db->get("files", $id);
    }
  }
}
