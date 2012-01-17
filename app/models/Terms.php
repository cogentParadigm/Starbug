<?php
/**
 * terms model
 * @ingroup models
 */
class Terms extends TermsModel {

	function create($term) {
		$term['term'] = normalize($term['term']);
		$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		$this->store($term);
	}

	function delete($term) {
		return $this->remove('id='.$term['id']);
	}

}
?>
