<?php
namespace Starbug\Core;

class ApiTermsController extends ApiController {
  public $model = "terms";
  public function __construct(IdentityInterface $user) {
    $this->user = $user;
  }
  public function admin() {
    $this->api->render("AdminTerms");
  }
  public function select() {
    $this->api->render("SelectTerms");
  }
  public function index() {
    $this->api->render("TermsList");
  }
  public function tree() {
    $this->api->render("TermsTree");
  }
  public function filterQuery($collection, $query, $ops) {
    if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
    return $query;
  }
}
