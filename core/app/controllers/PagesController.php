<?php
namespace Starbug\Core;
class PagesController extends Controller {
	function show() {
		$this->render("blocks", array("region" => "content"), array("scope" => "templates"));
	}
}
?>
