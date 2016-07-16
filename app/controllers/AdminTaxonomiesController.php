<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\InputFilterInterface;
class AdminTaxonomiesController extends Controller {
	public $routes = array(
		'update' => '{id}',
		'taxonomy' => '{taxonomy}'
	);
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models, InputFilterInterface $filter) {
		$this->db = $db;
		$this->terms = $models->get("terms");
		$this->filter = $filter;
	}
	function init() {
		$this->assign("model", "terms");
		$this->assign("form", "terms");
		$this->assign("cancel_url", "admin/taxonomies");
		if ($this->request->hasParameter('taxonomy')) $this->assign("taxonomy", $this->filter->normalize($this->request->getParameter('taxonomy')));
	}
	function default_action() {
		$this->render("admin/list");
	}
	function add() {
		$this->assign("form", "taxonomy");
		$this->create();
	}
	function create() {
		if ($this->db->success("terms", "create") && $this->request->getFormat() != "xhr") {
			$term = $this->db->get("terms", $this->terms->insert_id);
			$this->redirect("admin/taxonomies/taxonomy/".$term['taxonomy']);
		} else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		$term = $this->db->get("terms", $id);
		$this->assign("taxonomy", $term['taxonomy']);
		if ($this->db->success("terms", "create")) {
			$this->redirect("admin/taxonomies/taxonomy/".$term['taxonomy']);
		} else $this->render("admin/update");
	}
	function taxonomy($taxonomy) {
		$this->assign("taxonomy", $taxonomy);
		$this->render("admin/taxonomies/taxonomy");
	}
	function import() {
		$this->render("admin/import");
	}
}
?>
