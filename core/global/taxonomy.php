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
	$parents = query("terms")->condition("taxonomy", $taxonomy)->condition("parent", $parent)->sort("position");
	if ($taxonomy == "groups" && !logged_in("root")) $parents->condition("slug", "root", "!=");
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
function tag($table, $object_id, $field, $tag="") {
	$column_info = column_info($table, $field);
	if (empty($column_info['taxonomy'])) $column_info['taxonomy'] = $table."_".$field;
	$taxonomy = $column_info['taxonomy'];
	$tags = empty($column_info['table']) ? $table."_".$field : $column_info['table'];

	$tag = normalize($tag);
	$slug = strtolower($tag);
	//IF THE TAG IS ALREADY APPLIED, RETURN TRUE
	$existing = query($table)->condition($table.".id", $object_id)
									->open("tag")->condition($field.".id", $tag)->orCondition($field.".slug", $tag)->orCondition($field.".term", $tag)->close();
	if (!empty($existing->one())) return true;

	//IF THE TERM DOESN'T EXIST, ADD IT
	$term = query("terms")->where("(terms.id=:tag || terms.slug=:tag || terms.term=:tag) AND taxonomy=:tax")->params(array("tag" => $tag, "tax" => $taxonomy))->one();
	if (empty($term)) store("terms", "term:$tag  slug:$slug  taxonomy:$taxonomy  parent:0  position:");
	else if ($term['taxonomy'] == "groups" && !logged_in("root") && in_array($term['slug'], array("root"))) return false;
	if (errors()) return false;

	//APPLY TAG
	$term_id = (empty($term)) ? sb("terms")->insert_id : $term['id'];
	store($tags, array($field."_id" => $term_id, $table."_id" => $object_id));
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
function untag($table, $object_id, $field, $tag="") {
	$column_info = column_info($table, $field);
	if (empty($column_info['taxonomy'])) $column_info['taxonomy'] = $table."_".$field;
	$taxonomy = $column_info['taxonomy'];
	$tags = empty($column_info['table']) ? $table."_".$field : $column_info['table'];
	query($tags)->condition($tags.".".$table."_id", $object_id)->open("terms")->condition($field."_id.id", $tag)->orCondition($field."_id.slug", $tag)->orCondition($field."_id.term", $tag)->close()->delete();
}
?>
