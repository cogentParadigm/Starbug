<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/taxonomy.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup taxonomy
 */
/**
 * @defgroup taxonomy
 * global functions
 * @ingroup global
 */
function terms($taxonomy, $parent=0, $depth=0) {
	$terms = array();
	$parents = query("terms", "where:taxonomy=? AND parent=?", array($taxonomy, $parent));
	foreach ($parents as $idx => $term) {
		$term['depth'] = $depth;
		$terms[] = $term;
		$terms = array_merge($terms, terms($taxonomy, $term['id'], ($depth+1)));
	}
	return $terms;
}
/**
 * apply tags
 * @ingroup taxonomy
 * @param string $taxonomy the taxonomy/classification of terms. eg. uris_tags
 * @param int $object_id the id of the object to apply the tag to
 * @param string $tag the tag
 * @return bool returns true on success, false otherwise.
 */
function tag($table, $taxonomy, $object_id, $tag="") {
	$args = func_get_args();
	$count = count($args);
	if ($count == 3) {
		list($taxonomy, $object_id, $tag) = $args;
		$table = reset(explode("_", $taxonomy));
	}
	
	$tag = normalize($tag);
	$slug = strtolower($tag);
	//IF THE TAG IS ALREADY APPLIED, RETURN TRUE
	$existing = query("$taxonomy,terms", "where:$taxonomy.$table"."_id=? AND terms.term=?", array($object_id, $tag));
	if (!empty($existing)) return true;

	//IF THE TERM DOESN'T EXIST, ADD IT
	$term = query("terms", "where:term=? AND taxonomy=?  limit:1", array($tag, $taxonomy));
	if (empty($term)) store("terms", "term:$tag  slug:$slug  taxonomy:$taxonomy  parent:0  position:");
	if (errors()) return false;
		
	//APPLY TAG
	$term_id = (empty($term)) ? sb("insert_id") : $term['id'];
	store($taxonomy, "terms_id:$term_id  $table"."_id:$object_id");
	return (!errors());
}
/**
 * remove tags
 * @ingroup taxonomy
 * @param string $table (optional) the table to which tags are applied. This is only needed if not implied by $taxonomy
 * @param string $taxonomy the taxonomy/classification of terms. eg. uris_tags or genres
 * @param int $object_id the id of the object to apply the tag to
 * @param string $tag the tag
 */
function untag($table, $taxonomy, $object_id, $tag="") {
	$args = func_get_args();
	$count = count($args);
	if ($count == 3) {
		list($taxonomy, $object_id, $tag) = $args;
		$table = reset(explode("_", $taxonomy));
	}
	remove($taxonomy, $table."_id='$object_id' AND terms_id IN (SELECT id FROM ".P("terms")." WHERE (term='$tag' || slug='$tag'))");
}
?>
