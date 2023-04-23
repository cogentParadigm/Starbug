<?php
namespace Starbug\Intl;

use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Core\TemplateInterface;

class AddressForm extends FormDisplay {
  public $model = "address";
  public $cancel_url = "address";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    DatabaseInterface $db,
    TranslationInterface $translation
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
    $this->db = $db;
    $this->translation = $translation;
  }
  public function buildDisplay($ops) {
    if ($this->success("create") && !$this->hasPost("id")) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    if (!empty($ops["input_name"])) {
      $this->template = "fields.html";
    }
    if (empty($ops['code'])) {
      $ops['code'] = "US";
    }
    $country = $this->db->query("countries")->condition("code", $ops['code'])->orCondition("id", $ops["code"])->one();
    $country['province_label'] = $this->translation->get($country['province_label']);
    $country['postal_code_label'] = $this->translation->get($country['postal_code_label']);
    $this->add(["country", "input_type" => "select", "from" => "countries", "default" => $country['id']]);
    $format = str_split($country['format']);
    $upper = str_split($country['upper']);
    $req = str_split($country['require']);
    if (!empty($ops["includeRecipient"])) {
      $this->add(["recipient", "input_type" => "text", "label" => "Full name", "required" => true]);
    }
    $this->add(["address1", "input_type" => "text", "label" => "Address 1", "required" => true]);
    $this->add(["address2", "input_type" => "text", "label" => "Address 2"]);
    foreach ($format as $idx => $token) {
      $append = [];
      if (in_array($token, $req)) {
        $append['required'] = true;
      }
      // Ignoring N (Name), O (Organization), and A (Address). Address lines and recipient are included above.
      if ($token == 'D') {
        $this->add(["district", "input_type" => "text", "label" => "District"] + $append);
      } elseif ($token == 'C') {
        $this->add(["locality", "input_type" => "text", "label" => "City"] + $append);
      } elseif ($token == 'S') {
        if (in_array($country['code'], ['US', 'CA'])) {
          $this->add(["administrative_area", "input_type" => "select", "from" => "provinces", "query" => "ProvincesSelect", "label" => $country['province_label'], "country" => $country['code']] + $append);
        } else {
          $this->add(["administrative_area", "input_type" => "text", "label" => $country['province_label']] + $append);
        }
      } elseif ($token == 'Z') {
        $this->add(["postal_code", "input_type" => "text", "label" => $country['postal_code_label']] + $append);
      } elseif ($token == 'X') {
        $this->add(["sorting_code", "input_type" => "text", "label" => "Sorting Code"] + $append);
      }
    }
  }
  protected function beforeQuery($options) {
    if (!empty($options["default"]) && empty($options["id"]) && !$this->hasPost()) {
      $this->setPost($options["default"]);
    }
  }
}
