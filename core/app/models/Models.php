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
			$dl = "<dl id=\"$prefix-fields\">\n";
			foreach ($arr as $k => $v) {
				$dl .= "\t<dt id=\"$prefix-$k-key\"";
				$dl .= (is_array($v) ? " class=\"sub\"><a class=\"right\" href=\"\" onclick=\"delete_key('$prefix-$k');return false;\">delete</a><a class=\"right\" href=\"\" onclick=\"edit_field('$prefix-$k');return false;\">rename</a><a class=\"right\" href=\"\" onclick=\"new_key('$prefix-$k');return false\">add key</a><a href=\"\" onclick=\"showhide('$prefix-$k-fields');return false;\">$k</a>" : ">$k");
				$dl .= "</dt><dd id=\"$prefix-$k\">".Models::dlfields($v, "$prefix-$k", false)."</dd>\n";
			}
			return $dl."</dl>\n";
		} else return "<span class=\"options\"><a href=\"\" onclick=\"edit_key('$prefix');return false;\">edit</a><a href=\"\" onclick=\"delete_key('$prefix');return false;\">delete</a></span>".$arr;
	}
	
	function remove($loc) {
		$parts = split("-", $loc, 2);
		$filename = $this->schema_dir.$parts[0];
		if (count($parts) == 1) unlink($filename);
		else {
			$arr = split("-", $parts[1]);
			$fields = unserialize(file_get_contents($filename));
			$this->rmloc($fields, $arr);
			$file = fopen($filename, "wb");
			fwrite($file, serialize($fields));
			fclose($file);
		}
	}
	
	function add_field($field, $where) {
		$filename = $this->schema_dir.$where;
		$fields = unserialize(file_get_contents($filename));
		if (!isset($fields[$field])) $fields[$field] = array();
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
	}
	
	function get_field($loc) {
		$parts = split("-", $loc, 2);
		$filename = $this->schema_dir.$parts[0];
		$arr = split("-", $parts[1]);
		$fields = unserialize(file_get_contents($filename));
		$k = end($arr);
		array_pop($arr);
		$keys = "";
		foreach($arr as $key) $keys .= $key."-";
		$keys .= $k;
		return $keys;
	}
	
	function edit_field($field, $where) {
		$parts = split("-", $loc, 2);
		$filename = $this->schema_dir.$parts[0];
		$arr = split("-", $parts[1]);
		$fields = unserialize(file_get_contents($filename));
		$out = $this->rmloc($fields, $arr);
		$merge = array($field => $out);
		end($arr);
		while(($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
	}
	
	function add_key($key, $value, $where) {
		$parts = split("-", $where, 2);
		$filename = $this->schema_dir.$parts[0];
		$arr = split("-", $parts[1]);
		$merge = array(end($arr) => array($key => $value));
		while (($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		$fields = unserialize(file_get_contents($filename));
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
	}
	
	function get_key($loc) {
		$parts = split("-", $loc, 2);
		$filename = $this->schema_dir.$parts[0];
		$arr = split("-", $parts[1]);
		$fields = unserialize(file_get_contents($filename));
		$val = $fields[current($arr)];
		while (($k = next($arr)) !== false) $val = $val[$k];
		return $val;
	}
	
	function edit_key($loc, $value) {
		$parts = split("-", $loc, 2);
		$filename = $this->schema_dir.$parts[0];
		$arr = split("-", $parts[1]);
		$fields = unserialize(file_get_contents($filename));
		$merge = array(end($arr) => $value);
		while(($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		reset($arr);
		$this->rmloc($fields, $arr);
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
	}

	private function rmloc(&$arr, &$locarr) {
		if (($pos = current($locarr)) !== false) {
			if (next($locarr) === false) {
				$rem = $arr[$pos];
				unset($arr[$pos]);
				return $rem;
			} else rmloc($arr[$pos], $locarr);
		}
	}

}
?>
