<?php
include("core/db/Schemer.php");
class Models {

	var $schema_dir; // "core/db/schema/"

	function Models($d) {
		$this->schema_dir = $d;
	}

	function create() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array("securityError");
		$filename = $this->schema_dir.$_POST['modelname'];
		$fields = serialize(array());
		if (!file_exists($filename)) {
			$file = fopen($filename, "wb");
			fwrite($file, $fields);
			fclose($file);
		} else return array("fileExistsError" => true);
		return array();
	}

	function get_all() {
		$models = array();
		if ($handle = opendir($this->schema_dir)) {
			while (false !== ($file = readdir($handle))) if ((substr($file, 0, 1) != ".")) $models[$file] = unserialize(file_get_contents($this->schema_dir.$file));
			closedir($handle);
		}
		return $models;
	}

	function get($name) {
		return unserialize(file_get_contents($this->schema_dir.$name));
	}

	function activate($name, $db) {
		$fields = unserialize(file_get_contents($this->schema_dir.$name));
		$schemer = new Schemer($db);
		$schemer->create($name, $fields, $_POST['restore_backup']);
	}

	function deactivate($name, $db) {
		$schemer = new Schemer($db);
		$schemer->drop($name);
	}

	function dlfields($arr, $prefix, $top=true) {
		if (is_array($arr)) {
			$dl = "<dl id=\"$prefix-fields\"".(($top)?"":" class=\"hidden\"").">\n";
			foreach ($arr as $k => $v) {
				$dl .= "\t<dt id=\"$prefix-$k-key\"";
				$dl .= (is_array($v) ? " class=\"sub\"><a class=\"right\" href=\"\" onclick=\"delete_key('$prefix-$k');return false;\">delete</a><a class=\"right\" href=\"\" onclick=\"edit_field('$prefix-$k');return false;\">rename</a><a class=\"right\" href=\"\" onclick=\"new_key('$prefix-$k');return false\">add key</a><a href=\"\" onclick=\"showhide('$prefix-$k-fields');return false;\">$k</a>" : ">$k");
				$dl .= "</dt><dd id=\"$prefix-$k\">".Models::dlfields($v, "$prefix-$k", false)."</dd>\n";
			}
			return $dl."</dl>\n";
		} else return "<span class=\"options\"><a href=\"\" onclick=\"edit_key('$prefix');return false;\">edit</a><a href=\"\" onclick=\"if (confirm('Are you sure you want to delete?')) {delete_key('$prefix');}return false;\">delete</a></span>".$arr;
	}

}
?>