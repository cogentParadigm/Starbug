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
		global $groups; global $statuses; global $schemer;
		//CREATE MODEL XML
		$xml = "<model name=\"$model\" label=\"".ucwords($model)."\" package=\"".Etc::WEBSITE_NAME."\">\n";
		$relations = array();
		foreach($fields as $name => $field) {
			$xml .= "\t<field name=\"$name\"";
			$xml .= ((isset($schemer->tables[$model][$name])) && ($field['display'] !== "false")) ? ' display="true"' : ' display="false"';
			$kids = "";
			if (!isset($field['input_type'])) {
				if ($field['type'] == "text") $field['input_type'] = "textarea";
				else if ($field['type'] == "password") $field['input_type'] = "password";
				else if ($field['type'] == "bool") $field['input_type'] = "checkbox";
				else if (isset($field['upload'])) $field['input_type'] = "file_select";
				else $field['input_type'] = "text";
			}
			$field[$field['type']] = "";
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
		$permits = query("permits", "where:related_table='".P($model)."'");
		$actions = array();
		foreach ($permits as $p) {
			if (!isset($actions[$p['action']])) $actions[$p['action']] = array();
				if ("object" == $p['priv_type']) $val = $p['related_id'];
				else $val = $p['priv_type'];
				if ($p['status'] != array_sum($statuses)) $val .= " ".$p['status'];
			if ("group" == $p['role']) { //GROUP PERMIT
				$actions[$p['action']][array_search($p['who'], $groups)] = (empty($actions[$p['action']][$groups[$p['who']]])) ? $val : ",".$val;
			} else $actions[$p['action']][$p['role']] = (empty($actions[$p['action']][$p['role']])) ? $val : ",".$val;
		}
		foreach ($actions as $a => $roles) {
			$xml .= "\t<action name=\"$a\"";
			foreach ($roles as $role => $value) $xml .= " $role=\"$value\"";
			$xml .= "/>\n";
		}
		if ($model == "uris") {
			$uris = query("uris");
			foreach($uris as $uri) {
				$xml .= "\t<uri path=\"$uri[path]\"";
				if ($uri['title'] != ucwords(str_replace("-", " ", $uri['path']))) $xml .= " title=\"$uri[title]\"";
				if ($uri['template'] != "templates/View") $xml .= " template=\"$uri[template]\"";
				if ($uri['collective'] != "0") $xml .= " collective=\"$uri[collective]\"";
				if ($uri['status'] != array_sum($statuses)) $xml .= " status=\"$uri[status]\"";
				if ($uri['prefix'] != "app/views/") $xml .= " prefix=\"$uri[prefix]\"";
				if ($uri['check_path'] != "1") $xml .= " check_path=\"$uri[check_path]\"";
				$xml .= "/>\n";
			}
		}
		$xml .= "</model>";
		//WRITE XML
		$file = fopen(BASE_DIR."/var/xml/$model.xml", "wb");
		fwrite($file, $xml);
		fclose($file);
		passthru("chmod 0777 ".BASE_DIR."/var/xml/$model.xml");
	}
}