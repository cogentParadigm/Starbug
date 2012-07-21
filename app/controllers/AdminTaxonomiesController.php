<?php
class AdminTaxonomiesController extends Controller {
	function init() {
		assign("model", "taxonomies");
	}
	function default_action() {
		$this->render("admin/taxonomies/list");
	}
	function create() {
		if (success("taxonomies", "create")) redirect(uri("admin/taxonomies/update", 'u'));
		else $this->render("admin/taxonomies/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/taxonomies/update");
	}
}
?>
