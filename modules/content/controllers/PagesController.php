<?php
namespace Starbug\Content;
use Starbug\Core\Controller;
class PagesController extends Controller {
	public $routes = [
		"view" => "view/{id}"
	];
	function view($id) {
		$this->render("blocks.html", array("region" => "content", "id" => $id), array("scope" => "templates"));
	}
}
