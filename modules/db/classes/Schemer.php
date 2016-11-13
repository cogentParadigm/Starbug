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
namespace Starbug\Core;
/**
 * The Schemer class. Manages a schema of the database using migrations and handles synching a database with the schema
 * @ingroup Schemer
 */
class Schemer {
	/**
	 * @var db The db class is a PDO wrapper
	 */
	public $db;
	protected $models;
	/**
	 * @var array Holds tables, columns and column options
	 */
	public $tables = array();
	/**
	 * @var array Holds table options
	 */
	public $options = array();
	/**
	 * @var array Tables that have been dropped
	 */
	public $table_drops = array();
	/**
	 * @var array Columns that have been dropped
	 */
	public $column_drops = array();
	/**
	 * @var array indexes
	 */
	public $indexes = array();
	/**
	 * @var array drop indexes
	 */
	public $index_drops = array();
	/**
	 * @var array Holds uris
	 */
	public $uris = array();
	/**
	 * @var array Holds blocks
	 */
	public $blocks = array();
	/**
	 * @var array Holds permits
	 */
	public $permits = array();
	/**
	 * @var array Ordered list of migrations
	 */
	public $migrations = array();
	/**
	 * @var array Holds records to be inserted immediately after a table is created
	 */
	public $population = array();
	/**
	* @var array Holds entities to be cerated
	*/
	public $entities = array();
	/**
	 * @var array Holds menus
	 */
	public $menus = array();
	/**
	 * @var array Holds taxonomies
	 */
	public $taxonomies = array();
	/**
	 * @var array Holds sql triggers
	 */
	public $triggers = array();
	/**
	 * @var array triggers to drop
	 */
	public $trigger_drops = array();

	public $current;

	public $testMode = false;

	/**#@-*/
	/**
	 * @var array holds mixed in objects
	 */
	public $imported = array();
	/**
	 * @var array holds function names of mixed in objects
	 */
	public $imported_functions = array();

	/**
	 * constructor. loads migrations
	 */
	function __construct(DatabaseInterface $data, ModelFactoryInterface $models, ConfigInterface $config, $modules) {
		$this->db = $data;
		$this->models = $models;
		$this->config = $config;
		$this->migrations = $modules;
	}

	function set_database(DatabaseInterface $db) {
		$this->db = $db;
	}

	function clean() {
		$this->tables = $this->table_drops = $this->column_drops = $this->uris = $this->blocks = $this->permits = $this->population = $this->triggers = $this->trigger_drops = $this->menus = $this->taxonomies = $this->indexes = $this->index_drops = $this->entities = array();
	}

	function up($migration) {
		$migration = $this->migrations[$migration];
		$this->current = $migration;
		$migration = BASE_DIR."/".$migration."/up.php";
		if (file_exists($migration)) include($migration);
		if ($this->testMode) {
			$migration = BASE_DIR."/".$this->current."/tests/up.php";
			if (file_exists($migration)) include($migration);
		}
	}

	function down($migration) {
		if (is_numeric($migration)) $migration = $this->migrations[$migration];
		$this->current = $migration;
		$migration = BASE_DIR."/".$migration."/down.php";
		if (file_exists($migration)) include($migration);
		if ($this->testMode) {
			$migration = BASE_DIR."/".$this->current."/tests/down.php";
			if (file_exists($migration)) include($migration);
		}
	}

	function fill() {
		foreach ($this->migrations as $mid => $migration) {
			$this->up($mid);
		}
	}

