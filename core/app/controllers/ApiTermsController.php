<?php
namespace Starbug\Core;
class ApiTermsController extends ApiController {
	public $model = "terms";
	function __construct(IdentityInterface $user) {
		$this->user = $user;
	}
	function admin() {
		$this->api->render("AdminTerms");
	}
	function select() {
		$this->api->render("Select");
	}
	function index() {
		$this->api->render("TermsList");
	}
	function tree() {
		$this->api->render("TermsTree");
	}
	function filterQuery($collection, $query, &$ops) {
		if (!$this->user->loggedIn("root") && !$this->user->loggedIn("admin")) $query->action("read");
		return $query;
	}
}
