<?php
namespace Starbug\Content;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\IdentityInterface;

class PagesController extends Controller {
  public function __construct(DatabaseInterface $db, IdentityInterface $user) {
    $this->db = $db;
    $this->user = $user;
  }
  public function view($id) {
    $page = $this->db->query("pages")->condition("id", $id)->one();
    if (!$page["published"] && !$this->user->loggedIn("admin")) {
      $this->missing();
    } else {
      $this->render("blocks.html", ["region" => "content", "id" => $id], ["scope" => "templates"]);
    }
  }
}
