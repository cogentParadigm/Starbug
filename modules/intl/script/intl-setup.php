<?php
class IntlSetupCommand {
	public function __construct(ConfigInterface $config) {
		$this->config = $config;
	}
	public function run($argv) {
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
		$countries = $this->config->get("countries");
		foreach($countries as $c) {
			$exists = query("countries")->condition("code", $c['code'])->one();
			$data = json_decode(file_get_contents($address_data.$c['code']), true);
			$record = array("name" => $c['name'], "code" => $c['code']);
			if ($exists) $record["id"] = $exists["id"];
			if (is_array($record)) {
				foreach ($address_map as $k => $v) {
					if (isset($data[$k])) $record[$v] = $data[$k];
				}
			}
			store("countries", $record);
		}

		//populate regions
		$country = query("countries")->condition("code", "CA")->one();
		$regions = $this->config->get("provinces");
		foreach ($regions as $r) {
			$exists = query("provinces")->conditions(array("countries_id" => $country['id'], "code" => $r['code']));
			if (!$exists->one()) {
				$r['countries_id'] = $country['id'];
				store("provinces", $r);
			}
		}
		$country = query("countries")->condition("code", "US")->one();
		$regions = $this->config->get("states");
		foreach ($regions as $r) {
			$exists = query("provinces")->conditions(array("countries_id" => $country['id'], "code" => $r['code']));
			if (!$exists->one()) {
				$r['countries_id'] = $country['id'];
				store("provinces", $r);
			}
		}

		//populate languages
		$languages = $this->config->get("languages");
		foreach ($languages as $l) {
			$exists = query("languages")->condition("language", $l['language']);
			if (!$exists->one()) store("languages", $l);
		}

		//populate strings
		$strings = $this->config->get("strings");
		foreach ($strings as $s) {
			$exists = query("strings")->conditions(array("language" => "en", "name" => $s['name']));
			if (!$exists->one()) {
				$s['language'] = "en";
				store("strings", $s);
			}
		}
	}
}

?>
