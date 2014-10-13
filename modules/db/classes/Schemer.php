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
	 * @var array indexes
	 */
	var $indexes = array();
	/**
	 * @var array drop indexes
	 */
	var $index_drops = array();
	/**
	 * @var array Holds uris
	 */
	var $uris = array();
	/**
	 * @var array Holds blocks
	 */
	var $blocks = array();
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
	* @var array Holds entities to be cerated
	*/
	var $entities = array();
	/**
	 * @var array Holds menus
	 */
	var $menus = array();
	/**
	 * @var array Holds taxonomies
	 */
	var $taxonomies = array();
	/**
	 * @var array Holds sql triggers
	 */
	var $triggers = array();
	/**
	 * @var array triggers to drop
	 */
	var $trigger_drops = array();

	var $current;

	var $testMode = false;

	/**#@-*/
	/**
	 * @var array holds mixed in objects
	 */
	var $imported = array();
	/**
	 * @var array holds function names of mixed in objects
	 */
	var $imported_functions = array();

	/**
	 * constructor. loads migrations
	 */
	function __construct($data) {
		$this->db = $data;
		$this->migrations = config("modules");
		foreach ($this->migrations as $i => $m) $this->migrations[$i] = "modules/".$m;
		$this->migrations = array_merge(array("core/app"), $this->migrations, array("app"));
	}

	function set_database($db) {
		$this->db = $db;
	}

	function  clean() {
		$this->tables = $this->table_drops = $this->column_drops = $this->uris = $this->blocks = $this->permits = $this->population = $this->triggers = $this->trigger_drops = $this->menus = $this->taxonomies = array();
	}

	function up($migration) {
		if (is_numeric($migration)) $migration = $this->migrations[$migration];
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
		$to = count($this->migrations);
		//MOVE TO CURRENT MIGRATION
		$current = 0;
		while ($current < $to) {
			$this->up($current);
			$current++;
		}
	}

	function testMode($to=true) {
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
		$table_info = $this->options[$table];
		$primary = array();
		foreach ($fields as $column => $options) {
			if ((isset($options['key'])) && ("primary" == $options['key'])) $primary[] = $column;
			if ($options['type'] == "category") {
				$fields[$column]['references'] = "terms id";
				efault($fields[$column]['alias'], '%taxonomy% %slug%');
			}
		}
		if (empty($primary)) $fields['id'] = star("type:int  auto_increment:  key:primary");
		if (empty($table_info['base'])) {
			if (empty($fields["owner"])) $fields["owner"] = star("type:int  null:  references:users id  owner:  optional:");
			if ($table !== "terms_index") {
				if (empty($fields["groups"])) $fields["groups"] = star("type:terms  taxonomy:groups  optional:");
				if (empty($fields["statuses"])) $fields["statuses"] = star("type:category  label:Status  taxonomy:statuses  optional:");
			}
			if (empty($fields["created"])) $fields["created"] = star("type:datetime  default:0000-00-00 00:00:00  time:insert");
			if (empty($fields["modified"])) $fields["modified"] = star("type:datetime  default:0000-00-00 00:00:00  time:update");
		}
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
		foreach ($this->tables as $table => $fields) {
			$records = $this->db->pdo->query("SHOW TABLES LIKE '".P($table)."'");
			if (false === ($row = $records->fetch())) {
				// NEW TABLE																																													// NEW TABLE
				fwrite(STDOUT, "Creating table ".P($table)."...\n");
				$this->create($table);
				$ts++;
			}
		}
		//UPDATE TABLES WITH COLUMN ALTERATIONS AND FOREIGN KEYS
		foreach ($this->tables as $table => $fields) {
			$table_info = $this->get($table);
			$fields = $table_info['fields'];
			// OLD TABLE																																														// OLD TABLE
			foreach ($fields as $name => $field) {
				if ($field['type'] != "category" && !isset($this->tables[$field['type']])) {
					// REAL COLUMN
					$records = $this->db->pdo->query("SHOW COLUMNS FROM ".P($table)." WHERE Field='".$name."'");
					if (false === ($row = $records->fetch())) {
						// NEW COLUMN																																												// NEW COLUMN
						fwrite(STDOUT, "Adding column $name...\n");
						$this->add($table, $name);
						$cs++;
					} else {
						// OLD COLUMN																																												// OLD COLUMN
						$type = explode(" ", $this->get_sql_type($field));
						if (($row['Type'] != $type[0]) || ((isset($field['default'])) && ($row['Default'] != $field['default'])) || (isset($field['null']) && ($row['Null'] == "NO")) || (!isset($field['null']) && ($row['Null'] == "YES"))) {
							fwrite(STDOUT, "Altering column $name...\n");
							$this->modify($table, $name);
							$ms++;
						}
						if (isset($row['index'])) {
							$index = $this->db->pdo->query("SHOW INDEXES FROM ".P($table)." WHERE key_name='".$name."'");
							if (empty($index)) $this->create_index($table, $name);
						}
					}
				}
				if (isset($field['references']) && ($field['type'] != "category") && ($field['constraint'] !== false)) {
					$fks = $this->db->pdo->query("SELECT * FROM information_schema.STATISTICS WHERE TABLE_NAME='".P($table)."' && COLUMN_NAME='$name' && TABLE_SCHEMA='".$this->db->database_name."'");
					if (false === ($row = $fks->fetch())) {
						// ADD CONSTRAINT																																									// CONSTRAINT
						fwrite(STDOUT, "Adding foreign key ".P($table)."_".$name."_fk...\n");
						$this->add_foreign_key($table, $name);
						$ms++;
					}
				}
			}
			//entity type definition
			$rows = query("entities")->condition("name", $table)->one();
			unset($table_info['fields']);
			unset($table_info['relations']);
			unset($table_info['select']);
			unset($table_info['search']);
			unset($table_info['label_select']);
			unset($table_info['list']);
			if (empty($rows)) {
				// ADD CONTENT TYPE																																														// ADD CONTENT TYPE
				fwrite(STDOUT, "Adding Entity '$table'...\n");
				store("entities", $table_info);
				$is++;
			} else {
				$query = query("entities");
				foreach ($table_info as $k => $v) $query->condition("entities.".$k, $v);
				$rows = $query->all();
				if (empty($rows)) {
					// UPDATE CONTENT TYPE																																												// UPDATE CONTENT TYPE
					fwrite(STDOUT, "Updating Entity '$table'...\n");
					store("entities", $table_info, array("name" => $table));
					$as++;
				}
			}
			$this->generate_model($table);
			$is += $this->populate($table, true);
		}
		foreach ($this->indexes as $idx => $index) {
			$table = reset($index);
			$name = P("").implode("_", $index)."_index";
			$exists = $this->db->pdo->query("SHOW INDEXES FROM ".P($table)." WHERE key_name='".$name."'")->fetch();
			if (empty($exists)) {
				fwrite(STDOUT, "Creating index '$name'...\n");
				call_user_method_array("create_index", $this, $index);
			}
		}
		foreach ($this->index_drops as $idx => $index) {
			$table = reset($index);
			$name = P("").implode("_", $index)."_index";
			$exists = $this->db->pdo->query("SHOW INDEXES FROM ".P($table)." WHERE key_name='".$name."'")->fetch();
			if (!empty($exists)) {
				fwrite(STDOUT, "Dropping index '$name'...\n");
				call_user_method_array("drop_index", $this, $index);
			}
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
		foreach ($this->taxonomies as $taxonomy => $items) {
			$records = query("terms", "select:count(*) as count  where:taxonomy=?  limit:1", array($taxonomy));
			if ($records['count'] == 0) {
				//CREATE TAXONOMY                                                                                     //CREATE TAXONOMY
				fwrite(STDOUT, "Creating taxonomy ".$taxonomy."...\n");
				$is += $this->create_taxonomy($taxonomy);
			} else $is += $this->create_taxonomy($taxonomy, true);
		}
		foreach ($this->uris as $path => $uri) {
			$rows = query("uris")->condition("path", $path)->one();
			if (empty($rows)) {
				// ADD URI																																														// ADD URI
				fwrite(STDOUT, "Adding URI '$path'...\n");
				$this->add_uri($path);
				$us++;
			} else {
				$query = query("uris"); $extra_terms = false;
				foreach ($uri as $k => $v) if (!in_array($k, array("groups", "statuses"))) $query->condition("uris.".$k, $v);
				$rows = $query->all();
				if (!empty($rows)) {
					$s = query("terms_index", "select:terms_index.id as id,terms_index.terms_id.slug as slug")->condition(array(
						"terms_index.type" => "uris",
						"terms_index.rel" => "statuses",
						"terms_index.type_id" => $rows[0]['id']
					))->execute();
					$g = query("terms_index", "select:terms_index.id as id,terms_index.terms_id.slug as slug")->condition(array(
						"terms_index.type" => "uris",
						"terms_index.rel" => "groups",
						"terms_index.type_id" => $rows[0]['id']
					))->execute();
					$statuses = (empty($uri['statuses'])) ? array() : explode(",", $uri['statuses']);
					$groups = (empty($uri['groups'])) ? array() : explode(",", $uri['groups']);
					$found_s = $found_g = array();
					//check the existing statuses in the db to find items to remove
					foreach ($s as $status) {
						if (!in_array($status['slug'], $statuses)) {
							$statuses[] = "-".$status['slug'];
							$extra_terms = true;
						} else $found_s[] = $status['slug'];
					}
					//check the existing groups in the db to find items to remove
					foreach ($g as $group) {
						if (!in_array($group['slug'], $groups)) {
							$groups[] = "-".$group['slug'];
							$extra_terms = true;
						} else $found_g[] = $group['slug'];
					}
					//check the requested statuses to see if any are not already stored
					foreach ($statuses as $status) {
						if (!in_array($status, $found_s)) {
							$extra_terms = true;
						}
					}
					//check the requested groups to see if any are not already stored
					foreach ($groups as $group) {
						if (!in_array($group, $found_g)) {
							$extra_terms = true;
						}
					}
					if (!empty($statuses)) $uri['statuses'] = implode(",", $statuses);
					if (!empty($groups)) $uri['groups'] = implode(",", $groups);
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
				foreach ($roles as $ridx => $ops) {
					$role = $ops['role'];
					$permits = $this->get_permits($model, $action, $ops);
					foreach ($permits as $permit) {
						$query = ''; foreach ($permit as $k => $v) if (!in_array($k, array("status", "roles", "terms"))) $query .= $k."='$v' && ";
						foreach ($permit['roles'] as $tax => $items) {
							$items = explode(",", $items);
							foreach ($items as $item) {
								$query .= "id IN (SELECT type_id FROM ".P("terms_index")." ti INNER JOIN ".P("terms")." t ON t.id=ti.terms_id WHERE type='permits' && t.taxonomy='$tax' && t.slug='$item') && ";
							}
						}
						//echo $query;
						$query = query("permits", "where:".rtrim($query, '& '));
						foreach ($ops['terms'] as $field => $value) $query->where("permits.id IN (SELECT type_id FROM ".P("terms_index")." ti INNER JOIN ".P("terms")." t ON t.id=ti.terms_id WHERE type='permits' && t.taxonomy='$field' && t.slug='$value')");
						$row = $query->one();
						if (empty($row)) {
							// ADD PERMIT																																										// ADD PERMIT
							fwrite(STDOUT, "Adding $permit[priv_type] permit on $model::$action for $role...\n");
							$this->add_permit($permit);
							$ps++;
						} else {
							$update = false;
							$new_terms = $permit['terms'];
							foreach ($new_terms as $tax => $items) {
								$new_terms[$tax] = array();
								foreach (explode(",", $items) as $item) {
									$new_terms[$tax][$item] = true;
								}
							}
							$current_terms = query("terms", "select:id,taxonomy,slug  where:id IN (SELECT terms_id FROM ".P("terms_index")." WHERE type='permits' && type_id='".$row['id']."' && rel='terms')");
							$remove_terms = array();
							foreach ($current_terms as $term) {
									if (!isset($new_terms[$term['taxonomy']][$term['slug']])) {
										$remove_terms[] = $term['id'];
									} else unset($new_terms[$term['taxonomy']][$term['slug']]);
							}
							foreach ($new_terms as $tax => $items) if (empty($items)) unset($new_terms[$tax]);
							if (!empty($new_terms) || !empty($remove_terms)) {
								// UPDATE PERMIT																																								// UPDATE PERMIT
								fwrite(STDOUT, "Updating $permit[priv_type] permit on $model::$action for $role...\n");
								$this->update_permit($permit, $new_terms, $remove_terms);
								$ps++;
							}
						}
					}
				}
			}
		}
		foreach ($this->menus as $menu => $items) {
			$records = query("menus", "select:count(*) as count  where:menu=?  limit:1", array($menu));
			if ($records['count'] == 0) {
				//CREATE MENU                                                                                          //CREATE MENU
				fwrite(STDOUT, "Creating menu ".$menu."...\n");
				$is += $this->create_menu($menu);
			} else $is += $this->create_menu($menu, true);
		}
		foreach ($this->tables as $table => $fields) $is += $this->populate($table, false);
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
	function create($name, $backup=false, $write=true) {
		$fields = $this->get_table($name);
		$this->drop_table($name);
		$sql = "CREATE TABLE `".P($name)."` (";
		$primary = array();
		$index = array();
		$sql_fields = "";
		$primary_fields = "";
		foreach ($fields as $fieldname => $options) {
			if ($options['type'] != "category" && !isset($this->tables[$options['type']])) {
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
		foreach($index as $k) $sql .= ", KEY `".$k."` (`$k`)";
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
	 * Run SQL to add an index
	 * @param string $table the name of the table from tables array
	 * @param string $name the name of column
	 */
	function create_index($table, $name) {
		$args = func_get_args();
		$table = array_shift($args);
		$sql = "CREATE INDEX ".P($table)."_".implode("_", $args)."_index ON ".P($table)." (".implode(", ", $args).")";
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
		$sql = "ALTER TABLE ".P($table)." DROP INDEX ".P($table)."_".implode("_", $args)."_index";
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
		if (false !== strpos($table, "  ")) {
			list($table, $ops) = explode("  ", $table, 2);
			$ops = star($ops);
		} else $ops = array();
		efault($this->tables[$table], array());
		$additional = array();
		foreach ($args as $col) {
			$col = star($col);
			$colname = array_shift($col);
			if ($col['type'] !== "terms" && isset($this->tables[$col['type']])) {
				$ref_table_name = (empty($col['table'])) ? $table."_".$colname : $col['table'];
				$ref_table_def = array($ref_table_name,
					"owner  type:int  null:  references:users id  update:cascade  delete:cascade  optional:",
					$table."_id  type:int  default:0  references:$table id  null:  update:cascade  delete:cascade",
					"position  type:int  ordered:".$table."_id  optional:"
				);
				if ($ref_table_name != $col['type']) {
					$ref_table_def[] = $col['type']."_id  type:int  default:0  references:$col[type] id  update:cascade  delete:cascade";
					$this->index($ref_table_name, $table."_id", $col['type']."_id");
				}
				$additional[] = $ref_table_def;
			}
			$this->tables[$table][$colname] = $col;
		}
		$search_cols = array_keys($this->tables[$table]);
		foreach ($search_cols as $colname_index => $colname_value) if (isset($this->tables[$this->tables[$table][$colname_value]['type']])) unset($search_cols[$colname_index]);
		efault($this->options[$table], array("select" => "$table.*", "search" => $table.'.'.implode(",$table.", $search_cols)));
		foreach ($ops as $k => $v) $this->options[$table][$k] = $v;
		if (isset($ops['base']) && $ops['base'] !== $table) {
			//find the root
			$base = $ops['base'];
			while (!empty($this->options[$base]["base"])) $base = $this->options[$base]["base"];
			$this->tables[$table][$base."_id"] = array("type" => "int", "references" => $base." id");
		}
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
		entity_save($entity, $uri);
	}

	/**
	 * Update a uri in the db from the schema
	 * @param string $path the path of the uri
	 */
	function update_uri($path, $uri=array()) {
		if (empty($uri)) $uri = $this->uris[$path];
		$entity = (empty($uri['type'])) ? "uris" : $uri['type'];
		entity_save($entity, $uri, array("path" => $path));
		/*
		if (!empty($extra_terms)) {
			foreach ($extra_terms as $tid) remove("terms_index", "id:".$tid);
		}
		*/
	}

	/**
	 * Add a uri to the schema
	 * @param string $path the path
	 * @param star $args other fields
	 */
	function uri($path, $args=array(), $groups=array()) {
		$options = array();
		$args = star($args);
		$args['path'] = $path;
		efault($args['title'], ucwords(str_replace("-", " ", $path)));
		efault($args['statuses'], "published");
		if ($this->current != "core/app") efault($args['prefix'], $this->current."/views/");
		$this->uris[$path] = $args;
	}

	/**
	 * Add a content type to the database
	 * @param string $type the path of the uri
	 */
	function add_entity($name) {
		$record = $this->entities[$name];
		store("entities", $record);
	}

	/**
	 * Update a content type
	 * @param string $type the type
	 */
	function update_entity($name, $record=array()) {
		if (empty($record)) $record = $this->entities[$name];
		store("entities", $record, array("name" => $name));
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
	function block($path, $content, $ops=array()) {
		$ops = star($ops);
		$ops["content"] = $content;
		efault($this->blocks[$path], array());
		$this->blocks[$path][] = $ops;
	}

	/**
	 * Add a permit to the db from the schema
	 * @param array $permit the permit to add
	 */
	function add_permit($permit) {
		$roles = $terms = array();
		if (isset($permit['roles'])) {
			$roles = $permit['roles'];
			unset($permit['roles']);
		}
		if (isset($permit['terms'])) {
			$terms = $permit['terms'];
			unset($permit['terms']);
		}
		store("permits", $permit);
		$id = sb("permits")->insert_id;
		foreach ($roles as $tax => $items) {
			$items = explode(",", $items);
			foreach ($items as $item) {
				$term = get("terms", array("taxonomy" => $tax, "slug" => $item), array("limit" => 1));
				store("terms_index", array("type" => 'permits', "type_id" => $id, "terms_id" => $term['id'], "rel" => "roles"));
			}
		}
		foreach ($terms as $tax => $items) {
			$items = explode(",", $items);
			foreach ($items as $item) {
				$term = get("terms", array("taxonomy" => $tax, "slug" => $item), array("limit" => 1));
				store("terms_index", array("type" => 'permits', "type_id" => $id, "terms_id" => $term['id'], "rel" => "terms"));
			}
		}
	}

	/**
	 * Update a permit in the db from the schema
	 * @param string $permit the updated record
	 */
	function update_permit($permit, $new, $remove) {
		unset($permit['roles']);
		unset($permit['terms']);
		$permit = get("permits", $permit, array("limit" => 1));
		foreach($remove as $id) {
			remove("terms_index", "type:permits  type_id:".$permit['id']."  terms_id:".$id."  rel:terms");
		}
		foreach($new as $tax => $items) {
			foreach ($items as $item => $val) {
				$term = get("terms", array("taxonomy" => $tax, "slug" => $item), array("limit" => 1));
				store("terms_index", array("type" => 'permits', "type_id" => $permit['id'], "terms_id" => $term['id'], "rel" => "terms"));
			}
		}
	}

	/**
	 * Add a permit to the schema
	 * @param string $on the model and action to apply the permit on
	 * @param star $args a field string where keys are roles and values are priv_type and status
	 */
	function permit($on, $args, $terms=array()) {
		$on = explode("::", $on);
		$args = star($args);
		foreach ($args as $role => $type) $args[$role] = array("role" => $role, "type" => $type, "terms" => $terms);
		efault($this->permits[$on[0]], array());
		efault($this->permits[$on[0]][$on[1]], array());
 		$this->permits[$on[0]][$on[1]] = array_merge($this->permits[$on[0]][$on[1]], array_values($args));
	}

	/**
	 * Get permits as records from a schema entry
	 * @param string $model the model the permit is applied to
	 * @param string $action the function on the model that the permit is applied to
	 * @param string $role the role that the permit is applied to
	 */
	function get_permits($model, $action, $ops) {
		$permit = array("related_table" => P($model), "action" => $action, "roles" => array(), "terms" => array());
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
		efault($this->menus[$menu], array());
		foreach ($args as $item) $this->menus[$menu][] = star($item);
	}

	/**
	 * Add a taxonomy to the schema
	 * @param string $menu the name of the menu
	 * @param star $item the item
	 */
	function taxonomy($taxonomy, $item) {
		$args = func_get_args();
		$taxonomy = array_shift($args);
		efault($this->taxonomies[$taxonomy], array());
		foreach ($args as $item) $this->taxonomies[$taxonomy][] = star($item);
	}

	/**
	 * add records to be populated immediately upon the creation of a table
	 * @param string $table the name of the table
	 * @param star $match the fields which if exist, do not store this record
	 * @param star $others the other, non-unique fields
	 */
	function store($table, $match, $others=array(), $immediate=false) {
		$merge = array($table => array(array("match" => star($match), "others" => star($others), "immediate" => $immediate)));
		$this->population = array_merge_recursive($this->population, $merge);
	}

	/**
	 * create blocks
	 * @param string $path the name of the path
	 */
	function create_blocks($path, $blocks=array()) {
		$rs = 0;
		$uri = get("uris", "path:$path", "limit:1");
		if (!empty($blocks)) foreach ($blocks as $block) $rs += $this->create_block($uri, $block);
		return $rs;
	}

	/**
	 * create block
	 * @param string $path the name of the path
	 * @param array $block
	 */
	function create_block($uri, $block) {
		efault($block['region'], "content");
		$block['uris_id'] = $uri['id'];
		$content = $block['content'];
		unset($block['content']);
		$results = get("blocks", $block);
		if (empty($results)) {
			fwrite(STDOUT, "Creating block for /".$uri['path']."...\n");
			efault($block['position'], "");
			$block['content'] = $content;
			store("blocks", $block);
			return 1;
		}
		return 0;
	}

	/**
	 * insert records from a menu
	 * @param string $menu the name of the menu
	 */
	function create_menu($menu, $update=false) {
		$rs = 0;
		$items = $this->menus[$menu];
		if (!empty($items)) foreach ($items as $record) $rs += $this->create_menu_item($menu, $record, $update);
		return $rs;
	}

	function create_menu_item($menu, $item, $update=false) {
		$children = empty($item['children']) ? array() : $item['children'];
		unset($item['children']);
		$item['menu'] = $menu;
		$match = array();
		foreach ($item as $k => $v) {
			if ($k == "groups" || $k == "statuses") $k = "menus.".$k;
			$match[$k] = $v;
		}
		$record = query("menus")->conditions($match)->one();
		if (empty($record)) {
			if ($update) fwrite(STDOUT, "Creating $menu menu item...\n");
			store("menus", $item);
			$id = sb("menus")->insert_id;
			$count = 1;
		} else $id = $record['id'];
		foreach ($children as $child) {
			$child = star($child);
			$child['parent'] = $id;
			$count += $this->create_menu_item($menu, $child);
		}
		return $count;
	}

	/**
	 * insert records from a taxonomy
	 * @param string $taxonomy the name of the taxonomy
	 */
	function create_taxonomy($taxonomy, $update=false) {
		$rs = 0;
		$items = $this->taxonomies[$taxonomy];
		if (!empty($items)) foreach ($items as $record) $rs += $this->create_taxonomy_item($taxonomy, $record, $update);
		return $rs;
	}

	function create_taxonomy_item($taxonomy, $item, $update=false) {
		$children = empty($item['children']) ? array() : $item['children'];
		unset($item['children']);
		$item['taxonomy'] = $taxonomy;
		$record = query("terms")->conditions($item)->one();
		if (empty($record)) {
			if ($update) fwrite(STDOUT, "Creating $taxonomy taxonomy term...\n");
			store("terms", $item);
			$id = sb("terms")->insert_id;
			$count = 1;
		} else $id = $record['id'];
		foreach ($children as $child) {
			$child = star($child);
			$child['parent'] = $id;
			$count += $this->create_taxonomy_item($taxonomy, $child);
		}
		return $count;
	}

	/**
	 * insert records from population
	 * @param string $table the name of the table to populate
	 */
	function populate($table, $immediate=false) {
		$rs = 0;
		$pop = $this->population[$table];
		if (!empty($pop)) {
			foreach ($pop as $record) {
				if ($record['immediate'] == $immediate) {
					$match = query($table)->conditions($record['match'])->one();
					if (empty($match)) {
						$store = array_merge($record['match'], $record['others']);
						fwrite(STDOUT, "Inserting $table record...\n");
						store($table, $store);
						$rs++;
					}
				}
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
		exec("./sb generate model ".$name);
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
	function migrate() {
		if ($this->update()) {
			fwrite(STDOUT, "Database update completed.\n");
		} else {
			fwrite(STDOUT, "The Database already matches the schema.\n");
		}
		fwrite(STDOUT, "Generating Models...\n");
		fwrite(STDOUT, "Run 'sb generate models' to generate models manually.\n");
		foreach ($this->tables as $table => $fields) $this->generate_model($table);
		fwrite(STDOUT, "Generating CSS...\n");
		fwrite(STDOUT, "Run 'sb generate css' to generate CSS manually.\n");
		include(end(locate("generate/css/css.php", "script")));
	}

	function generate_model($table) {
		import("lib/Renderer", "core");
		$data = $this->get($table);
		$this->toXML($data);
		$this->toJSON($data);
		$result = end(locate("generate/model/update.php", "script"));
		$render_prefix = reset(explode("/model/", str_replace(BASE_DIR, "", $result)))."/model/";
		$o = BASE_DIR."/var/models/".ucwords($table)."Model.php"; //output
		assign("model", $table);
		$base = "";
		//if (!empty($this->options[$table]['base'])) $base = $this->options[$table]['base'];
		$data = capture(array($base."/base", "base"), "", $render_prefix);
		file_put_contents($o, $data);
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
				INSERT INTO ".P("log")." (table_name, object_id, action, created, modified) VALUES ('$table', NEW.id, 'INSERT', NOW(), NOW());
			END";
		} else if ($type == "update") {
			$trigger = "BEGIN";
			$fields = $this->get_table($table);
			unset($fields['modified']);
			foreach ($fields as $name => $ops) { $trigger .= "
				IF OLD.$name != NEW.$name THEN
					INSERT INTO ".P("log")." (table_name, object_id, action, column_name, old_value, new_value, created, modified) VALUES ('$table', NEW.id, 'UPDATE', '$name', OLD.$name, NEW.$name, NOW(), NOW());
				END IF;";
			}
			$trigger .= "
			END";
		} else if ($type == "delete") {
			$trigger = "BEGIN
				INSERT INTO ".P("log")." (table_name, object_id, action, created, modified) VALUES ('$table', OLD.id, 'DELETE', NOW(), NOW());
			END";
		}
		return $trigger;
	}

	function get($model) {
		$sb = sb();
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
		//ADD FIELDS
		foreach($fields as $name => $field) {
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
			efault($field[$field['input_type']], "");
			efault($field["label"], format_label($name));
			foreach ($field as $k => $v) {
				$data["fields"][$name][$k] = $v;
			}
		}
		//ADD RELATIONS
		foreach ($this->tables as $table => $fields) {
			$relations = $this->get_relations($table, $model);
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
