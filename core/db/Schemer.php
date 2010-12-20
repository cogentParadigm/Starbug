<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/db/Schemer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
include("core/db/Migration.php");
/**
 * The Schemer class. Manages a schema of the database using migrations and handles synching a database with the schema
 * @ingroup db
 */
class Schemer {
	/**
	 * @var db The db class is a PDO wrapper
	 */
	var $db;
	/**
	 * @var array Holds tables, columns and column options
	 */
	var $tables = array();
	/**
	 * @var array Tables that have been dropped
	 */
	var $table_drops = array();
	/**
	 * @var array Columns that have been dropped
	 */
	var $column_drops = array();
	/**
	 * @var array Holds uris
	 */
	var $uris = array();
	/**
	 * @var array Holds permits
	 */
	var $permits = array();
	/**
	 * @var array Ordered list of migrations
	 */
	var $migrations = array();
	/**
	 * @var array Holds records to be inserted immediately after a table is created
	 */
	var $population = array();

	/**
	 * constructor. loads migrations
	 */
	function __construct($data) {
		global $sb;
		$this->db = $data;
		$this->migrations = $sb->publish("migrations");
		foreach($this->migrations as $i => $a) {
			include(BASE_DIR."/app/migrations/$a.php");
		}
	}

	/**
	 * Get the schema of a table
	 * @param string $table the name of the table
	 * @return array the table schema with default fields added
	 */
	function get_table($table) {
		$fields = $this->tables[$table];
		$primary = array();
		foreach ($fields as $column => $options) {
			if ((isset($options['key'])) && ("primary" == $options['key'])) $primary[] = $column;
		}
		if (empty($primary)) $fields['id'] = star("type:int  auto_increment:  key:primary");
		if (empty($fields["owner"])) $fields["owner"] = star("type:int  default:1  references:users id");
		if (empty($fields["collective"])) $fields["collective"] = star("type:int  default:1");
		if (empty($fields["status"])) $fields["status"] = star("type:int  default:4");
		if (empty($fields["created"])) $fields["created"] = star("type:datetime  default:0000-00-00 00:00:00");
		if (empty($fields["modified"])) $fields["modified"] = star("type:datetime  default:0000-00-00 00:00:00");
		return $fields;
	}

