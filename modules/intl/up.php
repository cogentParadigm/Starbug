<?php

	$this->table(["languages", "groups" => false],
		["language", "type" => "string"],
		["name", "type" => "string", "length" => "128"]
	);

	$this->table(["strings", "groups" => false],
		["name", "type" => "string", "length" => "255", "index" => ""],
		["language", "type" => "string", "default" => "", "input_type" => "select", "select" => "languages.*", "from" => "languages", "caption" => "%name%", "value" => "language", "optional" => "Language Neutral"],
		["value", "type" => "text"],
		["source", "type" => "bool", "default" => "0", "selection" => "name"]
	);
	$this->index("strings", "language", "name");

	$this->table(["countries", "singular" => "country", "singular_label" => "Country", "groups" => false, "label_select" => "countries.name"],
		["name", "type" => "string", "length" => "128"],
		["code", "type" => "string", "length" => "2", "index" => ""],
		["format", "type" => "string", "default" => "%N%n%O%n%A%n%C"],
		["upper", "type" => "string", "default" => "C"],
		["require", "type" => "string", "default" => "AC"],
		["postal_code_prefix", "type" => "string", "default" => ""],
		["postal_code_format", "type" => "string", "default" => ""],
		["postal_code_label", "type" => "string", "default" => "postal"],
		["province_label", "type" => "string", "default" => "province"],
		["postal_url", "type" => "string", "length" => "255", "default" => ""]
	);

	$this->table(["provinces", "groups" => false, "label_select" => "provinces.name"],
		["countries_id", "type" => "int", "references" => "countries id"],
		["name", "type" => "string", "length" => "128"],
		["code", "type" => "string", "length" => "2", "index" => ""]
	);

	foreach (array("settings", "uris", "terms", "countries") as $m) {
		$this->column($m, ["language", "type" => "string", "default" => "", "input_type" => "select", "select" => "languages.*", "from" => "languages", "caption" => "%name%", "value" => "language", "optional" => "", "index" => ""]);
		$this->column($m, ["source", "type" => "int", "references" => "$m id", "null" => "", "index" => "", "constraint" => false, "default" => "NULL", "display" => false]);
	}

	$this->table(["address", "groups" => false],
		["country", "type" => "int", "references" => "countries id", "alias" => "%code%"],
		["administrative_area", "type" => "string", "default" => ""],
		["locality", "type" => "string", "default" => ""],
		["district", "type" => "string", "default" => ""],
		["postal_code", "type" => "string", "default" => ""],
		["sorting_code", "type" => "string", "default" => ""],
		["address1", "type" => "string", "length" => "128"],
		["address2", "type" => "string", "length" => "128", "default" => ""],
		["organization", "type" => "string", "length" => "128"],
		["recipient", "type" => "string", "length" => "128"]
	);
?>
