<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
class AdminTaxonomiesController extends Controller {
	public $routes = array(
		'update' => '{id}',
		'taxonomy' => '{taxonomy}'
	);
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->terms = $models->get("terms");
	}
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
		if ($this->db->success("terms", "create") && $this->request->format != "xhr") {
			$term = $this->db->get("terms", $this->terms->insert_id);
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		$term = $this->db->get("terms", $id);
		$this->assign("taxonomy", $term['taxonomy']);
		if ($this->db->success("terms", "create")) {
			redirect(uri("admin/taxonomies/taxonomy/".$term['taxonomy']));
		} else $this->render("admin/update");
	}
	function taxonomy($taxonomy) {
		$this->assign("taxonomy", $taxonomy);
		$this->render("admin/taxonomies/taxonomy");
	}
}
?>
