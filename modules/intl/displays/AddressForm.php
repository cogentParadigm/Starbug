<?php
namespace Starbug\Intl;

use Starbug\Core\FormDisplay;

class AddressForm extends FormDisplay {
  public $model = "address";
  public $cancel_url = "address";
  public function buildDisplay($ops) {
    if ($this->success("create") && !$this->request->hasPost($this->model, "id")) $this->request->setPost($this->model, "id", $this->models->get($this->model)->insert_id);
    if (!empty($ops["input_name"])) $this->template = "fields";
    if (empty($ops['code'])) $ops['code'] = "US";
    $country = $this->models->get("countries")->query()->condition("code", $ops['code'])->orCondition("id", $ops["code"])->one();
    // $country['province_label'] = t($country['province_label']);
    // $country['postal_code_label'] = t($country['postal_code_label']);
    $this->add(["country", "input_type" => "select", "from" => "countries", "default" => $country['id']]);
    $format = str_split($country['format']);
    $upper = str_split($country['upper']);
    $req = str_split($country['require']);
    $this->add(["recipient", "input_type" => "text", "label" => "Full name", "required" => true]);
    $this->add(["address1", "input_type" => "text", "label" => "Address 1", "required" => true]);
    $this->add(["address2", "input_type" => "text", "label" => "Address 2"]);
    foreach ($format as $idx => $token) {
      $append = [];
      if (in_array($token, $req)) $append['required'] = true;
      // Ignoring N (Name), O (Organization), and A (Address). Address lines and recipient are included above.
      if ($token == 'D') {
        $this->add(["district", "input_type" => "text", "label" => "District"] + $append);
      } elseif ($token == 'C') {
        $this->add(["locality", "input_type" => "text", "label" => "City"] + $append);
      } elseif ($token == 'S') {
        if (in_array($country['code'], ['US', 'CA'])) $this->add(["administrative_area", "input_type" => "select", "from" => "provinces", "query" => "Select", "label" => $country['province_label'], "country" => $country['code']] + $append);
        else $this->add(["administrative_area", "input_type" => "text", "label" => $country['province_label']] + $append);
      } elseif ($token == 'Z') {
        $this->add(["postal_code", "input_type" => "text", "label" => $country['postal_code_label']] + $append);
      } elseif ($token == 'X') {
        $this->add(["sorting_code", "input_type" => "text", "label" => "Sorting Code"] + $append);
      }
    }
  }
}
