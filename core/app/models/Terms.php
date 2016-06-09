<?php
/**
 * terms model
 * @ingroup models
 */
namespace Starbug\Core;
class Terms extends TermsModel {

	function create($term) {
		if (!empty($term['term'])) {
			$term['term'] = $this->filter->normalize($term['term']);
			$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		}
		if(empty($term['id']) && empty($term['position'])) $term['position'] = '';
		$this->store($term);
		if ($this->errors('slug') && !empty($term['term'])) foreach ($this->errors("slug", true) as $e) $this->error(str_replace("slug", "term", $e), "term");
	}

	function delete($term) {
		try {
			$this->db->query("terms")->condition("id", $term['id'])->delete();
		} catch (Exception $e) {
			//TODO: handle this if it's a foreign key constraint
		}
		$term = $this->db->query("terms")->condition("id", $term['id'])->one();
		if ($term) $this->error("This term must be detached from all entities before it can be deleted.", "global");
	}

	function delete_taxonomy($term) {
		$tax = $term['taxonomy'];
		$this->db->query("terms")->condition("taxonomy", $tax)->delete();
	}

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		$query->undo("select");
		$query->select("DISTINCT terms.taxonomy");
		return $query;
	}

	function query_list($query, &$ops) {
		$query->sort("terms.term_path ASC, terms.position ASC");
		return $query;
	}

	function query_filters($action, $query, &$ops) {
		$query = parent::query_filters($action, $query, $ops);
		if (!empty($ops['taxonomy'])) {
			$query->condition("terms.taxonomy", $ops['taxonomy']);
		}
		return $query;
	}

	function query_tree($query, &$ops) {
		$query->select("terms.*,(SELECT COUNT(*) FROM ".$this->db->prefix("terms")." as t WHERE t.parent=terms.id) as children");
		if (!empty($ops['parent'])) $query->condition("parent", $ops['parent']);
		else $query->condition("terms.parent", 0);
		$query->sort("terms.position");
		return $query;
	}

	function filter($item, $action) {
		if ($action === "tree") {
			$depth = 0;
			if (!empty($item['term_path'])) {
				$tree = $item['term_path'];
				$depth = substr_count($tree, "-")-1;
			}
			if ($depth > 0) $item['term'] = str_pad(" ".$item['term'], strlen(" ".$item['term'])+$depth, "-", STR_PAD_LEFT);
		} else if ($action === "admin") {
			$item['id'] = $item['taxonomy'];
		}
		return $item;
	}

}
?>
