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
    $this->response->setFormat("xhr");
  }
  public function create() {
    $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    $this->render("admin/update.html");
  }
  public function form($locale = "US") {
    $address = [];
    $queryParams = $this->request->getQueryParams();
    $bodyParams = $this->request->getParsedBody();
    $edit = !empty($queryParams["edit"]);

    // load the country by id OR code
    $country = $this->db->query("countries")->condition("id", $locale)->orCondition("code", $locale)->one();

    // if we have just saved an address or an id has been provided, then load it
    if ($this->db->success("address", "create")) {
      $id = $bodyParams["address"]["id"] ?? $this->models->get("address")->insert_id;
      $address = $this->db->query("address")->condition("id", $id)->one();
    } elseif (!empty($queryParams["id"])) {
      $address = $this->db->query("address")->condition("id", $queryParams["id"])->one();
    }

    // assign and render
    $formatted = $this->models->get("address")->format($address, $country);
    $this->assign("formatted_address", $formatted);
    $this->assign("address", $address);
    $this->assign("edit", $edit);
    $options = ["code" => $country['code'], 'id' => $queryParams["id"]];
    if (!empty($queryParams["keys"])) {
      $options["input_name"] = $queryParams["keys"];
    }
    $this->assign("options", $options);
    $this->render("address/form.html");
  }
}
