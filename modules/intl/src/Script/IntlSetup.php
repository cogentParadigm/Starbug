<?php
namespace Starbug\Intl\Script;

use Starbug\Db\DatabaseInterface;
use Starbug\Config\ConfigInterface;

class IntlSetup {
  public function __construct(
    protected ConfigInterface $config,
    protected DatabaseInterface $db
  ) {
  }
  public function __invoke() {
    // populate countries
    $countries = $this->config->get("countries");
    foreach ($countries as $country) {
      $exists = $this->db->query("countries")->condition("code", $country["code"])->one();
      if ($exists) {
        $country["id"] = $exists["id"];
      }
      $this->db->store("countries", $country);
    }

    // populate regions
    $country = $this->db->query("countries")->condition("code", "CA")->one();
    $regions = $this->config->get("provinces");
    foreach ($regions as $r) {
      $exists = $this->db->query("provinces")->conditions(["countries_id" => $country['id'], "code" => $r['code']]);
      if (!$exists->one()) {
        $r['countries_id'] = $country['id'];
        $this->db->store("provinces", $r);
      }
    }
    $country = $this->db->query("countries")->condition("code", "US")->one();
    $regions = $this->config->get("states");
    foreach ($regions as $r) {
      $exists = $this->db->query("provinces")->conditions(["countries_id" => $country['id'], "code" => $r['code']]);
      if (!$exists->one()) {
        $r['countries_id'] = $country['id'];
        $this->db->store("provinces", $r);
      }
    }

    // populate languages
    $languages = $this->config->get("languages");
    foreach ($languages as $l) {
      $exists = $this->db->query("languages")->condition("language", $l['language']);
      if (!$exists->one()) {
        $this->db->store("languages", $l);
      }
    }

    // populate strings
    $strings = $this->config->get("strings");
    foreach ($strings as $s) {
      $exists = $this->db->query("strings")->conditions(["language" => "en", "name" => $s['name']]);
      if (!$exists->one()) {
        $s['language'] = "en";
        $this->db->store("strings", $s);
      }
    }
  }
}
