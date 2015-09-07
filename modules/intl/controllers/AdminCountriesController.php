<?php
namespace Starbug\Intl;
use Starbug\Core\DatabaseInterface;
class AdminCountriesController {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "countries");
		$this->assign("cancel_url", "admin/countries");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("countries", "create")) redirect(uri("admin/countries", 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("countries", "create")) redirect(uri("admin/countries", 'u'));
		else $this->render("admin/update");
	}
}
?>
