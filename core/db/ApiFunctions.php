<?php
/**
 * This file is part of StarbugPHP
 * @file core/db/ApiFunctions.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * ApiFunctions class. provides data in xml and json format
 * @ingroup db
 */
class ApiFunctions {

	/**
	 * get an xml formatted recordset
	 * @param string $models a comma delimited list of models
	 * @param string $query the parameters of the query
	 * @return string xml output of records
	 */
	public function getXML($models, $query="") {
		global $sb;
		$from = $models[0];
		if (!empty($query)) $query .= "  ";
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement($from);
		$data = $sb->query(join(",", $models), $query."action:api_get", true);
		foreach($data as $row) {
			$xml->startElement("entry");
			ApiFunctions::write($xml, $row);
			$xml->endElement();
		}
		$xml->endElement();
		return $xml->outputMemory(true);
	}

	/**
	 * get a json formatted recordset
	 * @param string $models a comma delimited list of models
	 * @param string $query the parameters of the query
	 * @return string json output of records
	 */
	function getJSON($models, $query="") {
		global $sb;
		$from = $models[0];
		if (!empty($query)) $query .= "  ";
		$data = $sb->query(join(",", $models), $query."action:api_get", true);
		$json = '{ "'.$from.'" : [';
		foreach($data as $row) $json .= ApiFunctions::rowToJSON($row).", ";
		return rtrim($json, ", ")." ] }";
	}

	/**
	 * get an padded json formatted recordset
	 * @param string $models a comma delimited list of models
	 * @param string $query the parameters of the query
	 * @return string padded json string of records
	 */
	function getJSONP($models, $pad, $query="") {
		echo "Test";
		global $sb;
		if (!empty($query)) $query .= "  ";
		$data = $sb->query(join(",", $models), $query."action:api_get", true);
		return $pad."(".json_encode($data).");";
	}

	protected function write(XMLWriter $xml, $data){
		foreach($data as $key => $value){
			if(is_array($value)){
				$xml->startElement($key);
				ApiFunctions::write($xml, $value);
				$xml->endElement();
				continue;
			}
			$xml->writeElement($key, $value);
		}
	}

	protected function rowToJSON($row) {
		$json = "{";
		foreach($row as $k => $v) $json .= '"'.$k.'"'.' : "'.str_replace(array("{", "}"), array("", ""), addslashes($v)).'", ';
		return rtrim($json, ", ")."}";
	}
}
?>
