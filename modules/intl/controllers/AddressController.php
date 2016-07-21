<?php
namespace Starbug\Intl;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
class AddressController extends Controller {
	public function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
	}
	public function init() {
		$this->assign("model", "address");
		$this->response->setTemplate("xhr");
	}
	public function form($locale = "US") {
		$address = array();
		$edit = $this->request->hasParameter("edit");

		//load the country by id OR code
		$country = $this->db->query("countries")->condition("id", $locale)->orCondition("code", $locale)->one();

		//if we have just saved an address or an id has been provided, then load it
		if ($this->db->success("address", "create")) {
			$id = $this->request->hasPost("address", "id") ? $this->request->getPost("address", "id") : $this->models->get("address")->insert_id;
			$address = $this->db->query("address")->condition("id", $id)->one();
		} else if ($this->request->hasParameter("id")) {
			$address = $this->db->query("address")->condition("id", $this->request->getParameter("id"))->one();
		}

		//assign and render
		$formatted = $this->models->get("address")->format($address, $country);
		$this->assign("formatted_address", $formatted);
		$this->assign("address", $address);
		$this->assign("edit", $edit);
		$this->assign("country", $country);
		$this->render("address/form");
	}
}
?>
