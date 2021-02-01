<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class ApiTermsController extends ApiController {
  public $model = "terms";
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function admin() {
    return $this->api->render("AdminTerms");
  }
  public function select() {
    return $this->api->render("SelectTerms");
  }
  public function index() {
    return $this->api->render("TermsList");
  }
  public function tree() {
    return $this->api->render("TermsTree");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->session->loggedIn("root") && !$this->session->loggedIn("admin")) $query->action("read");
    return $query;
  }
}
