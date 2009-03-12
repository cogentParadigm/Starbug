<?php
/**
* FILE: core/db/Schemer.php
* PURPOSE: This is the Schemer class, it is used for managing the schema files and db.
*
* This file is part of StarbugPHP
*
* StarbugPHP - meta content manager
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
class Schemer {

	private $db;

	function Schemer($data) {
		$this->db = $data;
	}

	function create($name, $fields, $backup=false) {
		$sql = "DROP TABLE IF EXISTS `".P($name)."`";
		$this->db->Execute($sql);
		$sql = "CREATE TABLE `".P($name)."` (";
		$sql .= "id int(11) NOT NULL AUTO_INCREMENT, ";
		foreach ($fields as $fieldname => $options) {
			$sql .= $fieldname." ".$this->get_sql_type($options).", ";
		}
		$sql .= "security int(2) NOT NULL default '2', PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$result = $this->db->Execute($sql);
		$this->write_model($name, $backup);
	}

	function write_model($name, $backup) {
		$loc = "app/models/".ucwords($name).".php";
		if ($backup) rename("app/models/.".ucwords($name), $loc);
		else if (!file_exists($loc)) exec("script/generate model ".$name);
	}

	function drop_model($name) {
		$model_loc = "app/models/".ucwords($name).".php";
		if (file_exists($model_loc)) {
			$info = unserialize(file_get_contents("core/db/schema/.info/$name"));
			if (filemtime($model_loc) == $info['mtime']) unlink($model_loc);
			else rename($model_loc, "app/models/.".ucwords($name));
		}
	}
	
	function add($table, $name, $field) {
		$sql = $name." ".$this->get_sql_type($field);
		$this->db->Execute("ALTER TABLE ".P($table)." ADD ".$sql);
	}
	
	function remove($table, $name) {
		$this->db->Execute("ALTER TABLE ".P($table)." DROP COLUMN ".$name);
	}
	
	function modify($table, $name, $field) {
		$sql = $name." ".$this->get_sql_type($field);
		$this->db->Execute("ALTER TABLE ".P($table)." ALTER COLUMN ".$sql); 
	}

	function drop($name) {$this->db->Execute("DROP TABLE IF EXISTS `".P($name)."`;");$this->drop_model($name);}

	function insert($table, $keys, $values) {$this->db->Execute("INSERT INTO ".P($table)." (".$keys.") VALUES (".$values.")");}

	function get_sql_type($field) {
		$type = "varchar(64)";
		if ($field['type'] == 'string') $type = "varchar(".(isset($field['length'])?$field['length']:"64").")";
		if ($field['type'] == 'password') $type = "varchar(32)";
		else if ($field['type'] == 'text') $type = "text";
		else if ($field['type'] == 'int') $type = "int(".(isset($field['length'])?$field['length']:"11").")";
		else if ($field['type'] == 'bool') $type = "int(1)";
		else if (($field['type'] == 'datetime') || ($field['type'] == 'timestamp')) $type = "datetime";
		$type = $type." NOT NULL".((!isset($field['default'])) ? "" : " default '".$field['default']."'");
		return $type;
	}

	function get_schemas() {
		$schemas = array();
		if ($handle = opendir("core/db/schema/")) {
			while (false !== ($file = readdir($handle))) if ((substr($file, 0, 1) != ".")) $schemas[$file] = unserialize(file_get_contents("core/db/schema/".$file));
			closedir($handle);
		}
		return $schemas;
	}

	function exists($name) { return file_exists("core/db/schema/$name"); }

	function schema_write($what, $where) {
		$parts = split("-", $where, 2);
		$fields = ($this->exists($parts[0])) ? $this->schema_get($parts[0]) : array();
		if (count($parts) == 1) $merge = $what;
		else {
			$arr = split("-", $parts[1]);
			$merge = array(end($arr) => $what);
			while (($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		}
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen("core/db/schema/".$parts[0], "wb");	
		fwrite($file, serialize($fields));
		fclose($file);
	}

	function schema_get($where) {
		$parts = split("-", $where, 2);
		$val = unserialize(file_get_contents("core/db/schema/".$parts[0]));
		if (count($parts) > 1) {
			$val = $val[current($arr)];
			while (($k = next($arr)) !== false) $val = $val[$k];
		}
		return $val;
	}

	function schema_edit($new, $where) {
		$parts = split("-", $where, 2);
		$fields = $this->schema_get($where);
		$arr = split("-", $parts[1]);
		$val = $this->rmloc($fields, $arr);
		$key = end($arr);
		$merge = (is_array($out)) ? array($new => $val) : array($key => $new);
		while(($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen("core/db/schema/".$parts[0]);
	}

	function schema_remove($loc) {
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
