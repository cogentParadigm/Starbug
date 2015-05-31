<?php
class AdminTaxonomiesController {
	public $routes = array(
		'update' => '{id}',
		'taxonomy' => '{taxonomy}'
	);
	function init() {
		$this->assign("model", "terms");
		$this->assign("form", "terms");
		$this->assign("cancel_url", "admin/taxonomies");
		if (!empty($this->request->parameters['taxonomy'])) $this->assign("taxonomy", normalize($this->request->parameters['taxonomy']));
	}
	function default_action() {
		$this->render("admin/list");
	}
	function add() {
		$this->assign("form", "taxonomy");
		$this->create();
	}
	function create() {
		if (success("terms", "create") && request()->format != "xhr") {
			$term = get("terms", sb("terms")->insert_id);
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		$term = get("terms", $id);
		$this->assign("taxonomy", $term['taxonomy']);
		if (success("terms", "create")) {
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/update");
	}
	function taxonomy($taxonomy) {
		$this->assign("taxonomy", $taxonomy);
		$this->render("admin/taxonomies/taxonomy");
	}
}
?>
