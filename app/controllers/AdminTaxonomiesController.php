<?php
class AdminTaxonomiesController {
	function init() {
		assign("model", "terms");
		assign("form", "terms");
		assign("cancel_url", "admin/taxonomies");
		if (!empty($_GET['taxonomy'])) assign("taxonomy", normalize($_GET['taxonomy']));
	}
	function default_action() {
		$this->render("admin/list");
	}
	function add() {
		assign("form", "taxonomy");
		$this->create();
	}
	function create() {
		if (success("terms", "create") && request()->format != "xhr") {
			$term = get("terms", sb("terms")->insert_id);
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$term = get("terms", $id);
		assign("taxonomy", $term['taxonomy']);
		if (success("terms", "create")) {
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/update");
	}
	function taxonomy($taxonomy=null) {
		assign("taxonomy", $taxonomy);
		$this->render("admin/taxonomies/taxonomy");
	}
}
?>
