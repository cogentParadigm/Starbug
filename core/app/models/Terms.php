<?php
namespace Starbug\Core;
class Terms extends TermsModel {

	function create($term) {
		if (!empty($term['term'])) {
			$term['term'] = $this->filter->normalize($term['term']);
			$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		}
		if (empty($term['id']) && empty($term['position'])) $term['position'] = '';
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
}
