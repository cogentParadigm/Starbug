<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/ApiRequest.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
/**
 * ApiRequest
 * @ingroup core
 */
class ApiRequest {

	var $types = array(
		"xml" => "text/xml",
		"json" => "application/json",
		"jsonp" => "application/x-javascript"
	);
	var $result = "";

	/**
	 * API Request constructor
	 * @param string $what an api request string in the format '[object].[format]'
	 * 										 where object is an API function or set of models to query, and format is the desired output format (json, jsonp, xml)
	 * @param star $ops additional options, query paramaters if [object] is a model or group of models
	 */
	function __construct($what, $ops="") {
		$format = end(explode(".", $what));
		$call = reset(explode("/", str_replace(".$format", "", $what)));
		$ops = starr::star($ops);
		efault($ops['action'], 'read');
		header("Content-Type: ".$this->types[$format]);
		$this->result = call_user_func(array($this, $call), $call, $format, $ops);
	}

	/**
	 * API query function - outputs records from the DB
	 * @param string $call function name passed by constructor
	 * @param string $format the desired output format: json, jsonp or xml
	 * @param star $ops additional options
	 */
	function __call($model, $args) {
		global $sb;
		$format = $args[1];
		$ops = $args[2];
		if (false !== strpos($model, ".")) {
			$models = explode(".", $model);
			$model = $models[0];
		} else $models = array($model);
		if ((!empty($_POST['action'][$model])) && (empty($sb->errors[$model]))) {
			$id = (!empty($_POST[$model]['id'])) ? $_POST[$model]['id'] : $sb->insert_id;
			if (!empty($ops['where'])) $ops['where'] .= " && $model.id='$id'";
			else $ops['where'] = $model.".id='$id'";
		}
		if (!empty($ops['query'])) {
			$query = base64_decode($ops['query']);
			unset($ops['query']);
			$ops = array_merge($ops, starr::star($query));
		}
		
		$data = $sb->query(implode(",", $models), $ops, true);
		$f = strtoupper($format);
		$error = $f."errors";
		if (empty($sb->errors[$model])) {
			if (!empty($data)) {
				switch ($format) {
					case "xml": return $this->getXML($model, $data); break;
					case "json": return $this->getJSON("id", $data); break;
					case "jsonp": return $this->getJSONP($ops['pad'], $data); break;
				}
			}
		} else return $this->$error($model);
	}

	/**
	 * groups API function - outputs a list of groups
	 * @param string $call function name passed by constructor
	 * @param string $format the desired output format: json, jsonp or xml
	 * @param star $ops additional options
	 */
	protected function groups($call, $format, $ops) {
		global $groups;
		$data = array();
		foreach ($groups as $name => $number) $data[] = array("name" => $name, "id" => $number);
		if (!empty($data)) {
			switch ($format) {
				case "xml": return $this->getXML("groups", $data); break;
				case "json": return $this->getJSON("id", $data); break;
				case "jsonp": return $this->getJSONP($ops['pad'], $data); break;
			}
		}
	}

	/**
	 * statuses API function - outputs a list of statuses
	 * @param string $call function name passed by constructor
	 * @param string $format the desired output format: json, jsonp or xml
	 * @param star $ops additional options
	 */
	protected function statuses($call, $format, $ops) {
		global $statuses;
		$data = array();
		foreach ($statuses as $name => $number) $data[] = array("name" => $name, "id" => $number);
		if (!empty($data)) {
			switch ($format) {
				case "xml": return $this->getXML("statuses", $data); break;
				case "json": return $this->getJSON("id", $data); break;
				case "jsonp": return $this->getJSONP($ops['pad'], $data); break;
			}
		}
	}

	/**
	 * get a json formatted recordset
	 * @param array $data an array of data
	 * @return string json output of records
	 */
	protected function getJSON($identifier, $data) {
		$json = '{ "identifier" : "'.$identifier.'", "items" : [';
		foreach($data as $row) $json .= json_encode($row).", ";
		return rtrim($json, ", ")." ] }";
	}

	/**
	 * get an padded json formatted recordset
	 * @param array $data the data
	 * @param string $pad the string to pad with
	 * @return string padded json string of records
	 */
	protected function getJSONP($pad, $data) {
		return $pad."(".json_encode($data).");";
	}

	/**
	 * Get XML formatted recordset
	 * @param string $root the root node name
	 * @param array $data the data
	 */
	protected function getXML($root, $data) {
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement($root);
		foreach($data as $row) {
			$xml->startElement("item");
			$this->write($xml, $row);
			$xml->endElement();
		}
		$xml->endElement();
		return $xml->outputMemory(true);
	}

	/**
	 * Recursive XML tag writer
	 * @param XMLWriter $xml the XMLWriter instance
	 * @param array $data the data
	 */
	protected function write(XMLWriter $xml, $data){
		foreach($data as $key => $value){
			if(is_array($value)){
				$xml->startElement($key);
				$this->write($xml, $value);
				$xml->endElement();
				continue;
			}
			$xml->writeElement($key, $value);
		}
	}

	/**
	 * get json formatted errors
	 * @param string $model the model
	 * @return string json output of errors
	 */
	protected function JSONerrors($model) {
		global $sb;
		$json = '{ "errors" : [';
		foreach($sb->errors[$model] as $k => $v) {
			$json .= '{ "field":"'.$k.'", "errors": [ ';
			foreach ($v as $e) $json .= '"'.$e.'", ';
			$json = rtrim($json, ", ")." ] }, ";
		}
		return rtrim($json, ", ")." ] }";
	}

}
?>
