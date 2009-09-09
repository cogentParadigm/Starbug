<?php
include("core/db/Schemer.php");
class Models {

	var $schemer;

	function Models($db) { $this->schemer = new Schemer($db); }

	function create($name) {
		if ($this->schemer->exists($name)) return array("fileExistsError" => true);
		else $this->schemer->schema_write(array(), $name);
		return array();
	}

	function get_all() { return $this->schemer->get_schemas(); }

	function get($name) { return $this->schemer->schema_get($name); }

	function activate($name, $backup=false) {
		$parts = split("-", $name);
		if (count($parts) == 1) $this->schemer->create($name, $backup);
		else $this->schemer->add($parts[0], $parts[1]);
	}

	function deactivate($name) { $this->schemer->drop($name); }
	
	function is_active($name) {
		$info = unserialize(file_get_contents("var/schema/.info/$name"));
		return (!empty($info['active']));
	}

	function dlfields($arr, $prefix, $has) {
		if (is_array($arr)) {
			$dl = "<dl id=\"$prefix-fields\">\n";
			foreach ($arr as $k => $v) {
				$dl .= "\t<dt id=\"$prefix-$k-key\"";
				if (is_array($v))  {
					if (!empty($v["inactive"])) {
						$inact = " inactive";
						unset($v['inactive']);
					} else $inact = "";
					$dl .= " class=\"sub$inact\"><a class=\"right\" href=\"\" onclick=\"delete_key('$prefix-$k');return false;\">delete</a><a class=\"right\" href=\"\" onclick=\"edit_field('$prefix-$k');return false;\">rename</a><a class=\"right\" href=\"\" onclick=\"new_key('$prefix-$k');return false\">add key</a>".((!empty($inact) && $has)?"<form style=\"display:none\" id=\"activate_$prefix-$k\"><input type=\"hidden\" name=\"activate_field\" value=\"1\"/></form><a class=\"right\" href=\"\" onclick=\"activate_field('$prefix', '$k');return false;\">activate</a>":"")."<a href=\"\" onclick=\"showhide('$prefix-$k-fields');return false;\">$k</a>";
				} else $dl .= ">$k";
				$dl .= "</dt><dd id=\"$prefix-$k\"".(!empty($inact)?" class=\"".trim($inact)."\"":"").">".Models::dlfields($v, "$prefix-$k", $has)."</dd>\n";
			}
			return $dl."</dl>\n";
		} else return "<span class=\"options\"><a href=\"\" onclick=\"edit_key('$prefix');return false;\">edit</a><a href=\"\" onclick=\"delete_key('$prefix');return false;\">delete</a></span>".$arr;
	}

	function add_field($field, $where) { $this->schemer->schema_write(array($field => array("inactive" => true)), $where); }

	function add_key($key, $value, $where) { $this->schemer->schema_write(array($key => $value), $where); }	

	function edit($new, $where) {	$this->schemer->schema_edit($new, $where); }
	
	function remove($loc) { $this->schemer->schema_remove($loc); }

}
?>