	/**
	 * Updates the DB to match the schema state
	 */
	function update() {
		$ts = 0; //tables
		$cs = 0; //cols
		$ms = 0; //mods
		$ds = 0; //drops
		$us = 0; //uris
		$ps = 0; //permits
		$is = 0; //inserts
		foreach ($this->tables as $table => $fields) {
			$fields = $this->get_table($table);
			$records = $this->db->query("SHOW TABLES LIKE '".P($table)."'");
			if (false === ($row = $records->fetch())) {
				// NEW TABLE																																													// NEW TABLE
				fwrite(STDOUT, "Creating table ".P($table)."...\n");
				$this->create($table);
				$ts++;
			} else {
				// OLD TABLE																																													// OLD TABLE
				foreach ($fields as $name => $field) {
					$records = $this->db->query("SHOW COLUMNS FROM ".P($table)." WHERE Field='".$name."'");
					if (false === ($row = $records->fetch())) {
						// NEW COLUMN																																											// NEW COLUMN
						fwrite(STDOUT, "Adding column $name...\n");
						$this->add($table, $name);
						$cs++;
					} else {
						// OLD COLUMN																																											// OLD COLUMN
						$type = explode(" ", $this->get_sql_type($field));
						if (($row['Type'] != $type[0]) || ((!empty($field['default'])) && ($row['Default'] != $field['default']))) {
							fwrite(STDOUT, "Altering column $name...\n");
							$this->modify($table, $name);
							$ms++;
						}
					}
					if (isset($field['references']) && ($field['constraint'] != "false")) {
						$fks = $this->db->query("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME='".P($table)."_".$name."_fk'");
						if (false === ($row = $fks->fetch())) {
							// ADD CONSTRAINT																																								// CONSTRAINT
							fwrite(STDOUT, "Adding foreign key ".P($table)."_".$name."_fk...\n");
							$this->add_foreign_key($table, $name);
							$ms++;
						}
					}
				}
			}
			$is += $this->populate($table);
		}
		foreach ($this->uris as $path => $uri) {
			$rows = query("uris", "where:path='$path'");
			if (empty($rows)) {
				// ADD URI																																														// ADD URI
				fwrite(STDOUT, "Adding URI '$path'...\n");
				$this->add_uri($path);
				$us++;
			} else {
				$query = ''; foreach ($uri as $k => $v) $query .= $k."='$v' && ";
				$rows = query("uris", "where:".rtrim($query, '& '));
				if (empty($rows)) {
					// UPDATE URI																																												// UPDATE URI
					fwrite(STDOUT, "Updating URI '$path'...\n");
					$this->update_uri($path);
					$us++;
				}
			}
		}
		foreach ($this->permits as $model => $actions) {
			foreach ($actions as $action => $roles) {
				foreach ($roles as $role => $ops) {
					$permits = $this->get_permits($model, $action, $role);
					foreach ($permits as $permit) {
						$query = ''; foreach ($permit as $k => $v) if ("status" != $k) $query .= $k."='$v' && ";
						$row = query("permits", "where:".rtrim($query, '& ')."  limit:1");
						if (empty($row)) {
							// ADD PERMIT																																										// ADD PERMIT
							fwrite(STDOUT, "Adding $permit[priv_type] permit on $model::$action for $role...\n");
							$this->add_permit($permit);
							$ps++;
						} else if ($row['status'] != $permit['status']) {
							// UPDATE PERMIT																																								// UPDATE PERMIT
							fwrite(STDOUT, "Updating $permit[priv_type] permit on $model::$action for $role...\n");
							$this->update_permit($permit);
							$ps++;
						}
					}
				}
			}
		}
		foreach ($this->table_drops as $table) {
			$records = $this->db->query("SHOW TABLES LIKE '".P($table)."'");
			if ($row = $records->fetch()) {
				// DROP TABLE																																													// DROP TABLE
				fwrite(STDOUT, "Dropping table ".P($table)."...\n");
				$this->drop_table($table);
				$ds++;
			}
		}
		foreach ($this->column_drops as $table => $cols) {
			$records = $this->db->query("SHOW TABLES LIKE '".P($table)."'");
			if ($row = $records->fetch()) {
				foreach($cols as $col) {
					$records = $this->db->query("SHOW COLUMNS FROM ".P($table)." WHERE field='".$col."'");
					if ($row = $records->fetch()) {
						// DROP COLUMN																																										// DROP COLUMN
						fwrite(STDOUT, "Dropping column ".P($table).".$col...\n");
						$this->remove($table, $col);
						$ds++;
					}
				}
			}
		}
		if (($ts == 0) && ($cs == 0) && ($ms == 0) && ($ds == 0) && ($us == 0) && ($ps == 0) && ($is == 0)) fwrite(STDOUT, "The Database already matches the schema\n");
		else {
			fwrite(STDOUT, "Generating models (this may take a minute)...\n");
			passthru("sb generate models");
		}
		passthru("sb generate css");
	}

