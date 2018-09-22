<?php
namespace Starbug\App;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\InputFilterInterface;

class AdminTaxonomiesController extends Controller {
  public $routes = [
    'update' => '{id}',
    'taxonomy' => '{taxonomy}'
  ];
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, InputFilterInterface $filter) {
    $this->db = $db;
    $this->terms = $models->get("terms");
    $this->filter = $filter;
  }
  public function init() {
    $this->assign("model", "terms");
    if ($this->request->hasParameter('taxonomy')) $this->assign("taxonomy", $this->filter->normalize($this->request->getParameter('taxonomy')));
  }
  public function default_action() {
    $this->render("admin/list.html");
  }
  public function add() {
    $this->assign("form", "taxonomy");
    $this->create();
  }
  public function create() {
    if ($this->db->success("terms", "create") && $this->request->getFormat() != "xhr") {
      $term = $this->db->get("terms", $this->terms->insert_id);
      $this->redirect("admin/taxonomies/taxonomy/".$term['taxonomy']);
    } else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $term = $this->db->get("terms", $id);
    $this->assign("taxonomy", $term['taxonomy']);
    if ($this->db->success("terms", "create")) {
      $this->redirect("admin/taxonomies/taxonomy/".$term['taxonomy']);
    } else $this->render("admin/update.html");
  }
  public function taxonomy($taxonomy) {
    $this->assign("taxonomy", $taxonomy);
    $this->render("admin/taxonomies/taxonomy.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
}
