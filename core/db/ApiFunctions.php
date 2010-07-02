<?php
/**
* FILE: core/db/ApiFunctions.php
* PURPOSE: Extends the Table to provide data in json and xml
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
class ApiFunctions {
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
	function getJSON($models, $query="") {
		global $sb;
		$from = $models[0];
		if (!empty($query)) $query .= "  ";
		$data = $sb->query(join(",", $models), $query."action:api_get", true);
		$json = '{ "'.$from.'" : [';
		foreach($data as $row) $json .= ApiFunctions::rowToJSON($row).", ";
		return rtrim($json, ", ")." ] }";
	}
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
