<?php
/**
 * terms model
 * @ingroup models
 */
class Terms extends TermsModel {

	function create($term) {
		$term['term'] = normalize($term['term']);
		$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		efault($term['position'], '');
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

}
?>
