<?php
/**
 * @file util/XMLBuilder.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup XMLBuilder
 */
/**
 * @defgroup XMLBuilder
 * XMLBuilder utility
 * @ingroup util
 */
$sb->provide("util/XMLBuilder");
/**
 * writes data in xml format
 * @ingroup XMLBuilder
 */
class XMLBuilder {
	function write_model($model, $fields) {
		//CREATE MODEL XML
		$xml = "<model name=\"$model\" label=\"".ucwords($model)."\" package=\"".Etc::WEBSITE_NAME."\">\n";
		$relations = array();
		foreach($fields as $name => $field) {
			$xml .= "\t<field name=\"$name\"";
			$kids = "";
			if (!isset($field['input_type'])) {
				if ($field['type'] == "text") $field['input_type'] = "textarea";
				else if ($field['type'] == "password") $field['input_type'] = "password";
				else if ($field['type'] == "bool") $field['input_type'] = "checkbox";
				else if ($field['type'] == "datetime") $field['input_type'] = "date_select";
				else if (isset($field['upload'])) $field['input_type'] = "file";
				else $field['input_type'] = "text";
			}
			if ($field['input_type'] == "file") $xml .= " multipart=\"true\"";
			foreach ($field as $k => $v) {
				if (("references" == $k) && (false === strpos($v, $model))) {
					$ref = explode(" ", $v);
					$kids .= "\t\t<references model=\"$ref[0]\" field=\"$ref[1]\"/>\n";
				}
				if (file_exists(BASE_DIR."/core/app/filters/store/$k.php")) $kids .= "\t\t<filter name=\"$k\" value=\"$v\"/>\n";
				else $xml .= " $k=\"$v\"";
			}
			if (empty($kids)) $xml .= "/>\n"; else $xml .= ">\n$kids\t</field>\n";
		}
		foreach ($schemer->tables as $table => $fields) {
			$relations = $schemer->get_relations($table, $model);
			foreach ($relations as $m => $r) {
				$xml .= "\t<relation model=\"$m\" field=\"$r[hook]\"".((!empty($r['lookup'])) ? " lookup=\"$r[lookup]\" ref_field=\"$r[ref_field]\"" : "")."/>\n";
			}
		}
		$xml .= "</model>";
		//WRITE XML
		$file = fopen("var/xml/$model.xml", "wb");
		fwrite($file, $xml);
		fclose($file);
		chmod("var/xml/$model.xml", 0777);
	}
}