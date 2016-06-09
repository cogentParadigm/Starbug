<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/Taxonomy.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
/**
 * implementation of TaxonomyInterface
 */
class Taxonomy implements TaxonomyInterface {
	protected $db;
	protected $models;
	protected $user;
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models, IdentityInterface $user, InputFilterInterface $filter) {
		$this->db = $db;
		$this->models = $models;
		$this->user = $user;
		$this->filter = $filter;
	}
	function terms($taxonomy, $parent = 0, $depth = 0) {
		$terms = array();
		$parents = $this->db->query("terms")->condition("taxonomy", $taxonomy)->condition("parent", $parent)->sort("position");
		if ($taxonomy == "groups" && !$this->user->loggedIn("root")) $parents->condition("slug", "root", "!=");
		foreach ($parents as $idx => $term) {
			$term['depth'] = $depth;
			$terms[] = $term;
			$terms = array_merge($terms, $this->terms($taxonomy, $term['id'], ($depth+1)));
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
	function tag($table, $object_id, $field, $tag = "") {
		$column_info = $this->models->get($table)->column_info($field);
		if (empty($column_info['taxonomy'])) $column_info['taxonomy'] = $table."_".$field;
		$taxonomy = $column_info['taxonomy'];
		$tags = empty($column_info['table']) ? $table."_".$field : $column_info['table'];

		$tag = $this->filter->normalize($tag);
		$slug = strtolower($tag);
		//IF THE TAG IS ALREADY APPLIED, RETURN TRUE
		$existing = $this->db->query($table)->condition($table.".id", $object_id)
										->open("tag")->condition($field.".id", $tag)->orCondition($field.".slug", $tag)->orCondition($field.".term", $tag)->close();
		if ($existing->one()) return true;

		//IF THE TERM DOESN'T EXIST, ADD IT
		$term = $this->db->query("terms")->where("(terms.id=:tag || terms.slug=:tag || terms.term=:tag) AND taxonomy=:tax")->params(array("tag" => $tag, "tax" => $taxonomy))->one();
		if (empty($term)) $this->db->store("terms", ["term" => $tag, "slug" => $slug, "taxonomy" => $taxonomy, "parent" => 0, "position" => ""]);
		else if ($term['taxonomy'] == "groups" && !$this->user->loggedIn("root") && in_array($term['slug'], array("root"))) return false;
		if ($this->db->errors()) return false;

		//APPLY TAG
		$term_id = (empty($term)) ? $this->models->get("terms")->insert_id : $term['id'];
		$this->db->store($tags, array($field."_id" => $term_id, $table."_id" => $object_id));
		return (!$this->db->errors());
	}
	/**
	 * remove tags
	 * @ingroup taxonomy
	 * @param string $table (optional) the table to which tags are applied. This is only needed if not implied by $taxonomy
	 * @param string $taxonomy the taxonomy/classification of terms. eg. uris_tags or genres
	 * @param int $object_id the id of the object to apply the tag to
	 * @param string $tag the tag
	 */
	function untag($table, $object_id, $field, $tag = "") {
		$column_info = $this->models->get($table)->column_info($field);
		if (empty($column_info['taxonomy'])) $column_info['taxonomy'] = $table."_".$field;
		$tags = empty($column_info['table']) ? $table."_".$field : $column_info['table'];
		$this->db->query($tags)->condition($tags.".".$table."_id", $object_id)->open("terms")->condition($field."_id.id", $tag)->orCondition($field."_id.slug", $tag)->orCondition($field."_id.term", $tag)->close()->delete();
	}
}
