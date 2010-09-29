<?php
/**
* FILE: core/db/Schemer.php
* PURPOSE: This is the Schemer class, it is used for managing the schema files and db.
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
include("core/db/Migration.php");
class Schemer {

	var $db;
	var $tables = array();
	var $table_drops = array();
	var $column_drops = array();
	var $migrations = array();

	function Schemer($data) {
		$this->db = $data;
	}
	//RUN SQL TO MATCH SCHEMA
	function update() {
		$ts = 0; //tables
		$cs = 0; //cols
		$ms = 0; //mods
		$ds = 0; //drops
		foreach($this->tables as $table => $fields) {
			$records = $this->db->query("SHOW TABLES LIKE '".P($table)."'");
			if (false === ($row = $records->fetch())) {
				//NEW TABLE
				fwrite(STDOUT, "Creating table ".P($table)."...\n");
				$this->create($table);
				$ts++;
			} else {
				//OLD TABLE
				foreach($fields as $name => $field) {
					$records = $this->db->query("SHOW COLUMNS FROM ".P($table)." WHERE Field='".$name."'");
					if (false === ($row = $records->fetch())) {
						//NEW COLUMN
						fwrite(STDOUT, "Adding column $name...\n");
						$this->add($table, $name);
						$cs++;
					} else {
						//OLD COLUMN
						$type = explode(" ", $this->get_sql_type($field));
						if (($row['Type'] != $type[0]) || ((!empty($field['default'])) && ($row['Default'] != $field['default']))) {
							fwrite(STDOUT, "Altering column $name...\n");
							$this->modify($table, $name);
							$ms++;
						}
					}
					if (isset($field['references'])) {
						$fks = $this->db->query("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='".$table."_".$name."_fk'");
						if (false === ($row = $fks->fetch())) {
							//ADD CONSTRAINT
							fwrite(STDOUT, "Adding foreign key ".$table."_".$name."_fk...\n");
							$this->add_foreign_key($table, $name);
						}
					}
				}
			}
		}
		foreach($this->table_drops as $table) {
			$records = $this->db->query("SHOW TABLES LIKE '".P($table)."'");
			if ($row = $records->fetch()) {
				//DROP TABLE
				fwrite(STDOUT, "Dropping table ".P($table)."...\n");
				$this->drop_table($table);
				$ds++;
			}
		}
		foreach($this->column_drops as $table => $cols) {
			$records = $this->db->query("SHOW TABLES LIKE '".P($table)."'");
			if ($row = $records->fetch()) {
				foreach($cols as $col) {
					$records = $this->db->query("SHOW COLUMNS FROM ".P($table)." WHERE field='".$col."'");
					if ($row = $records->fetch()) {
						//DROP COLUMN
						fwrite(STDOUT, "Dropping column ".P($table).".$col...\n");
						$this->remove($table, $col);
						$ds++;
					}
				}
			}
		}
		if (($ts == 0) && ($cs == 0) && ($ms == 0) && ($ds == 0)) fwrite(STDOUT, "The Database already matches the schema\n");
	}
	//RUN SQL TO CREATE TABLE
	function create($name, $backup=false, $write=true) {
		$fields = $this->tables[$name];
		$this->drop_table($name);
		$sql = "CREATE TABLE `".P($name)."` (";
		$primary = array();
		$index = array();
		$foreign = array();
		$sql_fields = "";
		foreach ($fields as $fieldname => $options) {
			$sql_fields .= "`".$fieldname."` ".$this->get_sql_type($options).", ";
			if (!empty($options['key'])) {
				if ($options['key'] == "primary") $primary[] = "`$fieldname`"; 
			}
			if (isset($options['index'])) $index[] = $fieldname;
			if (!empty($options['references'])) {
				$ref = explode(" ", $options['references']);
				$rec = array("table" => $ref[0], "column" => $ref[1]);
				if (!empty($options['update'])) $rec['update'] = $options['update'];
				if (!empty($options['delete'])) $rec['delete'] = $options['delete'];
				$foreign[$fieldname] = $rec;
			}
		}
		if (empty($primary)) {
			$sql_fields = "id int(11) NOT NULL AUTO_INCREMENT, ".$sql_fields;
			$primary = "`id`";
		} else $primary = join(", ", $primary);
		$sql .= $sql_fields."owner int(11) NOT NULL default '1', collective int(11) NOT NULL default '1', status int(11) NOT NULL default '4', ";
		$sql .= "created datetime not null default '0000-00-00 00:00:00', modified datetime not null default '0000-00-00 00:00:00', ";
		$sql .= "PRIMARY KEY ($primary), KEY `owner` (`owner`)";
		foreach($index as $k) $sql .= ", KEY `".$k."` (`$k`)";
		foreach($foreign as $k => $v) $sql .= ", KEY `".$k."` (`$k`)";
		$sql .= ", CONSTRAINT `".$name."_owner_fk` FOREIGN KEY (`owner`) REFERENCES `sb_users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE";
		foreach($foreign as $k => $v) {
			$sql .=", CONSTRAINT `".$name."_".$k."_fk` FOREIGN KEY (`$k`) REFERENCES `".P($v['table'])."` (`".$v['column']."`)";
			if ($v['update']) $sql .= " ON UPDATE ".$v['update'];
			if ($v['delete']) $sql .= " ON DELETE ".$v['delete'];
		}
		$result = $this->db->exec($sql." ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		//if ($write) $this->write_model($name, $backup);
	}
	//RUN SQL TO DROP TABLE
	function drop_table($name) {
		$this->db->exec("DROP TABLE IF EXISTS `".P($name)."`");
		//$this->drop_model($name);
	}
	//RUN SQL TO ADD COLUMN
	function add($table, $name) {
		$fields = $this->tables[$table];
		$field = $fields[$name];
		$sql = $name." ".$this->get_sql_type($field);
		$this->db->exec("ALTER TABLE `".P($table)."` ADD ".$sql);
	}
	//RUN SQL TO REMOVE COLUMN
	function remove($table, $name) {
		$this->db->exec("ALTER TABLE `".P($table)."` DROP COLUMN ".$name);
	}
	//RUN SQL TO ALTER COLUMN
	function modify($table, $name) {
		$field = $this->tables[$table][$name];
		$sql = $name." ".$name." ".$this->get_sql_type($field);
		$this->db->exec("ALTER TABLE `".P($table)."` CHANGE ".$sql); 
	}
	function add_foreign_key($table, $name) {
		$field = $this->tables[$table][$name];
		$ref = explode(" ", $field['references']);
		$append = "";
		if ($field['update']) $append .= " ON UPDATE ".$field['update'];
		if ($field['delete']) $append .= " ON DELETE ".$field['delete'];
		$this->db->exec("ALTER TABLE `".P($table)."` ADD CONSTRAINT `".$table."_".$name."_fk` FOREIGN KEY (`$name`) REFERENCES `".P($ref[0])."` (`".$ref[1]."`)".$append);
	}
	//ADD TABLE TO DESCRIPTION
	function table($arg) {
		$args = func_get_args();
		$name = array_shift($args);
		$this->tables[$name] = array();
		foreach($args as $field) $this->column($name, $field);
	}
	//ADD COLUMN TO DESCRIPTION
	function column($table, $col) {
		$col = starr::star($col);
		$colname = array_shift($col);
		$this->tables[$table][$colname] = $col;
	}
	//DROP TABLE OR COLUMN FROM DESCRIPTION
	function drop($table, $col="") {
		if (empty($col)) {
			$this->table_drops[] = $table;
			unset($this->tables[$table]);
		} else {
			if (!isset($this->column_drops[$table])) $this->column_drops[$table] = array();
			$this->column_drops[$table][] = $col;
			unset($this->tables[$table][$col]);
		}
	}
	function insert($table, $keys, $values) {$this->db->query("INSERT INTO `".P($table)."` (".$keys.") VALUES (".$values.")");}

	function get_sql_type($field) {
		$type = "varchar(64)";
		if ($field['type'] == 'string') $type = "varchar(".(isset($field['length'])?$field['length']:"64").")";
		if ($field['type'] == 'password') $type = "varchar(32)";
		else if (($field['type'] == 'text') || ($field['type'] == 'longtext')) $type = $field["type"];
		else if ($field['type'] == 'int') $type = "int(".(isset($field['length'])?$field['length']:"11").")";
		else if ($field['type'] == 'bool') $type = "int(1)";
		else if (($field['type'] == 'datetime') || ($field['type'] == 'timestamp')) $type = "datetime";
		$type = $type." NOT NULL".((isset($field['auto_increment'])) ? " AUTO_INCREMENT" : "").((!isset($field['default'])) ? "" : " default '".$field['default']."'");
		return $type;
	}

	function write_model($name, $backup) {
		$loc = "app/models/".ucwords($name).".php";
		if ($backup) rename("app/models/.".ucwords($name), $loc);
		else if (!file_exists($loc)) exec("./script/generate model ".$name);
		chmod($loc, 0666);
	}

	function drop_model($name) {
		$model_loc = "app/models/".ucwords($name).".php";
		if (file_exists($model_loc)) {
			$info = unserialize(file_get_contents("var/schema/.info/$name"));
			if (filemtime($model_loc) == $info['mtime']) unlink($model_loc);
			else rename($model_loc, "app/models/.".ucwords($name));
		}
	}
	
	function add_migrations($arg) {
		$args = func_get_args();
		foreach($args as $i => $a) {
			include(BASE_DIR."/etc/migrations/$a.php");
		}
		$this->migrations = array_merge($this->migrations, $args);
	}
	
	function migrate($to="top", $from="current") {
		global $sb;
		$current_op = $sb->query("options", "select:id,value  where:name='migration'  limit:1");
		if (empty($to)) $to = "top";
		if ($to == "top") $to = count($this->migrations);
		if ($from == "current") $from = $current_op['value'];
		//MOVE TO FROM
		$current = 0;
		while ($current < $from) {
			$migration = new $this->migrations[$current]();
				$migration->up();
				$current++;
		}
		//MIGRATE
		if ($to < $from) { //DOWN
			while($current > $to) {
				$migration = new $this->migrations[$current-1]();
				$migration->down();
				$current--;
			}
			$this->update();
			$current = $from;
			while($current > $to) {
				$migration = new $this->migrations[$current-1]();
				$migration->removed();
				$current--;
			}
		} else {  //UP
			while($current < $to) {
				$migration = new $this->migrations[$current]();
				$migration->up();
				$current++;
			}
			$this->update();
			$current = $from;
			while($current < $to) {
				$migration = new $this->migrations[$current]();
				$migration->created();
				$current++;
			}
		}
		//UPDATE CURRENT
		$sb->store("options", "id:$current_op[id]  value:$to");
	}

}
?>
