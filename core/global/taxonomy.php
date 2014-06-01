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
	$category_column_info = schema($table.".fields.".$field);
	efault($category_column_info['taxonomy'], $table."_".$field);
	$taxonomy = $category_column_info['taxonomy'];
	
	$tag = normalize($tag);
	$slug = strtolower($tag);
	//IF THE TAG IS ALREADY APPLIED, RETURN TRUE
	$existing = query("terms_index,terms", "where:terms_index.type=:type && terms.taxonomy=:tax && terms_index.type_id=:id AND (terms.id=:tag || terms.slug=:tag || terms.term=:tag)", array("type" => $table, "tax" => $taxonomy, "id" => $object_id, "tag" => $tag))->execute();
	if (!empty($existing)) return true;

	//IF THE TERM DOESN'T EXIST, ADD IT
	$term = query("terms", "where:(terms.id=:tag || terms.slug=:tag || terms.term=:tag) AND taxonomy=:tax  limit:1", array("tag" => $tag, "tax" => $taxonomy));
	if (empty($term)) store("terms", "term:$tag  slug:$slug  taxonomy:$taxonomy  parent:0  position:");
	else if ($term['taxonomy'] == "groups" && !logged_in("root") && in_array($term['slug'], array("root"))) return false;
	if (errors()) return false;
		
	//APPLY TAG
	$term_id = (empty($term)) ? sb("terms")->insert_id : $term['id'];
	store("terms_index", "terms_id:$term_id  type:$table  type_id:$object_id  rel:$field");
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
	$category_column_info = schema($table.".fields.".$field);
	efault($category_column_info['taxonomy'], $table."_".$field);
	$taxonomy = $category_column_info['taxonomy'];
	query("terms_index")->where("type='$table' && type_id='$object_id' && rel='$field' && terms_id IN (SELECT id FROM ".P("terms")." WHERE taxonomy='$taxonomy' && (id='$tag' || term='$tag' || slug='$tag'))")->delete();
}
?>
