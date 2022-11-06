<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemaInterface;

class StoreTermsHook extends QueryHook {
  public function __construct(DatabaseInterface $db, TaxonomyInterface $taxonomy, SchemaInterface $schema) {
    $this->db = $db;
    $this->taxonomy = $taxonomy;
    $this->schema = $schema;
  }
  public function afterStore($query, $key, $value, $column, $argument) {
    $name = $query->model;
    $id = $query->getId();
    $category_column_info = $this->schema->getColumn($name, $column);
    if (empty($category_column_info['taxonomy'])) {
      $category_column_info['taxonomy'] = $name."_".$column;
    }
    $tags = empty($category_column_info['table']) ? $name."_".$column : $category_column_info['table'];
    $mentioned_tags = [];
    $remove_unmentioned = true;
    if (!is_array($value)) {
      $value = explode(",", preg_replace("/[,]+[,\s]*/", ",", $value));
    }
    foreach ($value as $tag) {
      if (0 === strpos($tag, "-")) {
        // remove tag
        $this->taxonomy->untag($name, $id, $column, substr($tag, 1));
        $mentioned_tags[] = substr($tag, 1);
        $remove_unmentioned = false;
      } else {
        // add tag
        if (0 === strpos($tag, "+")) {
          $tag = substr($tag, 1);
          $remove_unmentioned = false;
        }
        // echo "tag('".$name."', ".$id.", '".$column."', '".$tag."')";
        $this->taxonomy->tag($name, $id, $column, $tag);
        $mentioned_tags[] = $tag;
      }
    }
    if ($remove_unmentioned) {
      $query = $this->db->query($tags)->condition($name."_id", $id);
      if (!empty($mentioned_tags)) {
        $query->condition($column."_id", $mentioned_tags, "!=")
          ->condition($column."_id.slug", $mentioned_tags, "!=")
          ->condition($column."_id.term", $mentioned_tags, "!=");
      }
      $query->delete();
    }
  }
}