	/**
	 * Run SQL to create a table in the DB from the schema
	 * @param string $name the name of the table from tables array
	 */
	function create($name, $backup=false, $write=true) {
		$fields = $this->get_table($name);
		$this->drop_table($name);
		$sql = "CREATE TABLE `".P($name)."` (";
		$primary = array();
		$index = array();
		$foreign = array();
		$sql_fields = "";
		$primary_fields = "";
		foreach ($fields as $fieldname => $options) {
			$field_sql = "`".$fieldname."` ".$this->get_sql_type($options).", ";
			if (isset($options['key']) && ("primary" == $options['key'])) {
				$primary[] = "`$fieldname`";
				$primary_fields .= $field_sql;
			} else $sql_fields .= $field_sql;
			if (isset($options['index'])) $index[] = $fieldname;
			if (!empty($options['references'])) {
				$ref = explode(" ", $options['references']);
				$rec = array("table" => $ref[0], "column" => $ref[1]);
				if (!empty($options['update'])) $rec['update'] = $options['update'];
				if (!empty($options['delete'])) $rec['delete'] = $options['delete'];
				$foreign[$fieldname] = $rec;
			}
		}
		$primary = join(", ", $primary);
		$sql .= $primary_fields.$sql_fields."PRIMARY KEY ($primary)";
		foreach($index as $k) $sql .= ", KEY `".$k."` (`$k`)";
		foreach($foreign as $k => $v) $sql .= ", KEY `".$k."` (`$k`)";
		foreach($foreign as $k => $v) {
			$sql .=", CONSTRAINT `".P($name)."_".$k."_fk` FOREIGN KEY (`$k`) REFERENCES `".P($v['table'])."` (`".$v['column']."`)";
			if ($v['update']) $sql .= " ON UPDATE ".$v['update'];
			if ($v['delete']) $sql .= " ON DELETE ".$v['delete'];
		}
		$result = $this->db->exec($sql." ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		//if ($write) $this->write_model($name, $backup);
	}

	/**
	 * run SQL to drop a table
	 * @param string $name the name of the table from tables array
	 */
	function drop_table($name) {
		$this->db->exec("DROP TABLE IF EXISTS `".P($name)."`");
		//$this->drop_model($name);
	}

	/**
	 * Run SQL to add a column to the DB from the schema
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function add($table, $name) {
		$fields = $this->get_table($table);
		$field = $fields[$name];
		$sql = $name." ".$this->get_sql_type($field);
		$this->db->exec("ALTER TABLE `".P($table)."` ADD ".$sql);
	}

	/**
	 * Run SQL to drop a column
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function remove($table, $name) {
		$this->db->exec("ALTER TABLE `".P($table)."` DROP COLUMN ".$name);
	}

	/**
	 * Run SQL to alter a column in the DB to match the schema
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function modify($table, $name) {
		$fields = $this->get_table($table);
		$field = $fields[$name];
		$sql = $name." ".$name." ".$this->get_sql_type($field);
		$this->db->exec("ALTER TABLE `".P($table)."` CHANGE ".$sql); 
	}

	/**
	 * Run SQL to add a foreign key to the DB from the schema
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function add_foreign_key($table, $name) {
		$fields = $this->get_table($table);
		$field = $fields[$name];
		$ref = explode(" ", $field['references']);
		$append = "";
		if ($field['update']) $append .= " ON UPDATE ".$field['update'];
		if ($field['delete']) $append .= " ON DELETE ".$field['delete'];
		$this->db->exec("ALTER TABLE `".P($table)."` ADD CONSTRAINT `".P($table)."_".$name."_fk` FOREIGN KEY (`$name`) REFERENCES `".P($ref[0])."` (`".$ref[1]."`)".$append);
	}

	/**
	 * Get an SQL type string for a column
	 * @param array $field the column options from the schema
	 */
	function get_sql_type($field) {
		$type = "varchar(64)";
		if ($field['type'] == 'string') $type = "varchar(".(isset($field['length'])?$field['length']:"64").")";
		if ($field['type'] == 'password') $type = "varchar(32)";
		else if (($field['type'] == 'text') || ($field['type'] == 'longtext')) $type = $field["type"];
		else if ($field['type'] == 'int') $type = "int(".(isset($field['length'])?$field['length']:"11").")";
		else if ($field['type'] == 'decimal') $type = "decimal(".$field['length'].")";
		else if ($field['type'] == 'bool') $type = "tinyint(1)";
		else if (($field['type'] == 'datetime') || ($field['type'] == 'timestamp')) $type = "datetime";
		$type .= ((isset($field['null'])) ? " NULL" : " NOT NULL")
						.((isset($field['unsigned'])) ? " UNSIGNED" : "")
						.((isset($field['zerofill'])) ? " ZEROFILL" : "")
						.((isset($field['auto_increment'])) ? " AUTO_INCREMENT" : "")
						.((isset($field['unique'])) ? " UNIQUE" : "")
						.((!isset($field['default'])) ? "" : " default '".$field['default']."'");
		return $type;
	}

	/**
	 * Add a table to the schema
	 * @param string $arg0 the name of the table
	 * @param string $arg1-$arg(N-1) A star formatted column string
	 */
	function table($arg) {
		$args = func_get_args();
		$name = array_shift($args);
		$this->tables[$name] = array();
		foreach($args as $field) $this->column($name, $field);
	}

	/**
	 * Add column to schema
	 * @param string $table the name of the table
	 * @param string $col A star formatted column string
	 */
	function column($table, $col) {
		$col = starr::star($col);
		$colname = array_shift($col);
		$this->tables[$table][$colname] = $col;
	}

	/**
	 * Drop table or column from schema
	 * @param string $table the name of the table
	 * @param string $col (optional) column name
	 */
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

	/**
	 * Add a uri to the db from the schema
	 * @param string $path the path of the uri
	 */
	function add_uri($path) {
		$uri = $this->uris[$path];
		$errors = store("uris", $uri);
	}

	/**
	 * Update a uri in the db from the schema
	 * @param string $path the path of the uri
	 */
	function update_uri($path) {
		$uri = $this->uris[$path];
		store("uris", $uri, "path:$path");
	}

	/**
	 * Add a uri to the schema
	 * @param string $path the path
	 * @param star $args other fields
	 */
	function uri($path, $args) {
		global $statuses;
		$args = starr::star($args);
		$args['path'] = $path;
		efault($args['title'], ucwords(str_replace("-", " ", $path)));
		efault($args['template'], "templates/View");
		efault($args['collective'], 0);
		efault($args['status'], array_sum($statuses));
		$this->uris[$path] = $args;
	}

	/**
	 * Add a permit to the db from the schema
	 * @param array $permit the permit to add
	 */
	function add_permit($permit) {
		store("permits", $permit);
	}

	/**
	 * Update a permit in the db from the schema
	 * @param string $permit the updated record
	 */
	function update_permit($permit) {
		$old = $permit;
		unset($old['status']);
		store("permits", $permit, $old);
	}

	/**
	 * Add a permit to the schema
	 * @param string $on the model and action to apply the permit on
	 * @param star $args a field string where keys are roles and values are priv_type and status
	 */
	function permit($on, $args) {
		global $groups;
		$on = explode("::", $on);
		$args = starr::star($args);
		$merge = array($on[0] => array($on[1] => $args));
		$this->permits = array_merge_recursive($this->permits, $merge);
	}

	/**
	 * Get permits as records from a schema entry
	 * @param string $model the model the permit is applied to
	 * @param string $action the function on the model that the permit is applied to
	 * @param string $role the role that the permit is applied to
	 */
	function get_permits($model, $action, $role) {
		global $groups;
		global $statuses;
		$permit = array("related_table" => P($model), "action" => $action);
		$ops = $this->permits[$model][$action][$role];
		if (isset($groups[$role])) {
			$permit['role'] = "group";
			$permit['who'] = $groups[$role];
		} else $permit['role'] = $role;
		$ops = explode(" ", $ops);
		$count = count($ops);
		if (1 == $count) {
			if (is_numeric($ops[0])) {
				$ops[1] = $ops[0];
				$ops[0] = "table,global";
			}
		}
		efault($ops[0], "table,global");
		efault($ops[1], array_sum($statuses));
		$permit['status'] = $ops[1];
		$return = array();
		$types = explode(",", $ops[0]);
		foreach ($types as $type) {
			$copy = $permit;
			if (is_numeric($type)) {
				$copy['priv_type'] = "object";
				$copy['related_id'] = $type;
			} else $copy['priv_type'] = $type;
			$return[] = $copy;
		}
		return $return;
	}

	/**
	 * add records to be populated immediately upon the creation of a table
	 * @param string $table the name of the table
	 * @param star $match the fields which if exist, do not store this record
	 * @param star $others the other, non-unique fields
	 */
	function store($table, $match, $others) {
		$merge = array($table => array(array("match" => starr::star($match), "others" => starr::star($others))));
		$this->population = array_merge_recursive($this->population, $merge);
	}

	/**
	 * insert records from population
	 * @param string $table the name of the table to populate
	 */
	function populate($table) {
		$rs = 0;
		$pop = $this->population[$table];
		if (!empty($pop)) foreach ($pop as $record) {
			$query = ""; foreach ($record['match'] as $k => $v) $query .= "$k='$v' && "; $query = rtrim($query, '& ');
			$match = query($table, "where:$query");
			if (empty($match)) {
				$store = array_merge($record['match'], $record['others']);
				fwrite(STDOUT, "Inserting $table record...\n");
				store($table, $store, true);
				$rs++;
			} else if (!empty($record['others'])) {
				foreach ($record['others'] as $k => $v) $query .= " && $k='$v'";
				$match = query($table, "where:$query");
				if (empty($match)) {
					fwrite(STDOUT, "Updating $table record...\n");
					store($table, $record['others'], $record['match']);
					$rs++;
				}
			}
		}
		return $rs;
	}

	/**
	 * Write a model file
	 * @param string $name the name of the model
	 */
	function write_model($name, $backup) {
		exec("./script/generate model ".$name);
	}

	/**
	 * Delete a model
	 * @param string $name the name of the model
	 */
	function drop_model($name) {
		$model_loc = BASE_DIR."/core/app/models/".ucwords($name)."Model.php";
		if (file_exists($model_loc)) unlink($model_loc);
	}

	/**
	 * Permanently add migrations to the migration list
	 * @param string $arg0-$argN the name of the migration
	 */
	function add_migrations($arg) {
		global $sb;
		$sb->import("util/subscribe");
		$args = func_get_args();
		foreach($args as $i => $a) {
			if (!in_array($a, $this->migrations)) {
				if (file_exists(BASE_DIR."/app/migrations/$a.php")) include(BASE_DIR."/app/migrations/$a.php");
				$sb->subscribe("migrations", "global", 10, "return_it", $a);
				$this->migrations[] = $a;
			}
		}
	}

	/**
	 * Migrate from one state to another
	 * @param int $to the migration to go to
	 * @param int $from the migration to start from
	 */
	function migrate($to="top", $from="current") {
		global $sb;
		$last_at = file_get_contents(BASE_DIR."/var/migration");
		if (empty($to) && ("0" !== $to)) $to = "top";
		if ($to == "top") $to = count($this->migrations);
		if ($from == "current") $from = $last_at;
		//MOVE TO FROM
		$current = $from;
		//UPDATE CURRENT
		$file = fopen(BASE_DIR."/var/migration", "wb");
		fwrite($file, $to);
		fclose($file);
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
	}

	function get_relations($from, $to) {
		$fields = $this->get_table($from);
		$return = (isset($fields['id'])) ? array($from => array()) : array();
		$hook = "";
		foreach($fields as $column => $options) {
			if (isset($options['references'])) {
				$ref = explode(" ", $options['references']);
				if (0 === strpos($options['references'], $to)) $hook = $column;
				else $return[$ref[0]] = array("lookup" => $from, "ref_field" => $column);
			}
		}
		if (empty($hook)) return array();
		foreach ($return as $idx => $arr) $return[$idx]["hook"] = $hook;
		return $return;
	}

}
?>