<?php
namespace Starbug\Core;

/**
 * A simple interface for accessing taxonomy trees, and applying categories and tags.
 */
interface TaxonomyInterface {
  public function terms($taxonomy, $parent = 0, $depth = 0);
  /**
   * Apply tags.
   *
   * @param string $taxonomy the taxonomy/classification of terms. eg. products_tags
   * @param int $object_id the id of the object to apply the tag to
   * @param string $tag the tag
   *
   * @return bool returns true on success, false otherwise.
   */
  public function tag($table, $object_id, $field, $tag = "");
  /**
   * Remove tags
   *
   * @param string $table (optional) the table to which tags are applied. This is only needed if not implied by $taxonomy
   * @param string $taxonomy the taxonomy/classification of terms. eg. products_tags or genres
   * @param int $object_id the id of the object to apply the tag to
   * @param string $tag the tag
   */
  public function untag($table, $object_id, $field, $tag = "");
}
