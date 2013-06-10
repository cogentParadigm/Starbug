<?php
/**
 * terms model
 * @ingroup models
 */
class Terms {

	function create($term) {
		$term['term'] = normalize($term['term']);
		$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		if(empty($term['id'])) efault($term['position'], '');
		$this->store($term);
		if (errors('terms[slug]') && !empty($term['term'])) foreach (errors("terms[slug]", true) as $e) error(str_replace("slug", "term", $e), "term");
	}

	function delete($term) {
		return $this->remove('id='.$term['id']);
	}
	
	function delete_taxonomy($term) {
		$tax = $term['taxonomy'];
		$this->remove("taxonomy='$tax'");
	}
	
	function query_admin($query) {
		$query['select'] = "DISTINCT taxonomy";
		$query['where'][] = '!(terms.status & 1)';
		return $query;
	}
	
	function query_list($query) {
		if (!empty($query['taxonomy'])) {
			$query['where'][] = "taxonomy=?";
			$query['params'][] = $query['taxonomy'];
		}
		$query['orderby'] = "term_path ASC, position ASC";
		return $query;
	}

}
?>
