<?php
namespace Starbug\Intl\Controller;

use Starbug\Db\DatabaseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Routing\Controller;
use Starbug\Intl\AddressFormatter;
use Starbug\Intl\Display\AddressForm;

class AddressController extends Controller {
  public function __construct(
    protected DatabaseInterface $db,
    protected AddressFormatter $address
  ) {
  }
  public function __invoke(ServerRequestInterface $request, $locale = "US") {
    $address = [];
    $queryParams = $request->getQueryParams();
    $bodyParams = $request->getParsedBody();
    $edit = !empty($queryParams["edit"]);

    // load the country by id OR code
    $country = $this->db->query("countries")->condition("id", $locale)->orCondition("code", $locale)->one();

    // if we have just saved an address or an id has been provided, then load it
    if (!empty($bodyParams) && !$this->db->errors()) {
      $id = $bodyParams["address"]["id"] ?? $this->db->getInsertId("address");
      $address = $this->db->query("address")->condition("id", $id)->one();
    } elseif (!empty($queryParams["id"])) {
      $address = $this->db->query("address")->condition("id", $queryParams["id"])->one();
    }

    // assign and render
    $formatted = $this->address->format($address, $country);
    $this->assign("formatted_address", $formatted);
    $this->assign("address", $address);
    $this->assign("edit", $edit);
    $options = ["code" => $country['code']];
    if (!empty($queryParams["id"])) {
      $options["id"] = $queryParams["id"];
    }
    if (!empty($queryParams["keys"])) {
      $options["input_name"] = $queryParams["keys"];
    }
    $this->assign("options", $options);
    return $this->render("address/form.html", ["form" => AddressForm::class]);
  }
}
