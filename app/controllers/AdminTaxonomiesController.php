<?php
class AdminTaxonomiesController {
	function init() {
		assign("model", "terms");
	}
	function default_action() {
		$this->render("admin/taxonomies/list");
	}
	function add() {
		$this->render("admin/taxonomies/add");
	}
	function create() {
		assign("taxonomy", $_GET['taxonomy']);
		if (success("terms", "create")) {
			$term = get("terms", sb("terms")->insert_id);
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/taxonomies/create");
	}
	function update($id=null) {
		assign("id", $id);
		if (success("terms", "create")) {
			$term = get("terms", $id);
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/taxonomies/update");
	}
	function taxonomy($taxonomy=null) {
		assign("taxonomy", $taxonomy);
		$this->render("admin/taxonomies/taxonomy");
	}
}
?>
