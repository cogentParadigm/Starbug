<?php
namespace Starbug\Core;

use Exception;
use Starbug\Db\Schema\SchemerInterface;

class Terms extends Table {

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, SchemerInterface $schemer, InputFilterInterface $filter) {
    parent::__construct($db, $models, $schemer);
    $this->filter = $filter;
  }

  public function create($term) {
    if (!empty($term['term'])) {
      $term['term'] = $this->filter->normalize($term['term']);
      $term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
    }
    if (empty($term['id']) && empty($term['position'])) {
      $term['position'] = '';
    }
    $this->store($term);
    if ($this->errors('slug') && !empty($term['term'])) {
      foreach ($this->errors("slug", true) as $e) {
        $this->error(str_replace("slug", "term", $e), "term");
      }
    }
  }

  public function delete($term) {
    try {
      $this->db->query("terms")->condition("id", $term['id'])->delete();
    } catch (Exception $e) {
      $this->error("This term must be detached from all entities before it can be deleted.", "global");
    }
  }

  public function deleteTaxonomy($term) {
    $tax = $term['taxonomy'];
    $this->db->query("terms")->condition("taxonomy", $tax)->delete();
  }
}
