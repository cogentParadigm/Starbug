<?php
/**
* This is the Migration class, the base class for all migrations.
*
* Starbug - PHP web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
abstract class Migration {

	private $db;

	function Migration($data) {
		$this->db = $data;
	}

	function create_table($name, $fields) {
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
		$this->write_table_schema($name, $fields);
		if (!file_exists(dirname(__FILE__)."/../../app/models/".ucwords($name).".php")) exec(dirname(__FILE__)."/../../script/generate model ".$name);
	}

	function drop_table($name) {$this->db->Execute("DROP TABLE IF EXISTS `".P($name)."`;"); unlink(dirname(__FILE__)."/schema/".ucwords($name));}

	function table_insert($table, $keys, $values) {$this->db->Execute("INSERT INTO ".P($table)." (".$keys.") VALUES (".$values.")");}

	function write_table_schema($name, $fields) {
		$file = fopen(dirname(__FILE__)."/schema/".ucwords($name), "w");
		fwrite($file, serialize($fields));
	}

	abstract function describe();

	abstract function up();

	abstract function down();

}
?>
