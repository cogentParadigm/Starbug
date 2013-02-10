<?php
class AdminTaxonomiesController {
	function init() {
		assign("model", "taxonomies");
	}
	function default_action() {
		$this->render("admin/taxonomies/list");
	}
	function create() {
		assign("cancel_url", uri("admin/taxonomies"));
		if (success("terms", "create")) redirect(uri("admin/taxonomies/update", 'u'));
		else $this->render("admin/taxonomies/create");
	}
	function update($id=null) {
		$taxonomy = $_POST['terms']['taxonomy'] = urldecode($id);
		$label = ucwords(str_replace("_", " ", $taxonomy));
		assign("cancel_url", uri("admin/taxonomies"));
		assign("taxonomy", $taxonomy);
		assign("label", $label);
		$this->render("admin/taxonomies/update");
	}
}
?>
