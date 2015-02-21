<?php
  $module_path = "modules/intl/etc/";
  $address_data = "http://i18napis.appspot.com/address/data/";
  $address_map = array(
    "fmt" => "format",
    "upper" => "upper",
    "require" => "require",
    "postprefix" => "postal_code_prefix",
    "zip" => "postal_code_format",
    "zip_name_type" => "postal_code_label",
    "state_name_type" => "province_label",
    "posturl" => "postal_url"
  );

  //populate countries
  $countries = config("countries", null, $module_path);
  foreach($countries as $c) {
    $exists = query("countries")->condition("code", $c['code']);
    if (!$exists->one()) {
      $data = json_decode(file_get_contents($address_data.$c['code']), true);
      $record = array("name" => $c['name'], "code" => $c['code']);
      if (is_array($record)) {
        foreach ($address_map as $k => $v) {
          if (isset($data[$k])) $record[$v] = $data[$k];
        }
      }
      store("countries", $record);
    }
  }

  //populate regions
  $country = query("countries")->condition("code", "CA")->one();
  $regions = config("provinces", null, $module_path);
  foreach ($regions as $r) {
    $exists = query("provinces")->conditions(array("countries_id" => $country['id'], "code" => $r['code']));
    if (!$exists->one()) {
      $r['countries_id'] = $country['id'];
      store("provinces", $r);
    }
  }
  $country = query("countries")->condition("code", "US")->one();
  $regions = config("states", null, $module_path);
  foreach ($regions as $r) {
    $exists = query("provinces")->conditions(array("countries_id" => $country['id'], "code" => $r['code']));
    if (!$exists->one()) {
      $r['countries_id'] = $country['id'];
      store("provinces", $r);
    }
  }

  //populate languages
  $languages = config("languages", null, $module_path);
  foreach ($languages as $l) {
    $exists = query("languages")->condition("language", $l['language']);
    if (!$exists->one()) store("languages", $l);
  }

  //populate strings
  $strings = config("strings", null, $module_path);
  foreach ($strings as $s) {
    $exists = query("strings")->conditions(array("language" => "en", "name" => $s['name']));
    if (!$exists->one()) {
      $s['language'] = "en";
      store("strings", $s);
    }
  }

?>
