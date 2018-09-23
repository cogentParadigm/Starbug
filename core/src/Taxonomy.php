<?php
namespace Starbug\Core;

/**
 * Implementation of TaxonomyInterface.
 */
class Taxonomy implements TaxonomyInterface {
  protected $db;
  protected $models;
  protected $user;
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, IdentityInterface $user, InputFilterInterface $filter) {
    $this->db = $db;
    $this->models = $models;
    $this->user = $user;
    $this->filter = $filter;
  }
  public function terms($taxonomy, $parent = 0, $depth = 0) {
    $terms = [];
    $parents = $this->db->query("terms")->condition("taxonomy", $taxonomy)->condition("parent", $parent)->sort("position");
    if ($taxonomy == "groups" && !$this->user->loggedIn("root")) $parents->condition("slug", "root", "!=");
    $parents = $parents->all();
    foreach ($parents as $idx => $term) {
      $term['depth'] = $depth;
      $terms[] = $term;
      $terms = array_merge($terms, $this->terms($taxonomy, $term['id'], ($depth+1)));
    }
    return $terms;
  }
  /**
   * Apply tags
   *
   * @param string $taxonomy the taxonomy/classification of terms. eg. products_tags
   * @param int $object_id the id of the object to apply the tag to
   * @param string $tag the tag
   *
   * @return bool returns true on success, false otherwise.
   */
  public function tag($table, $object_id, $field, $tag = "") {
    $columnInfo = $this->models->get($table)->columnInfo($field);
    if (empty($columnInfo['taxonomy'])) $columnInfo['taxonomy'] = $table."_".$field;
    $taxonomy = $columnInfo['taxonomy'];
    $tags = empty($columnInfo['table']) ? $table."_".$field : $columnInfo['table'];

    $tag = $this->filter->normalize($tag);
    $slug = strtolower($tag);
    // IF THE TAG IS ALREADY APPLIED, RETURN TRUE
    $existing = $this->db->query($table)->condition($table.".id", $object_id);
    $existing->condition(
      $existing->createOrCondition()
      ->condition($table.".".$field.".id", $tag)
      ->condition($table.".".$field.".slug", $tag)
      ->condition($table.".".$field.".term", $tag)
    );
    if ($existing->one()) return true;

    // IF THE TERM DOESN'T EXIST, ADD IT
    $term = $this->db->query("terms")->where("(terms.id=:tag || terms.slug=:tag || terms.term=:tag) AND taxonomy=:tax")->bind(["tag" => $tag, "tax" => $taxonomy])->one();
    if (empty($term)) $this->db->store("terms", ["term" => $tag, "slug" => $slug, "taxonomy" => $taxonomy, "parent" => "0", "position" => ""]);
    elseif ($term['taxonomy'] == "groups" && !$this->user->loggedIn("root") && in_array($term['slug'], ["root"])) return false;
    if ($this->db->errors()) return false;

    // APPLY TAG
    $term_id = (empty($term)) ? $this->models->get("terms")->insert_id : $term['id'];
    $this->db->store($tags, [$field."_id" => $term_id, $table."_id" => $object_id]);
    return (!$this->db->errors());
  }
  /**
   * Remove tags
   *
   * @param string $table (optional) the table to which tags are applied. This is only needed if not implied by $taxonomy
   * @param string $taxonomy the taxonomy/classification of terms. eg. products_tags or genres
   * @param int $object_id the id of the object to apply the tag to
   * @param string $tag the tag
   */
  public function untag($table, $object_id, $field, $tag = "") {
    $columnInfo = $this->models->get($table)->columnInfo($field);
    if (empty($columnInfo['taxonomy'])) $columnInfo['taxonomy'] = $table."_".$field;
    $tags = empty($columnInfo['table']) ? $table."_".$field : $columnInfo['table'];
    $query = $this->db->query($tags)->condition($tags.".".$table."_id", $object_id);
    $fields = [$field."_id.id", $field."_id.slug", $field."_id.term"];
    $query->where("(".implode("=:tag || ", $fields)."=:tag)")->bind("tag", $tag)->delete();
  }
}