	function testMode($to = true) {
		$this->testMode = $to;
		$this->clean();
		$this->fill();
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
		if (empty($primary)) $fields['id'] = ["type" => "int", "auto_increment" => "", "key" => "primary"];
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
		$bs = 0; //blocks
		$ud = 0; //dropped uris
		$ps = 0; //permits
		$pd = 0; //dropped permits
		$is = 0; //inserts
		$as = 0; //updates
		$ds = 0; //drops
		$gs = 0; //created triggers
		$gu = 0; //updated triggers
		$gd = 0; //dropped triggers
		$xs = 0; //indexes
		$xd = 0; //dropped indexes

		//CREATE TABLES FIRST
		do {
			$previous_count = $ts;
			$this->clean();
			$this->fill();
			foreach ($this->tables as $table => $fields) {
				$records = $this->db->pdo->query("SHOW TABLES LIKE '".$this->db->prefix($table)."'");
				if (false === ($row = $records->fetch())) {
					// NEW TABLE																																													// NEW TABLE
					fwrite(STDOUT, "Creating table ".$this->db->prefix($table)."...\n");
					$this->create($table);
					$this->generate_model($table);
					$ts++;
				}
			}
		} while ($ts > $previous_count);
		//UPDATE TABLES WITH COLUMN ALTERATIONS AND FOREIGN KEYS
		foreach ($this->tables as $table => $fields) {
			$table_info = $this->get($table);
			$fields = $table_info['fields'];
			// OLD TABLE																																														// OLD TABLE
			foreach ($fields as $name => $field) {
				if (!isset($this->tables[$field['type']])) {
					// REAL COLUMN
					$records = $this->db->pdo->query("SHOW COLUMNS FROM ".$this->db->prefix($table)." WHERE Field='".$name."'");
					if (false === ($row = $records->fetch())) {
						// NEW COLUMN																																												// NEW COLUMN
						fwrite(STDOUT, "Adding column $name...\n");
						$this->add($table, $name);
						$cs++;
					} else {
						// OLD COLUMN																																												// OLD COLUMN
						$type = explode(" ", $this->get_sql_type($field));
						if (($row['Type'] != $type[0]) || ((isset($field['default'])) && ($row['Default'] != $field['default']) && (!isset($field['null']) || $field['default'] !== "NULL" || !empty($row['Default']))) || (isset($field['null']) && ($row['Null'] == "NO")) || (!isset($field['null']) && ($row['Null'] == "YES"))) {
							fwrite(STDOUT, "Altering column $name...\n");
							$this->modify($table, $name);
							$ms++;
						}
						if (isset($row['index'])) {
							$index = $this->db->pdo->query("SHOW INDEXES FROM ".$this->db->prefix($table)." WHERE key_name='".$name."'");
							if (empty($index)) $this->create_index($table, $name);
						}
					}
				}
				if (isset($field['references']) && ($field['constraint'] !== false)) {
					$fks = $this->db->pdo->query("SELECT * FROM information_schema.STATISTICS WHERE TABLE_NAME='".$this->db->prefix($table)."' && COLUMN_NAME='$name' && TABLE_SCHEMA='".$this->db->database_name."'");
					if (false === ($row = $fks->fetch())) {
						// ADD CONSTRAINT																																									// CONSTRAINT
						fwrite(STDOUT, "Adding foreign key ".$this->db->prefix($table)."_".$name."_fk...\n");
						$this->add_foreign_key($table, $name);
						$ms++;
					}
				}
			}
			//entity type definition
			$rows = $this->db->query("entities")->condition("name", $table)->one();
			unset($table_info['fields']);
			unset($table_info['relations']);
			unset($table_info['select']);
			unset($table_info['search']);
			unset($table_info['label_select']);
			unset($table_info['list']);
			unset($table_info['groups']);
			if (empty($rows)) {
				// ADD CONTENT TYPE																																														// ADD CONTENT TYPE
				fwrite(STDOUT, "Adding Entity '$table'...\n");
				$this->db->store("entities", $table_info);
				$is++;
			} else {
				$query = $this->db->query("entities");
				foreach ($table_info as $k => $v) $query->condition("entities.".$k, $v);
				$rows = $query->all();
				if (empty($rows)) {
					// UPDATE CONTENT TYPE																																												// UPDATE CONTENT TYPE
					fwrite(STDOUT, "Updating Entity '$table'...\n");
					$this->db->store("entities", $table_info, array("name" => $table));
					$as++;
				}
			}
			$this->generate_model($table);
			$is += $this->populate($table, true);
		}
		foreach ($this->indexes as $idx => $index) {
			$table = reset($index);
			$name = $this->db->prefix("").implode("_", $index)."_index";
			$exists = $this->db->pdo->query("SHOW INDEXES FROM ".$this->db->prefix($table)." WHERE key_name='".$name."'")->fetch();
			if (empty($exists)) {
				fwrite(STDOUT, "Creating index '$name'...\n");
				call_user_method_array("create_index", $this, $index);
			}
		}
		foreach ($this->index_drops as $idx => $index) {
			$table = reset($index);
			$name = $this->db->prefix("").implode("_", $index)."_index";
			$exists = $this->db->pdo->query("SHOW INDEXES FROM ".$this->db->prefix($table)." WHERE key_name='".$name."'")->fetch();
			if (!empty($exists)) {
				fwrite(STDOUT, "Dropping index '$name'...\n");
				call_user_method_array("drop_index", $this, $index);
			}
		}
		foreach ($this->triggers as $name => $triggers) {
			foreach ($triggers as $event => $trigger) {
				$record = $this->db->pdo->query("SELECT * FROM information_schema.TRIGGERS WHERE TRIGGER_NAME='".$this->db->prefix($trigger['table']."_".$event."_".$trigger['action'])."'")->fetch();
				if (empty($record)) {
					// ADD TRIGGER																																											// ADD TRIGGER
					fwrite(STDOUT, "Creating trigger ".$this->db->prefix($trigger['table'])."_".$event."_$trigger[action]...\n");
					$this->add_trigger($name, $event);
					$gs++;
				} else if ($record['ACTION_STATEMENT'] != $trigger['trigger']) {
					// UPDATE TRIGGER																																										// UPDATE TRIGGER
					fwrite(STDOUT, "Updating trigger ".$this->db->prefix($trigger['table'])."_".$event."_$trigger[action]...\n");
					$this->remove_trigger($name, $event);
					$this->add_trigger($name, $event);
					$gu++;
				}
			}
		}
		foreach ($this->taxonomies as $taxonomy => $items) {
			$records = $this->db->query("terms")
				->select("count(*) as count")->condition("taxonomy", $taxonomy)->one();
			if ($records['count'] == 0) {
				//CREATE TAXONOMY																																										 //CREATE TAXONOMY
				fwrite(STDOUT, "Creating taxonomy ".$taxonomy."...\n");
				$is += $this->create_taxonomy($taxonomy);
			} else $is += $this->create_taxonomy($taxonomy, true);
		}
		foreach ($this->uris as $path => $uri) {
			$rows = $this->db->query("uris")->condition("path", $path)->one();
			if (empty($rows)) {
				// ADD URI																																														// ADD URI
				fwrite(STDOUT, "Adding URI '$path'...\n");
				$this->add_uri($path);
				$us++;
			} else {
				$query = $this->db->query("uris")->select("uris.*,uris.groups.slug as groups");
				$extra_terms = false;
				foreach ($uri as $k => $v) {
					if ($k == "groups" || $k == "statuses") $query->condition("uris.".$k.".slug", $v);
					else $query->condition("uris.".$k, $v);
				}
				$rows = $query->all();
				if (!empty($rows) && !empty($rows[0]['groups'])) {
					$g = $this->db->query("uris")->select("uris.*,groups.slug")->condition("uris.id", $rows[0]['id']);
					if (!empty($uri['groups'])) $g->condition("groups.slug", $uri['groups'], "!=");
					$g = $g->execute();
					if (!empty($g)) {
						$uri['groups'] .= ",-~";
						$extra_terms = true;
					}
				}
				if (empty($rows) || $extra_terms) {
					// UPDATE URI																																												// UPDATE URI
					fwrite(STDOUT, "Updating URI '$path'...\n");
					$this->update_uri($path, $uri);
					$us++;
				}
			}
		}
		foreach ($this->blocks as $path => $blocks) $bs += $this->create_blocks($path, $blocks);
		foreach ($this->permits as $model => $actions) {
			foreach ($actions as $action => $roles) {
				foreach ($roles as $permit) {
					$permit['related_table'] = $model;
					$permit['action'] = $action;
					if (empty($permit['priv_type'])) $permit['priv_type'] = "*";
					$query = $this->db->query("permits");
					foreach ($permit as $k => $v) {
						if (0 === strpos($k, "user_") || 0 === strpos($k, "object_")) $query->condition($k.".slug", $v);
						else $query->condition($k, $v);
					}
					$row = $query->one();
					if (empty($row)) {
						// ADD PERMIT																																										// ADD PERMIT
						fwrite(STDOUT, "Adding $permit[priv_type] permit on $model::$action for ".$permit['role']."...\n");
						$this->db->store("permits", $permit);
						$ps++;
					}
				}
			}
		}
		foreach ($this->menus as $menu => $items) {
			$records = $this->db->query("menus")
				->select("count(*) as count")->condition("menu", $menu)->one();
			if ($records['count'] == 0) {
				//CREATE MENU																																													//CREATE MENU
				fwrite(STDOUT, "Creating menu ".$menu."...\n");
				$is += $this->create_menu($menu);
			} else $is += $this->create_menu($menu, true);
		}
		foreach ($this->tables as $table => $fields) $is += $this->populate($table, false);
		foreach ($this->table_drops as $table) {
			$records = $this->db->pdo->query("SHOW TABLES LIKE '".$this->db->prefix($table)."'");
			if ($row = $records->fetch()) {
				// DROP TABLE																																													// DROP TABLE
				fwrite(STDOUT, "Dropping table ".$this->db->prefix($table)."...\n");
				$this->drop_table($table);
				$td++;
			}
		}
		foreach ($this->column_drops as $table => $cols) {
			$records = $this->db->pdo->query("SHOW TABLES LIKE '".$this->db->prefix($table)."'");
			if ($row = $records->fetch()) {
				foreach ($cols as $col) {
					$records = $this->db->pdo->query("SHOW COLUMNS FROM ".$this->db->prefix($table)." WHERE field='".$col."'");
					if ($row = $records->fetch()) {
						// DROP COLUMN																																										// DROP COLUMN
						fwrite(STDOUT, "Dropping column ".$this->db->prefix($table).".$col...\n");
						$this->remove($table, $col);
						$cd++;
					}
				}
			}
		}
		foreach ($this->trigger_drops as $name => $event) {
			// DROP TRIGGER																																													// DROP TRIGGER
			$parts = explode("::", $name);
			$record = $this->db->pdo->query("SELECT * FROM information_schema.TRIGGERS WHERE TRIGGER_NAME='".$this->db->prefix($parts[0]."_".$event."_".$parts[1])."'")->fetch();
			if (false !== $record) {
				fwrite(STDOUT, "Dropping trigger ".$this->db->prefix($parts[0])."_".$event."_$parts[1]...\n");
				$this->remove_trigger($name, $event);
				$gd++;
			}
		}
		if (($ts == 0) && ($cs == 0) && ($ms == 0) && ($ds == 0) && ($us == 0) && ($ps == 0) && ($is == 0) && ($td == 0) && ($cd == 0) && ($gs == 0) && ($gd == 0) && ($gu == 0) && ($bs == 0) && ($as == 0)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Run SQL to create a table in the DB from the schema
	 * @param string $name the name of the table from tables array
	 */
	function create($name, $backup = false, $write = true) {
		$fields = $this->get_table($name);
		$this->drop_table($name);
		$sql = "CREATE TABLE `".$this->db->prefix($name)."` (";
		$primary = array();
		$index = array();
		$sql_fields = "";
		$primary_fields = "";
		foreach ($fields as $fieldname => $options) {
			if (!isset($this->tables[$options['type']])) {
				$field_sql = "`".$fieldname."` ".$this->get_sql_type($options).", ";
				if (isset($options['key']) && ("primary" == $options['key'])) {
					$primary[] = "`$fieldname`";
					$primary_fields .= $field_sql;
				} else $sql_fields .= $field_sql;
				if (isset($options['index'])) $index[] = $fieldname;
			}
		}
		$primary = join(", ", $primary);
		$sql .= $primary_fields.$sql_fields."PRIMARY KEY ($primary)";
		foreach ($index as $k) $sql .= ", KEY `".$k."` (`$k`)";
		$this->db->exec($sql." ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}

	/**
	 * run SQL to drop a table
	 * @param string $name the name of the table from tables array
	 */
	function drop_table($name) {
		$this->db->exec("DROP TABLE IF EXISTS `".$this->db->prefix($name)."`");
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
		$this->db->exec("ALTER TABLE `".$this->db->prefix($table)."` ADD ".$sql);
	}

	/**
	 * Run SQL to drop a column
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function remove($table, $name) {
		$this->db->exec("ALTER TABLE `".$this->db->prefix($table)."` DROP COLUMN `".$name."`");
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
		$this->db->exec("ALTER TABLE `".$this->db->prefix($table)."` CHANGE ".$sql);
	}

	/**
	 * Run SQL to add an index
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function create_index($table, $name) {
		$args = func_get_args();
		$table = array_shift($args);
		$sql = "CREATE INDEX ".$this->db->prefix($table)."_".implode("_", $args)."_index ON `".$this->db->prefix($table)."` (`".implode("`, `", $args)."`)";
		$this->db->exec($sql);
	}

	/**
	 * Run SQL to drop an index
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function drop_index($table, $name) {
		$args = func_get_args();
		$table = array_shift($args);
		$sql = "ALTER TABLE ".$this->db->prefix($table)." DROP INDEX ".$this->db->prefix($table)."_".implode("_", $args)."_index";
		$this->db->exec($sql);
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
		$this->db->exec("ALTER TABLE `".$this->db->prefix($table)."` ADD CONSTRAINT `".$this->db->prefix($table)."_".$name."_fk` FOREIGN KEY (`$name`) REFERENCES `".$this->db->prefix($ref[0])."` (`".$ref[1]."`)".$append);
	}

	/**
	 * Get an SQL type string for a column
	 * @param array $field the column options from the schema
	 */
	function get_sql_type($field) {
		$type = false;
		if ($field['type'] == 'string') $type = "varchar(".(isset($field['length'])?$field['length']:"64").")";
		else if ($field['type'] == 'password') $type = "varchar(100)";
		else if (($field['type'] == 'text') || ($field['type'] == 'longtext')) $type = $field["type"];
		else if ($field['type'] == 'int' || $field['type'] == 'category') $type = "int(".(isset($field['length'])?$field['length']:"11").")";
		else if ($field['type'] == 'decimal') $type = "decimal(".$field['length'].")";
		else if ($field['type'] == 'double') $type = "double(".$field['length'].")";
		else if ($field['type'] == 'bool') $type = "tinyint(1)";
		else if (($field['type'] == 'datetime') || ($field['type'] == 'timestamp')) $type = "datetime";
		else if ($field['type'] == 'date') $type = "date";
		$type .= ((isset($field['null'])) ? " NULL" : " NOT NULL")
						.((isset($field['unsigned'])) ? " UNSIGNED" : "")
						.((isset($field['zerofill'])) ? " ZEROFILL" : "")
						.((isset($field['auto_increment'])) ? " AUTO_INCREMENT" : "")
						.((isset($field['unique']) && empty($field['unique'])) ? " UNIQUE" : "")
						.((!isset($field['default'])) ? "" : " default ".(($field['default'] == "NULL" || substr($field['default'], -2) == "()") ? $field['default'] : "'".$field['default']."'"));
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
	function column($table) {
		$args = func_get_args();
		$table = array_shift($args);
		if (is_array($table)) {
			$ops = $table;
			$table = array_shift($ops);
		} else {
			$ops = array();
		}
		if (empty($this->tables[$table])) $this->tables[$table] = array();
		$additional = array();
		foreach ($args as $col) {
			$colname = array_shift($col);
			if ($col['type'] == "category") {
				$col['references'] = "terms id";
				$col['alias'] = '%taxonomy% %slug%';
			}
			$access_col = false;
			if ($table !== "permits" && isset($col['user_access'])) {
				$access_col = ["user_".$colname];
			} else if ($table !== "permits" && isset($col['object_access'])) {
				$access_col = ["object_".$colname];
			}
			if ($access_col) {
				foreach ($col as $nk => $nv) $access_col[$nk] = $nv;
				$access_col['type'] = "category";
				$access_col['null'] = "";
				$additional[] = array("permits", $access_col);
			}
			if (isset($this->tables[$col['type']]) || $this->models->has($col['type'])) {
				$ref_table_name = (empty($col['table'])) ? $table."_".$colname : $col['table'];
				$ref_table_def = array([$ref_table_name, "groups" => false],
					["owner", "type" => "int", "null" => true, "references" => "users id", "owner" => true, "optional" => true],
					[$table."_id", "type" => "int", "default" => "NULL", "references" => "$table id", "null" => false],
					["position", "type" => "int", "ordered" => $table."_id", "optional" => true]
				);
				if ($ref_table_name != $col['type']) {
					$ref_table_def[] = [$colname."_id", "type" => "int", "default" => "0", "references" => $col['type']." id"];
					$this->index($ref_table_name, $table."_id", $colname."_id");
				}
				$additional[] = $ref_table_def;
			}
			$this->tables[$table][$colname] = $col;
		}
		$search_cols = array_keys($this->tables[$table]);
		foreach ($search_cols as $colname_index => $colname_value) if (isset($this->tables[$this->tables[$table][$colname_value]['type']])) unset($search_cols[$colname_index]);
		if (empty($this->options[$table])) $this->options[$table] = array("select" => "$table.*", "search" => $table.'.'.implode(",$table.", $search_cols));
		foreach ($ops as $k => $v) $this->options[$table][$k] = ($v === "false") ? false : (($v === "true") ? true : $v);
		if (isset($ops['base']) && $ops['base'] !== $table) {
			//find the root
			$base = $ops['base'];
			while (!empty($this->options[$base]["base"])) $base = $this->options[$base]["base"];
			$this->tables[$table][$base."_id"] = array("type" => "int", "references" => $base." id");
		} else if (empty($this->options['base'])) {
			$add = array($table);
			if (empty($this->tables[$table]["owner"])) $add[] = ["owner", "type" => "int", "null" => true, "references" => "users id", "owner" => true, "optional" => true];
			if (empty($this->tables[$table]["groups"]) && (!isset($this->options[$table]["groups"]) || $this->options[$table]["groups"] == true)) $add[] = ["groups", "type" => "terms", "taxonomy" => "groups", "user_access" => true, "optional" => true];
			if (empty($this->tables[$table]["statuses"])) $add[] = ["statuses", "type" => "category", "label" => "Status", "taxonomy" => "statuses", "object_access" => true, "null" => true];
			if (empty($this->tables[$table]["created"])) $add[] = ["created", "type" => "datetime", "default" => "0000-00-00 00:00:00", "time" => "insert"];
			if (empty($this->tables[$table]["modified"])) $add[] = ["modified", "type" => "datetime", "default" => "0000-00-00 00:00:00", "time" => "update"];
			if (count($add) > 1) $additional[] = $add;
		}
		foreach ($additional as $tbl) call_user_func_array(array($this, "column"), $tbl);
	}

	/**
	 * Drop table or column from schema
	 * @param string $table the name of the table
	 * @param string $col (optional) column name
	 */
	function drop($table, $col = "") {
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
	 * Create an index
	 * @param string $table the name of the table
	 * @param string $col
	 * @param string $col2
	 * etc..
	 */
	function index($table, $col) {
		$args = func_get_args();
		$this->indexes[] = $args;
	}

	function index_drop($table, $col) {
		$args = func_get_args();
		$this->index_drops[] = $args;
	}

	/**
	 * Add a uri to the db from the schema
	 * @param string $path the path of the uri
	 */
	function add_uri($path) {
		$uri = $this->uris[$path];
		$entity = (empty($uri['type'])) ? "uris" : $uri['type'];
		$this->models->get($entity)->store($uri);
	}

	/**
	 * Update a uri in the db from the schema
	 * @param string $path the path of the uri
	 */
	function update_uri($path, $uri = array()) {
		if (empty($uri)) $uri = $this->uris[$path];
		$entity = (empty($uri['type'])) ? "uris" : $uri['type'];
		$this->models->get($entity)->store($uri, array("path" => $path));
	}

	/**
	 * Add a uri to the schema
	 * @param string $path the path
	 * @param star $args other fields
	 */
	function uri($path, $args = array(), $groups = array()) {
		$args['path'] = $path;
		if(empty($args['title'])) $args['title'] = ucwords(str_replace("-", " ", $path));
		if (empty($args['statuses'])) $args['statuses'] = "published";
		$this->uris[$path] = $args;
	}

	/**
	 * Add a content type to the database
	 * @param string $type the path of the uri
	 */
	function add_entity($name) {
		$record = $this->entities[$name];
		$this->db->store("entities", $record);
	}

	/**
	 * Update a content type
	 * @param string $type the type
	 */
	function update_entity($name, $record = array()) {
		if (empty($record)) $record = $this->entities[$name];
		$this->db->store("entities", $record, array("name" => $name));
	}

	/**
	* Add a table to the schema
	* @param string $arg0 the name of the table
	* @param string $arg1-$arg(N-1) A star formatted column string
	*/
	function entity($arg) {
		$args = func_get_args();
		call_user_func_array(array($this, "column"), $args);
	}

	/**
	 * Add a block to the schema
	 * @param string $path the uri path
	 * @param string $content the content
	 * @param star $ops options (region, type, position)
	 */
	function block($path, $content, $ops = array()) {
		$ops["content"] = $content;
		if (empty($this->blocks[$path])) $this->blocks[$path] = array();
		$this->blocks[$path][] = $ops;
	}

	/**
	 * Add a permit to the schema
	 * @param string $on the model and action to apply the permit on
	 * @param star $args a field string where keys are roles and values are priv_type and status
	 */
	function permit($on, $arg1) {
		$args = func_get_args();
		$on = array_shift($args);
		$on = explode("::", $on);
		if (empty($this->permits[$on[0]])) $this->permits[$on[0]] = array();
		if (empty($this->permits[$on[0]][$on[1]])) $this->permits[$on[0]][$on[1]] = array();
		foreach ($args as $row) {
			$role = array_shift($row);
			$row['role'] = $role;
			$this->permits[$on[0]][$on[1]][] = $row;
		}
	}

	function revoke($on, $arg1) {
		$args = func_get_args();
		$on = array_shift($args);
		$on = explode("::", $on);
		if (!empty($this->permits[$on[0]]) && !empty($this->permits[$on[0]][$on[1]])) {
			foreach ($args as $row) {
				$role = array_shift($row);
				$row['role'] = $role;
				foreach ($this->permits[$on[0]][$on[1]] as $idx => $existing) {
					$valid = true;
					foreach ($row as $k => $v) {
						if ($existing[$k] != $v) $valid = false;
					}
					if ($valid) unset($this->permits[$on[0]][$on[1]][$idx]);
				}
			}
		}
	}

	/**
	 * Get permits as records from a schema entry
	 * @param string $model the model the permit is applied to
	 * @param string $action the function on the model that the permit is applied to
	 * @param string $role the role that the permit is applied to
	 */
	function get_permits($model, $action, $ops) {
		$permit = array("related_table" => $model, "action" => $action);
		$role = $ops['role'];
		$parts = explode(" ", $role, 2);
		if (count($parts) == 1) $permit['role'] = $role;
		else {
			$permit['role'] = "taxonomy";
			$permit['roles'] = array($parts[0] => $parts[1]);
		}
		$permit['terms'] = $ops['terms'];
		$return = array();
		$types = explode(",", $ops['type']);
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
	 * Add a menu to the schema
	 * @param string $menu the name of the menu
	 * @param star $item the item
	 */
	function menu($menu, $item) {
		$args = func_get_args();
		$menu = array_shift($args);
		if (empty($this->menus[$menu])) $this->menus[$menu] = array();
		foreach ($args as $item) $this->menus[$menu][] = $item;
	}

	/**
	 * Add a taxonomy to the schema
	 * @param string $menu the name of the menu
	 * @param star $item the item
	 */
	function taxonomy($taxonomy, $item) {
		$args = func_get_args();
		$taxonomy = array_shift($args);
		if (empty($this->taxonomies[$taxonomy])) $this->taxonomies[$taxonomy] = array();
		foreach ($args as $item) $this->taxonomies[$taxonomy][] = $item;
	}

	/**
	 * add records to be populated immediately upon the creation of a table
	 * @param string $table the name of the table
	 * @param star $match the fields which if exist, do not store this record
	 * @param star $others the other, non-unique fields
	 */
	function store($table, $match, $others = array(), $immediate = false) {
		$merge = array($table => array(array("match" => $match, "others" => $others, "immediate" => $immediate)));
		$this->population = array_merge_recursive($this->population, $merge);
	}

	/**
	 * create blocks
	 * @param string $path the name of the path
	 */
	function create_blocks($path, $blocks = array()) {
		$count = 0;
		$uri = $this->db->get("uris", ["path" => $path], ["limit" => "1"]);
		if (!empty($blocks)) foreach ($blocks as $block) $count += $this->create_block($uri, $block);
		return $count;
	}

	/**
	 * create block
	 * @param string $path the name of the path
	 * @param array $block
	 */
	function create_block($uri, $block) {
		if (empty($block['region'])) $block['region'] = "content";
		$block['uris_id'] = $uri['id'];
		$content = $block['content'];
		unset($block['content']);
		$results = $this->db->get("blocks", $block);
		if (empty($results)) {
			fwrite(STDOUT, "Creating block for /".$uri['path']."...\n");
			if (empty($block['position'])) $block['position'] = "";
			$block['content'] = $content;
			$this->db->store("blocks", $block);
			return 1;
		}
		return 0;
	}

	/**
	 * insert records from a menu
	 * @param string $menu the name of the menu
	 */
	function create_menu($menu, $update = false) {
		$count = 0;
		$items = $this->menus[$menu];
		if (!empty($items)) foreach ($items as $record) $count += $this->create_menu_item($menu, $record, $update);
		return $count;
	}

	function create_menu_item($menu, $item, $update = false) {
		$children = empty($item['children']) ? array() : $item['children'];
		unset($item['children']);
		$item['menu'] = $menu;
		$match = array();
		foreach ($item as $k => $v) {
			if ($k == "groups" || $k == "statuses") $k = "menus.".$k;
			$match[$k] = $v;
		}
		$record = $this->db->query("menus")->conditions($match)->one();
		if (empty($record)) {
			if ($update) fwrite(STDOUT, "Creating $menu menu item...\n");
			$this->db->store("menus", $item);
			$id = $this->models->get("menus")->insert_id;
			$count = 1;
		} else $id = $record['id'];
		foreach ($children as $child) {
			$child['parent'] = $id;
			$count += $this->create_menu_item($menu, $child);
		}
		return $count;
	}

	/**
	 * insert records from a taxonomy
	 * @param string $taxonomy the name of the taxonomy
	 */
	function create_taxonomy($taxonomy, $update = false) {
		$count = 0;
		$items = $this->taxonomies[$taxonomy];
		if (!empty($items)) foreach ($items as $record) $count += $this->create_taxonomy_item($taxonomy, $record, $update);
		return $count;
	}

	function create_taxonomy_item($taxonomy, $item, $update = false) {
		$children = empty($item['children']) ? array() : $item['children'];
		unset($item['children']);
		$item['taxonomy'] = $taxonomy;
		$record = $this->db->query("terms")->conditions($item)->one();
		if (empty($record)) {
			if ($update) fwrite(STDOUT, "Creating $taxonomy taxonomy term...\n");
			$this->db->store("terms", $item);
			$id = $this->models->get("terms")->insert_id;
			$count = 1;
		} else $id = $record['id'];
		foreach ($children as $child) {
			$child['parent'] = $id;
			$count += $this->create_taxonomy_item($taxonomy, $child);
		}
		return $count;
	}

	/**
	 * insert records from population
	 * @param string $table the name of the table to populate
	 */
	function populate($table, $immediate = false) {
		$count = 0;
		$pop = $this->population[$table];
		if (!empty($pop)) {
			foreach ($pop as $record) {
				if ($record['immediate'] == $immediate) {
					$match = $this->db->query($table)->conditions($record['match'])->one();
					if (empty($match)) {
						$store = array_merge($record['match'], $record['others']);
						fwrite(STDOUT, "Inserting $table record...\n");
						$this->db->store($table, $store);
						$count++;
					}
				}
			}
		}
		return $count;
	}

	/**
	 * add a before trigger on a table
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $trig the statement to attach
	 */
	function before($name, $trig, $each = true) {
		$parts = explode("::", $name);
		if (empty($this->triggers[$name])) $this->triggers[$name] = array();
		$trigger = array("table" => $parts[0], "action" => $parts[1], "type" => "before", "each" => $each, "trigger" => $trig);
		$this->triggers[$name]["before"] = $trigger;
	}

	/**
	 * add an after trigger on a table
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $trig the statement to attach
	 */
	function after($name, $trig, $each = true) {
		$parts = explode("::", $name);
		if (empty($this->triggers[$name])) $this->triggers[$name] = array();
		$trigger = array("table" => $parts[0], "action" => $parts[1], "type" => "after", "each" => $each, "trigger" => $trig);
		$this->triggers[$name]["after"] = $trigger;
	}

	/**
	 * drop a trigger
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $event before or after
	 */
	function drop_trigger($name, $event) {
		if (empty($this->trigger_drops[$name])) $this->trigger_drops[$name] = array();
		$this->trigger_drops[$name][$event] = "";
	}

	/**
	 * run sql to create a trigger
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $type before or after
	 */
	function add_trigger($name, $type) {
		$trigger = $this->triggers[$name][$type];
		$sql = "CREATE TRIGGER `".$this->db->prefix($trigger['table']."_".$type."_".$trigger['action'])."` $type $trigger[action] ON ".$this->db->prefix($trigger['table']).(($trigger['each']) ? " FOR EACH ROW" : "")." ".$trigger['trigger'].";";
		$this->db->exec($sql);
	}

	/**
	 * run sql to create a trigger
	 * @param string $name in the form model::action, where action is insert, update, or delete
	 * @param string $type before or after
	 */
	function remove_trigger($name, $type) {
		$parts = explode("::", $name);
		$sql = "DROP TRIGGER `".$this->db->prefix($parts[0]."_".$type."_".$parts[1])."`;";
		$this->db->exec($sql);
	}

	/**
	 * Delete a model
	 * @param string $name the name of the model
	 */
	function drop_model($name) {
		$model_loc = BASE_DIR."/var/models/".ucwords($name)."Model.php";
		if (file_exists($model_loc)) unlink($model_loc);
	}

	/**
	 * Migrate from one state to another
	 * @param int $to the migration to go to
	 * @param int $from the migration to start from
	 */
	function migrate() {
		if ($this->update()) {
			fwrite(STDOUT, "Database update completed.\n");
		} else {
			fwrite(STDOUT, "The Database already matches the schema.\n");
		}
		fwrite(STDOUT, "Generating Models...\n");
		fwrite(STDOUT, "Run 'sb generate models' to generate models manually.\n");
		foreach ($this->tables as $table => $fields) $this->generate_model($table);
	}

	function generate_model($table) {
		$data = $this->get($table);
		$this->toXML($data);
		$this->toJSON($data);
		$result = BASE_DIR."/core/app/script/generate/model/update.php";
		$render_prefix = reset(explode("/model/", str_replace(BASE_DIR, "", $result)))."/model/";
		$output_path = BASE_DIR."/var/models/".ucwords($table)."Model.php"; //output
		$base = "";
		//if (!empty($this->options[$table]['base'])) $base = $this->options[$table]['base'];
		$locator = new ResourceLocator(BASE_DIR, array($render_prefix));
		$template = new Template($locator);
		$data = $template->get(array($base."/base", "base"), array("model" => $table, "config" => $this->config), array("prefix" => $render_prefix));
		file_put_contents($output_path, $data);
		if (!class_exists("Starbug\Core\\".ucwords($table)."Model")) include($output_path);
	}

	/**
	 * get all of the ways one table is related to another
	 * @param string $from the migration to go to
	 * @param int $from the migration to start from
	 */
	function get_relations($from, $to, $rels = array()) {
		$fields = $this->get_table($from);
		$return = $indirect = $hooks = array();
		foreach ($fields as $column => $options) {
			if (isset($options['references'])) {
				$ref = explode(" ", $options['references']);
				//if $to has a hook in $from, then it has an indirect relation to everything $from has a relation to
				//otherwise, there are no relationships
				if (0 === strpos($options['references'], $to)) $hooks[] = $column;
				else if (isset($rels[$options['references']])) $return[] = array("type" => "one", "model" => $from, "field" => $rels[$options['references']], "ref_field" => $column);
				else $indirect[] = array("type" => "many", "model" => $ref[0], "lookup" => $from, "ref_field" => $column);
			}
		}
		if (empty($hooks)) return $return;
		foreach ($hooks as $hook) {
			//add the direct relation to the hook
			if (isset($fields['id'])) $return[] = array("type" => "many", "model" => $from, "field" => $hook);
			//add each indirect relation through the hook
			foreach ($indirect as $i) $return[] = array_merge($i, array("field" => $hook));
		}
		return $return;
	}

	function get_logging_trigger($table, $type) {
		if ($type == "insert") {
			$trigger = "BEGIN
				INSERT INTO ".$this->db->prefix("log")." (table_name, object_id, action, created, modified) VALUES ('$table', NEW.id, 'INSERT', NOW(), NOW());
			END";
		} else if ($type == "update") {
			$trigger = "BEGIN";
			$fields = $this->get_table($table);
			unset($fields['modified']);
			foreach ($fields as $name => $ops) {
				$trigger .= "
				IF OLD.$name != NEW.$name THEN
					INSERT INTO ".$this->db->prefix("log")." (table_name, object_id, action, column_name, old_value, new_value, created, modified) VALUES ('$table', NEW.id, 'UPDATE', '$name', OLD.$name, NEW.$name, NOW(), NOW());
				END IF;";
			}
			$trigger .= "
			END";
		} else if ($type == "delete") {
			$trigger = "BEGIN
				INSERT INTO ".$this->db->prefix("log")." (table_name, object_id, action, created, modified) VALUES ('$table', OLD.id, 'DELETE', NOW(), NOW());
			END";
		}
		return $trigger;
	}

	function get($model) {
		$fields = $this->get_table($model);
		$options = $this->options[$model];
		//SET UP MODEL ARRAY
		$data = array(
			"name" => $model,
			"label" => ucwords(str_replace(array("-", "_"), array(" ", " "), $model)),
			"singular" => rtrim($model, 's'),
			"fields" => array(),
			"relations" => array()
		);
		$data["singular_label"] = ucwords(str_replace(array("-", "_"), array(" ", " "), $data["singular"]));
		$data = array_merge($data, $options);
		$rels = array();
		//ADD FIELDS
		foreach ($fields as $name => $field) {
			$data["fields"][$name] = array();
			$data["fields"][$name]['display'] = ((isset($this->tables[$model][$name])) && ($field['display'] !== "false")) ? true : false;
			if (!isset($field['input_type'])) {
				if ($field['type'] == "text") $field['input_type'] = "textarea";
				else if ($field['type'] == "password") $field['input_type'] = "password";
				else if ($field['type'] == "bool") $field['input_type'] = "checkbox";
				else if ($field['type'] == "category") $field['input_type'] = "category_select";
				else if ($field['type'] == "tags") $field['input_type'] = "tag_input";
				else if (isset($field['upload'])) $field['input_type'] = "file_select";
				else if ($field['type'] == "terms") $field['input_type'] = "multiple_category_select";
				else if (isset($this->tables[$field['type']])) $field['input_type'] = "multiple_select";
				else if (isset($field['references'])) $field['input_type'] = "select";
				else $field['input_type'] = "text";
			}
			$field[$field['type']] = "";
			if (empty($field[$field['input_type']])) $field[$field['input_type']] = "";
			if (empty($field["label"])) $field["label"] = ucwords(str_replace('_', ' ', $name));
			foreach ($field as $k => $v) {
				$data["fields"][$name][$k] = $v;
			}
			if (isset($field['references'])) $rels[$field['references']] = $name;
		}
		//ADD RELATIONS
		foreach ($this->tables as $table => $fields) {
			$relations = $this->get_relations($table, $model, $rels);
			$data['relations'] = array_merge($data['relations'], $relations);
			//foreach ($relations as $m => $r) $data["relations"][] = array("model" => $m, "field" => $r['hook'], "lookup" => $r['lookup'], "ref_field" => $r['ref_field']);
		}
		//ADD ACTIONS
		return $data;
	}
	/**
	 * convert table schema to XML
	 */
	function toXML($model) {
		$xmlDoc = new \DOMDocument();
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
								if (!empty($k)) $node->appendChild($xmlDoc->createAttribute($k))->appendChild($xmlDoc->createTextNode($v));
							}
						}
						break;
					default:
						foreach ($value as $tag) {
							$node = $root->appendChild($xmlDoc->createElement(rtrim($key, 's')));
							foreach ($tag as $k => $v) if (!empty($k)) $node->appendChild($xmlDoc->createAttribute($k))->appendChild($xmlDoc->createTextNode($v));
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
