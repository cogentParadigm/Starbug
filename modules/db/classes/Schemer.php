<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/db/Schemer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Schemer
 */
/**
 * @defgroup Schemer
 * the db class
 * @ingroup db
 */
/**
 * The Schemer class. Manages a schema of the database using migrations and handles synching a database with the schema
 * @ingroup Schemer
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
	 * @var array Holds table options
	 */
	var $options = array();
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
	 * @var array Holds sql triggers
	 */
	var $triggers = array();
	/**
	 * @var array triggers to drop
	 */
	var $trigger_drops = array();
	
	var $current;

	/**
	 * constructor. loads migrations
	 */
	function __construct($data) {
		$this->db = $data;
		$this->migrations = config("modules");
		foreach ($this->migrations as $i => $m) $this->migrations[$i] = "modules/".$m;
		$this->migrations = array_merge(array("core/app"), $this->migrations, array("app"));
	}

	function  clean() {
		$this->tables = $this->table_drops = $this->column_drops = $this->uris = $this->permits = $this->population = $this->triggers = $this->trigger_drops = array();
	}
	
	function up($migration) {
		if (is_numeric($migration)) $migration = $this->migrations[$migration];
		$this->current = $migration;
		$migration = BASE_DIR."/".$migration."/up.php";
		if (file_exists($migration)) include($migration);
	}
	
	function down($migration) {
		if (is_numeric($migration)) $migration = $this->migrations[$migration];
		$this->current = $migration;
		$migration = BASE_DIR."/".$migration."/down.php";
		if (file_exists($migration)) include($migration);
	}

	function fill() {
		$to = (file_exists(BASE_DIR."/var/migration")) ? trim(file_get_contents(BASE_DIR."/var/migration")) : 0;
		//MOVE TO CURRENT MIGRATION
		$current = 0;
		while ($current < $to) {
			$this->up($current);
			$current++;
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
			if ($options['type'] == "category") $fields[$column]['references'] = "terms id";
		}
		if (empty($primary)) $fields['id'] = star("type:int  auto_increment:  key:primary");
		if (empty($fields["owner"])) $fields["owner"] = star("type:int  default:1  references:users id");
		if (empty($fields["collective"])) $fields["collective"] = star("type:int  default:0");
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
		$td = 0; //dropped tables
		$cs = 0; //cols
		$cd = 0; //dropped tables
		$ms = 0; //mods
		$us = 0; //uris
		$ud = 0; //dropped uris
		$ps = 0; //permits
		$pd = 0; //dropped permits
		$is = 0; //inserts
		$ds = 0; //drops
		$gs = 0; //created triggers
		$gu = 0; //updated triggers
		$gd = 0; //dropped triggers
		foreach ($this->tables as $table => $fields) {
			$fields = $this->get_table($table);
			$records = $this->db->pdo->query("SHOW TABLES LIKE '".P($table)."'");
			if (false === ($row = $records->fetch())) {
				// NEW TABLE																																													// NEW TABLE
				fwrite(STDOUT, "Creating table ".P($table)."...\n");
				$this->create($table);
				$ts++;
			} else {
				// OLD TABLE																																													// OLD TABLE
				foreach ($fields as $name => $field) {
					if (!$this->db->has($field['type'])) {
						// REAL COLUMN
						$records = $this->db->pdo->query("SHOW COLUMNS FROM ".P($table)." WHERE Field='".$name."'");
						if (false === ($row = $records->fetch())) {
							// NEW COLUMN																																											// NEW COLUMN
							fwrite(STDOUT, "Adding column $name...\n");
							$this->add($table, $name);
							$cs++;
						} else {
							// OLD COLUMN																																											// OLD COLUMN
							$type = explode(" ", $this->get_sql_type($field));
							if (($row['Type'] != $type[0]) || ((isset($field['default'])) && ($row['Default'] != $field['default'])) || (isset($field['null']) && ($row['Null'] == "NO")) || (!isset($field['null']) && ($row['Null'] == "YES"))) {
								fwrite(STDOUT, "Altering column $name...\n");
								$this->modify($table, $name);
								$ms++;
							}
						}
					}
					if (isset($field['references']) && ($field['constraint'] != "false")) {
						$fks = $this->db->pdo->query("SELECT * FROM information_schema.STATISTICS WHERE TABLE_NAME='".P($table)."' && COLUMN_NAME='$name' && TABLE_SCHEMA='".Etc::DB_NAME."'");
						if (false === ($row = $fks->fetch())) {
							// ADD CONSTRAINT																																								// CONSTRAINT
							fwrite(STDOUT, "Adding foreign key ".P($table)."_".$name."_fk...\n");
							$this->add_foreign_key($table, $name);
							$ms++;
						}
					}
				}
			}
			passthru("sb generate model $table -u");
			$is += $this->populate($table);
		}
		foreach ($this->triggers as $name => $triggers) {
			foreach ($triggers as $event => $trigger) {
				$record = $this->db->pdo->query("SELECT * FROM information_schema.TRIGGERS WHERE TRIGGER_NAME='".P($trigger['table']."_".$event."_".$trigger['action'])."'")->fetch();
				if (empty($record)) {
					// ADD TRIGGER																																											// ADD TRIGGER
					fwrite(STDOUT, "Creating trigger ".P($trigger['table'])."_".$event."_$trigger[action]...\n");
					$this->add_trigger($name, $event);
					$gs++;
				} else if ($record['ACTION_STATEMENT'] != $trigger['trigger']) {
					// UPDATE TRIGGER																																										// UPDATE TRIGGER
					fwrite(STDOUT, "Updating trigger ".P($trigger['table'])."_".$event."_$trigger[action]...\n");
					$this->remove_trigger($name, $event);
					$this->add_trigger($name, $event);
					$gu++;
				}
			}
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
			$records = $this->db->pdo->query("SHOW TABLES LIKE '".P($table)."'");
			if ($row = $records->fetch()) {
				// DROP TABLE																																													// DROP TABLE
				fwrite(STDOUT, "Dropping table ".P($table)."...\n");
				$this->drop_table($table);
				$td++;
			}
		}
		foreach ($this->column_drops as $table => $cols) {
			$records = $this->db->pdo->query("SHOW TABLES LIKE '".P($table)."'");
			if ($row = $records->fetch()) {
				foreach($cols as $col) {
					$records = $this->db->pdo->query("SHOW COLUMNS FROM ".P($table)." WHERE field='".$col."'");
					if ($row = $records->fetch()) {
						// DROP COLUMN																																										// DROP COLUMN
						fwrite(STDOUT, "Dropping column ".P($table).".$col...\n");
						$this->remove($table, $col);
						$cd++;
					}
				}
			}
		}
		foreach ($this->trigger_drops as $name => $event) {
			// DROP TRIGGER																																													// DROP TRIGGER
			$parts = explode("::", $name);
			$record = $this->db->pdo->query("SELECT * FROM information_schema.TRIGGERS WHERE TRIGGER_NAME='".P($parts[0]."_".$event."_".$parts[1])."'")->fetch();
			if (false !== $record) {
				fwrite(STDOUT, "Dropping trigger ".P($parts[0])."_".$event."_$parts[1]...\n");
				$this->remove_trigger($name, $event);
				$gd++;
			}
		}
		if (($ts == 0) && ($cs == 0) && ($ms == 0) && ($ds == 0) && ($us == 0) && ($ps == 0) && ($is == 0) && ($td == 0) && ($cd == 0) && ($gs == 0) && ($gd == 0) && ($gu == 0)) {
			return false;
		} else {
			return true;
		}
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
			if (!$this->db->has($options['type'])) {
				$field_sql = "`".$fieldname."` ".$this->get_sql_type($options).", ";
				if (isset($options['key']) && ("primary" == $options['key'])) {
					$primary[] = "`$fieldname`";
					$primary_fields .= $field_sql;
				} else $sql_fields .= $field_sql;
				if (isset($options['index'])) $index[] = $fieldname;
				if (!empty($options['references']) && $options['constraint'] != "false") {
					$ref = explode(" ", $options['references']);
					$rec = array("table" => $ref[0], "column" => $ref[1]);
					if (!empty($options['update'])) $rec['update'] = $options['update'];
					if (!empty($options['delete'])) $rec['delete'] = $options['delete'];
					$foreign[$fieldname] = $rec;
				}
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
		$sql = "`".$name."` ".$this->get_sql_type($field);
		$this->db->exec("ALTER TABLE `".P($table)."` ADD ".$sql);
	}

	/**
	 * Run SQL to drop a column
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function remove($table, $name) {
		$this->db->exec("ALTER TABLE `".P($table)."` DROP COLUMN `".$name."`");
	}

	/**
	 * Run SQL to alter a column in the DB to match the schema
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function modify($table, $name) {
		$fields = $this->get_table($table);
		$field = $fields[$name];
		$sql = "`".$name."` `".$name."` ".$this->get_sql_type($field);
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
		$type = "varchar(".(isset($field['length'])?$field['length']:"64").")";
		if ($field['type'] == 'string') $type = "varchar(".(isset($field['length'])?$field['length']:"64").")";
		else if ($field['type'] == 'password') $type = "varchar(100)";
		else if (($field['type'] == 'text') || ($field['type'] == 'longtext')) $type = $field["type"];
		else if ($field['type'] == 'int' || $field['type'] == 'category') $type = "int(".(isset($field['length'])?$field['length']:"11").")";
		else if ($field['type'] == 'decimal') $type = "decimal(".$field['length'].")";
		else if ($field['type'] == 'double') $type = "double(".$field['length'].")";
		else if ($field['type'] == 'bool') $type = "tinyint(1)";
		else if (($field['type'] == 'datetime') || ($field['type'] == 'timestamp')) $type = "datetime";
		else if (!empty($field['type'])) $type = $field['type'].(isset($field['length'])?'('.$field['length'].')':"");
		$type .= ((isset($field['null'])) ? " NULL" : " NOT NULL")
						.((isset($field['unsigned'])) ? " UNSIGNED" : "")
						.((isset($field['zerofill'])) ? " ZEROFILL" : "")
						.((isset($field['auto_increment'])) ? " AUTO_INCREMENT" : "")
						.((isset($field['unique']) && empty($field['unique'])) ? " UNIQUE" : "")
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
		call_user_func_array(array($this, "column"), $args);
	}

	/**
	 * Add column to schema
	 * @param string $table the name of the table
	 * @param string $col A star formatted column string
	 */
	function column($table, $col) {
		$args = func_get_args();
		$table = array_shift($args);
		if (false !== strpos($table, "  ")) {
			list($table, $ops) = explode("  ", $table, 2);
			$ops = star($ops);
		} else $ops = array();
		efault($this->tables[$table], array());
		$additional = array();
		foreach ($args as $col) {
			$col = star($col);
			$colname = array_shift($col);
			if ($this->db->has($col['type'])) {
				$additional[] = array($table."_".$colname,
					$col['type']."_id  type:int  default:0  key:primary  references:$col[type] id  update:cascade  delete:cascade",
					"owner  type:int  default:1  key:primary  references:users id  update:cascade  delete:cascade",
					$table."_id  type:int  default:0  key:primary  references:$table id  update:cascade  delete:cascade"
				);
			}
			$this->tables[$table][$colname] = $col;
		}
		efault($this->options[$table], array("select" => "$table.*", "search" => $table.'.'.implode(",$table.", array_keys($this->tables[$table]))));
		foreach ($ops as $k => $v) $this->options[$table][$k] = $v;
		foreach ($additional as $tbl) call_user_func_array(array($this, "column"), $tbl);
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
		store("uris", $uri);
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
	function uri($path, $args=array(), $groups=array()) {
		$statuses = config("statuses");
		$options = array();
		$args = star($args);
		$args['path'] = $path;
		efault($args['title'], ucwords(str_replace("-", " ", $path)));
		if (!empty($args['groups'])) {
			$args['groups'] = explode(",", $args['groups']);
			$args['collective'] = 0;
			foreach ($args['groups'] as $group) $args['collective'] += intval($group);
			unset($args['groups']);
		}
		efault($args['collective'], 0);
		efault($args['status'], "4");
		if ($this->current != "core/app") efault($args['prefix'], $this->current."/views/");
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
		$groups = config("groups");
		$on = explode("::", $on);
		$args = star($args);
		efault($this->permits[$on[0]], array());
		efault($this->permits[$on[0]][$on[1]], array());
 		$this->permits[$on[0]][$on[1]] = array_merge($this->permits[$on[0]][$on[1]], $args);

	}

	/**
	 * Get permits as records from a schema entry
	 * @param string $model the model the permit is applied to
	 * @param string $action the function on the model that the permit is applied to
	 * @param string $role the role that the permit is applied to
	 */
	function get_permits($model, $action, $role) {
		$groups = config("groups");
		$statuses = config("statuses");
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
		$merge = array($table => array(array("match" => star($match), "others" => star($others))));
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
				store($table, $store);
				$rs++;
			}
		}
		return $rs;
	}

	/**
	 * add a before trigger on a table
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $trig the statement to attach
	 */
	function before($name, $trig, $each=true) {
		$parts = explode("::", $name);
		efault($this->triggers[$name], array());
		$trigger = array("table" => $parts[0], "action" => $parts[1], "type" => "before", "each" => $each, "trigger" => $trig);
		$this->triggers[$name]["before"] = $trigger;
	}

	/**
	 * add an after trigger on a table
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $trig the statement to attach
	 */
	function after($name, $trig, $each=true) {
		$parts = explode("::", $name);
		efault($this->triggers[$name], array());
		$trigger = array("table" => $parts[0], "action" => $parts[1], "type" => "after", "each" => $each, "trigger" => $trig);
		$this->triggers[$name]["after"] = $trigger;
	}

	/**
	 * drop a trigger
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $event before or after
	 */
	function drop_trigger($name, $event) {
		efault($this->trigger_drops[$name], array());
		$this->trigger_drops[$name][$event] = "";
	}

	/**
	 * run sql to create a trigger
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $type before or after
	 */
	function add_trigger($name, $type) {
		$trigger = $this->triggers[$name][$type];
		$sql = "CREATE TRIGGER `".P($trigger['table']."_".$type."_".$trigger['action'])."` $type $trigger[action] ON ".P($trigger['table']).(($trigger['each']) ? " FOR EACH ROW" : "")." ".$trigger['trigger'].";";
		$this->db->exec($sql);
	}

	/**
	 * run sql to create a trigger
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $type before or after
	 */
	function remove_trigger($name, $type) {
		$parts = explode("::", $name);
		$sql = "DROP TRIGGER `".P($parts[0]."_".$type."_".$parts[1])."`;";
		$this->db->exec($sql);
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
		$args = func_get_args();
		foreach($args as $i => $a) {
			if (!in_array($a, $this->migrations)) {
				$this->migrations[] = $a;
				config("migrations", $this->migrations);
			}
		}
	}

	/**
	 * Migrate from one state to another
	 * @param int $to the migration to go to
	 * @param int $from the migration to start from
	 */
	function migrate($to="top", $from=0) {
		global $sb;
		$last_at = (file_exists(BASE_DIR."/var/migration")) ? trim(file_get_contents(BASE_DIR."/var/migration")) : 0;
		if (empty($to) && ("0" !== $to)) $to = "top";
		if ($to == "top") $to = count($this->migrations);
		if ($from === "current") $from = $last_at;
		//MOVE TO FROM
		$current = $from;
		//UPDATE CURRENT
		$file = fopen(BASE_DIR."/var/migration", "wb");
		fwrite($file, $to);
		fclose($file);
		//MIGRATE
		$result = false;
		if ($to < $from) { //DOWN
			while($current > $to) {
				$this->clean();
				$this->down($current-1);
				if ($this->update()) $result = true;
				//$this->removed($current-1);
				$current--;
			}
		} else {  //UP
			while($current < $to) {
				$this->clean();
				$this->up($current);
				if ($this->update()) $result = true;
				//$this->created($current);
				$current++;
			}
		}
		$this->fill();
		if ($result) {
			fwrite(STDOUT, "Database update completed.\n");
		} else {
			fwrite(STDOUT, "The Database already matches the schema.\n");
		}
		fwrite(STDOUT, "Generating Models...\n");
		fwrite(STDOUT, "Run 'sb generate models' to generate models manually.\n");
		passthru("sb generate models");
		fwrite(STDOUT, "Generating CSS...\n");
		fwrite(STDOUT, "Run 'sb generate css' to generate CSS manually.\n");
		passthru("sb generate css");
	}

	/**
	 * get all of the ways one table is related to another
	 * @param string $from the migration to go to
	 * @param int $from the migration to start from
	 */
	function get_relations($from, $to) {
		$fields = $this->get_table($from);
		$return = $indirect = $hooks = array();
		foreach($fields as $column => $options) {
			if (isset($options['references'])) {
				$ref = explode(" ", $options['references']);
				//if $to has a hook in $from, then it has an indirect relation to everything $from has a relation to
				//otherwise, there are no relationships
				if (0 === strpos($options['references'], $to)) $hooks[] = $column;
				else $indirect[] = array("model" => $ref[0], "lookup" => $from, "ref_field" => $column);
			}
		}
		if (empty($hooks)) return array();
		foreach ($hooks as $hook) {
			//add the direct relation to the hook
			if (isset($fields['id'])) $return[] = array("model" => $from, "field" => $hook);
			//add each indirect relation through the hook
			foreach ($indirect as $i) $return[] = array_merge($i, array("field" => $hook));
		}
		return $return;
	}

	function get_logging_trigger($table, $type) {
		if ($type == "insert") {
			$trigger = "BEGIN
				INSERT INTO ".P("log")." (table_name, object_id, action, created, modified) VALUES ('users', NEW.id, 'INSERT', NOW(), NOW());
			END";
		} else if ($type == "update") {
			$trigger = "BEGIN";
			$fields = $this->get_table($table);
			unset($fields['modified']);
			foreach ($fields as $name => $ops) { $trigger .= "
				IF OLD.$name != NEW.$name THEN
					INSERT INTO ".P("log")." (table_name, object_id, action, column_name, old_value, new_value, created, modified) VALUES ('users', NEW.id, 'UPDATE', '$name', OLD.$name, NEW.$name, NOW(), NOW());
				END IF;";
			}
			$trigger .= "
			END";
		} else if ($type == "delete") {
			$trigger = "BEGIN
				INSERT INTO ".P("log")." (table_name, object_id, action, created, modified) VALUES ('users', OLD.id, 'DELETE', NOW(), NOW());
			END";
		}
		return $trigger;
	}

	function get($model) {
		$groups = config("groups");
		$statuses = config("statuses");
		$sb = sb();
		$fields = $this->get_table($model);
		$options = $this->options[$model];
		//SET UP MODEL ARRAY
		$data = array_merge(array("name" => $model, "label" => ucwords($model), "package" => Etc::WEBSITE_NAME, "fields" => array(), "relations" => array()), $options);
		//ADD FIELDS
		foreach($fields as $name => $field) {
			$data["fields"][$name] = array("filters" => array());
			$data["fields"][$name]['display'] = ((isset($this->tables[$model][$name])) && ($field['display'] !== "false")) ? true : false;
			if (!isset($field['input_type'])) {
				if ($field['type'] == "text") $field['input_type'] = "textarea";
				else if ($field['type'] == "password") $field['input_type'] = "password";
				else if ($field['type'] == "bool") $field['input_type'] = "checkbox";
				else if ($field['type'] == "category") $field['input_type'] = "category_select";
				else if ($field['type'] == "tags") $field['input_type'] = "tag_input";
				else if (isset($field['upload'])) $field['input_type'] = "file_select";
				else if ($this->db->has($field['type'])) $field['input_type'] = "multiple_select";
				else $field['input_type'] = "text";
			}
			$field[$field['type']] = "";
			efault($field[$field['input_type']], "");
			foreach ($field as $k => $v) {
				//if (("references" == $k) && (false === strpos($v, $model))) $data["fields"][$name]["references"] = $v;
				$filter_locations = locate("store/$k.php", "filters");
				if (!empty($filter_locations)) $data["fields"][$name]["filters"][$k] = $v;
				else $data["fields"][$name][$k] = $v;
			}
		}
		//ADD RELATIONS
		foreach ($this->tables as $table => $fields) {
			$relations = $this->get_relations($table, $model);
			$data['relations'] = array_merge($data['relations'], $relations);
			//foreach ($relations as $m => $r) $data["relations"][] = array("model" => $m, "field" => $r['hook'], "lookup" => $r['lookup'], "ref_field" => $r['ref_field']);
		}
		//ADD ACTIONS
		$permits = ($this->db->has("permits")) ? query("permits", "where:related_table='".P($model)."'") : array();
		$actions = array();
		foreach ($permits as $p) {
			if (!isset($actions[$p['action']])) $actions[$p['action']] = array();
				if ("object" == $p['priv_type']) $val = $p['related_id'];
				else $val = $p['priv_type'];
				if ($p['status'] != (array_sum($statuses)-1)) $val .= " ".$p['status'];
			if ("group" == $p['role']) { //GROUP PERMIT
				$actions[$p['action']][array_search($p['who'], $groups)] = (empty($actions[$p['action']][$groups[$p['who']]])) ? $val : ",".$val;
			} else $actions[$p['action']][$p['role']] = (empty($actions[$p['action']][$p['role']])) ? $val : ",".$val;
		}
		$data['actions'] = $actions;
		//ADD URIS
		if ($model == "uris") $data['uris'] = query("uris");
		return $data;
	}
	/**
	 * convert table schema to XML
	 */
	function toXML($model) {
		$xmlDoc = new DOMDocument();
		$root = $xmlDoc->appendChild($xmlDoc->createElement("model"));
		$xmlDoc->formatOutput = true;
		foreach ($model as $key => $value) {
			if (is_array($value)) {
				switch ($key) {
					case "fields":
						foreach ($value as $name => $field) {
							$node = $root->appendChild($xmlDoc->createElement("field"));
							$node->appendChild($xmlDoc->createAttribute("name"))->appendChild($xmlDoc->createTextNode($name));
							foreach ($field as $k => $v) {
								switch ($k) {
									case "filters":
										foreach ($v as $filter => $val) {
											$f = $node->appendChild($xmlDoc->createElement("filter"));
											$f->appendChild($xmlDoc->createAttribute("name"))->appendChild($xmlDoc->createTextNode($filter));
											$f->appendChild($xmlDoc->createAttribute("value"))->appendChild($xmlDoc->createTextNode($val));
										}
										break;
									case "references":
										$ref = explode(" ", $v);
										$f = $node->appendChild($xmlDoc->createElement("references"));
										$f->appendChild($xmlDoc->createAttribute("model"))->appendChild($xmlDoc->createTextNode($ref[0]));
										$f->appendChild($xmlDoc->createAttribute("field"))->appendChild($xmlDoc->createTextNode($ref[1]));
										break;
									default:
										if (!empty($k)) $node->appendChild($xmlDoc->createAttribute($k))->appendChild($xmlDoc->createTextNode($v));
										break;
								}
							}
						}
						break;
					default:
						foreach ($value as $tag) {
							$node = $root->appendChild($xmlDoc->createElement(rtrim($key, 's')));
							foreach ($tag as $k => $v) $node->appendChild($xmlDoc->createAttribute($k))->appendChild($xmlDoc->createTextNode($v));
						}
						break;
				}
			} else {
				$root->appendChild($xmlDoc->createAttribute($key))->appendChild($xmlDoc->createTextNode($value));
			}
		}
		$xml = $xmlDoc->saveXML();
		//WRITE XML
		$file = fopen(BASE_DIR."/var/xml/$model[name].xml", "wb");
		fwrite($file, $xml);
		fclose($file);
		passthru("chmod 0777 ".BASE_DIR."/var/xml/$model[name].xml");
		//RETURN
		return $xml;
	}
	/**
	 * convert table schema to JSON
	 */
	function toJSON($model) {
		$json = json_encode($model);
		$file = fopen(BASE_DIR."/var/json/$model[name].json", "wb");
		fwrite($file, $json);
		fclose($file);
		passthru("chmod 0777 ".BASE_DIR."/var/json/$model[name].json");
		return $json;
	}

}
?>
