<?php
/**
 * terms model
 * @ingroup models
 */
class Terms {

	function create($term) {
		if (!empty($term['term'])) {
			$term['term'] = normalize($term['term']);
			$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		}
		if(empty($term['id'])) efault($term['position'], '');
		$this->store($term);
		if (errors('terms[slug]') && !empty($term['term'])) foreach (errors("terms[slug]", true) as $e) error(str_replace("slug", "term", $e), "term");
	}

	function delete($term) {
		query("terms_index")->condition("terms_id", $term['id'])->delete();
		query("terms")->condition("id", $term['id'])->delete();
	}
	
	function delete_taxonomy($term) {
		$tax = $term['taxonomy'];
		$this->remove("taxonomy:$tax");
	}
	
	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
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
		$query->select("terms.*,(SELECT COUNT(*) FROM ".P("terms")." as t WHERE t.parent=terms.id) as children");
		if (!empty($ops['parent'])) $query->condition("parent", $ops['parent']);
		else $query->condition("terms.parent", 0);
		$query->sort("terms.position");
		return $query;
	}
	
	function display_admin($display, $ops) {
		$display->add("taxonomy", "row_options  plugin:starbug.grid.columns.taxonomy_options");
	}
	
	function display_tree($display, $ops) {
		$display->insert(0, "id  plugin:starbug.grid.columns.tree  sortable:false");
		$display->add("term  sortable:false", "position  sortable:false");
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
