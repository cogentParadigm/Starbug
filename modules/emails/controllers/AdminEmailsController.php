<?php
namespace Starbug\Emails;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminEmailsController extends Controller {
  public $routes = [
    'update' => '{id}'
  ];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function init() {
    $this->assign("model", "email_templates");
    $this->assign("cancel_url", "admin/emails");
    if ($this->db->success("email_templates", "create")) $this->redirect("admin/emails");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
}
