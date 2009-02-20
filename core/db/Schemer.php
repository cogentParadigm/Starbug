<?php
/**
* FILE: core/db/Schemer.php
* PURPOSE: This is the Schemer class, it is used for managing the db schema.
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
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
			$type = "int(11)";
			if ($options['type'] == 'string') $type = "varchar(".(isset($options['length'])?$options['length']:"64").")";
			if ($options['type'] == 'password') $type = "varchar(32)";
			else if ($options['type'] == 'text') $type = "text";
			else if ($options['type'] == 'int') $type = "int(".(isset($options['length'])?$options['length']:"11").")";
			else if ($options['type'] == 'datetime') $type = "datetime";
			else if ($options['type'] == 'timestamp') $type = "timestamp";
			$sql .= $fieldname." ".$type." NOT NULL".((!isset($options['default'])) ? "" : " default '".$options['default']."'").", ";
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

	function drop($name) {$this->db->Execute("DROP TABLE IF EXISTS `".P($name)."`;");$this->drop_model($name);}

	function insert($table, $keys, $values) {$this->db->Execute("INSERT INTO ".P($table)." (".$keys.") VALUES (".$values.")");}

	function write_table_schema($name, $fields) {
		$file = fopen(dirname(__FILE__)."/schema/".strtolower($name), "wb");
		fwrite($file, serialize($fields));
		fclose($file);
	}

}
?>
