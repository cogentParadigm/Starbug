<?php
namespace Starbug\Content;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class PagesController extends Controller {
  public function __construct(DatabaseInterface $db, SessionHandlerInterface $session) {
    $this->db = $db;
    $this->session = $session;
  }
  public function view($id) {
    $page = $this->db->query("pages")->condition("id", $id)->one();
    if (!$page["published"] && !$this->session->loggedIn("admin")) {
      $this->missing();
    } else {
      $this->render("blocks.html", ["region" => "content", "id" => $id], ["scope" => "templates"]);
    }
  }
}
