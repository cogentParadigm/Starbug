<?php
include("core/db/Schemer.php");
class Models {

	var $schemer;

	function Models($d) { $this->$schemer = new Schemer($db); }

	function create($name) {
		if ($this->schemer->exists($_POST['modelname'])) return array("fileExistsError" => true);
		else $this->schemer->write_schema($_POST['modelname'], array());
		return array();
	}

	function get_all() { return $this->schemer->get_schemas(); }

	function get($name) { return $this->schemer->schema_get($name); }

	function activate($name, $backup) { $this->schemer->create($name, $this->schemer->get_schema($name), $backup); }

	function deactivate($name) { $this->schemer->drop($name); }

	function dlfields($arr, $prefix, $top=true) {
		if (is_array($arr)) {
			$dl = "<dl id=\"$prefix-fields\">\n";
			foreach ($arr as $k => $v) {
				$dl .= "\t<dt id=\"$prefix-$k-key\"";
				$dl .= (is_array($v) ? " class=\"sub\"><a class=\"right\" href=\"\" onclick=\"delete_key('$prefix-$k');return false;\">delete</a><a class=\"right\" href=\"\" onclick=\"edit_field('$prefix-$k');return false;\">rename</a><a class=\"right\" href=\"\" onclick=\"new_key('$prefix-$k');return false\">add key</a><a href=\"\" onclick=\"showhide('$prefix-$k-fields');return false;\">$k</a>" : ">$k");
				$dl .= "</dt><dd id=\"$prefix-$k\">".Models::dlfields($v, "$prefix-$k", false)."</dd>\n";
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
