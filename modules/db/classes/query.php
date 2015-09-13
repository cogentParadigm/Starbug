<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/db/query.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * The query class. provides a generic query representation
 * usage:
 * $query = new query("users");
 * $query->select("first_name, last_name")->condition(array("id" => array(1, 2, 3), "users.groups" => "admin"))
 * 			 ->sort('last_name')->limit(10)->skip(20);
 * @ingroup db
 */
namespace Starbug\Core;
use \IteratorAggregate;
use \ArrayAccess;
use \ArrayIterator;
use \PDO;
class query implements IteratorAggregate, ArrayAccess {

	const PHASE_VALIDATION = 0;
	const PHASE_STORE = 1;
	const PHASE_AFTER_STORE = 2;
	const PHASE_BEFORE_DELETE = 3;
	const PHASE_AFTER_DELETE = 4;

	//query array, holds the parts of the query
	public $query = array(
		'distinct' => false,
		'select' => array(),
		'from' => array(),
		'where' => array(),
		'group' => array(),
		'having' => array(),
		'sort' => array(),
		'order' => array(),
		'limit' => false,
		'skip' => 0,
		'join' => array(),
		'on' => array()
	);

	public $db; //pdo instance
	public $model; //model name
	public $base_collection; //base collection
	public $last_collection; //last added collection
	public $mode = "query"; // mode (query, delete, insert, update)

	public $clauses = array();
	public $statements = array();
	public $parameters = array();
	public $fields = array();
	public $unvalidated = array();
	public $exclusions = array();
	public $result = false;
	public $pager = null;

	public $parameter_count = array();

	public $sets = array();
	public $set = "default";

	public $sql = null;
	public $count_sql = null;
	public $dirty = true;
	public $validated = false;
	public $executed = false;
	public $op = "default";
	public $tags = array();
	public $operations = array();
	public $hooks = array();

	public $raw = false;

	public $store_on_errors = false;
	protected $models;
	protected $hook_builder;
	protected $config;

	/**
	 * create a new query
	 * @param string $collection the name of the primary table/collection to query
	 * @param array $params parameters to merge into the query
	 */
	function __construct(DatabaseInterface $db, ConfigInterface $config, ModelFactoryInterface $models, HookFactoryInterface $hook_builder, $collection, $params = array()) {
		$this->db = $db;
		$this->config = $config;
		$this->models = $models;
		$this->hook_builder = $hook_builder;
		$params = star($params);
		$this->from($collection);
		foreach ($params as $key => $value) $this->{$key}($value);
	}

	/**************************************************************
	 * operation tagging, logging, and undoing
	 **************************************************************/

	/**
	 * Open a tag to start tagging operations so they can be referenced or canceled by a tag name
	 */
	function openTag($tag) {
		$this->op = $tag;
	}

	/**
	 * Close the operation tag (sets it back to 'default')
	 */
	function closeTag() {
		$this->op = "default";
	}

	/**
	 * Log an operation
	 */
	function operation($name, $args = array()) {
		$args["operation"] = $name;
		$args['tag'] = $this->op;
		$this->operations[$this->op][] = $args;
	}

	/**
	 * undo an operation
	 */
	function undo($name, $args = array()) {
		$any = false;
		//$this->operation[$tag] = $operations
		foreach ($this->operations as $operations) {
			foreach ($operations as $operation) {
				if ($operation['operation'] === $name) {
					$undo = true;
					if (!empty($args)) foreach ($args as $k => $v) if ($operation[$k] !== $v) $undo = false;
					if ($undo) {
						$any = true;
						if ($name === "select") {
							unset($this->query["select"][$operation["alias"]]);
						}
					}
				}
			}
		}
		if ($any) $this->dirty();
	}

	/**************************************************************
	 * Query building functions
	 **************************************************************/

	//SELECT, DELETE

	/**
	 * specify fields for selection
	 * @param string $field the name of the field
	 */
	function select($field, $prefix = "") {
		if (is_array($field)) {
			foreach ($field as $f) $this->select($f, $prefix);
		} else {
			if (!empty($prefix)) $field = $prefix.".".$field;
			$field = $this->parse_collection($field);
			$selection = $this->parse_fields($field['collection']);
			$alias = ($field['alias'] === $field['collection']) ? $selection : $field['alias'];
			$this->query['select'][$alias] = $selection;
			$this->operation("select", array("alias" => $alias));
			$this->dirty();
		}
		return $this;
	}

	//FROM

	/**
	 * add a collection or table to be queried
	 * @param string $collection the name of the table or collection
	 */
	function from($collections) {
		$collections = $this->parse_collections($collections);
		$collection = array_shift($collections);
		$this->query['from'][$collection['alias']] = $collection['collection'];
		$this->model = $collection['collection'];
		$this->base_collection = $this->last_collection = $collection['alias'];
		$this->operation("from", $collection);
		foreach ($collections as $collection) {
			$this->query['from'][$collection['alias']] = $collection['collection'];
			if (!empty($collection['join'])) $this->query['join'][$collection['alias']] = $collection['join'];
			if (!empty($collection['on'])) $this->query['on'][$collection['alias']] = $collection['on'];
			$this->operation('join', $collection);
		}
		$this->dirty();
		return $this;
	}

	//JOIN

	/**
	 * join a collection or table to be queried
	 * @param string $collection the name of the table or collection
	 * @param string $join the join type
	 */
	function join($collection, $type = "") {
		$collection = $this->parse_collection($collection);
		$this->query['from'][$collection['alias']] = $collection['collection'];
		$this->last_collection = $collection['alias'];
		if (!empty($type)) $this->query['join'][$collection['alias']] = $type;
		if (!empty($collection['on'])) $this->query['on'][$collection['alias']] = $collection['on'];
		$this->operation("join", $collection);
		$this->dirty();
		return $this;
	}

	/**
	 * join a collection or table to be queried using an INNER join
	 * @param string $collection the name of the table or collection
	 */
	function innerJoin($collection) {
		return $this->join($collection, "INNER");
	}

	/**
	 * join a collection or table to be queried using a LEFT join
	 * @param string $collection the name of the table or collection
	 */
	function leftJoin($collection) {
		return $this->join($collection, "LEFT");
	}

	/**
	 * join a collection or table to be queried using a RIGHT join
	 * @param string $collection the name of the table or collection
	 */
	function rightJoin($collection) {
		return $this->join($collection, "RIGHT");
	}

	//ON

	/**
	 * specify the on clause for a join
	 * @param string $expr the ON expression (not including 'ON ')
	 * @param string $collection the name of the table or collection
	 */
	function on($expr, $collection = "") {
		if (empty($collection)) $collection = $this->last_collection;
		$this->query['on'][$collection] = $expr;
		$this->dirty();
		return $this;
	}

	//WITH

	/**
	 * include a referenced item
	 * @param string $expr the ON expression (not including 'ON ')
	 * @param string $collection the name of the table or collection
	 */
	function with($field, $collection = "", $mode = "select", $token = null) {
		$return = false;
		$parsed = $this->parse_collection($field);
		list($field, $alias) = array($parsed['collection'], $parsed['alias']);
		if (empty($collection)) $collection = $this->last_collection;
		$table = $this->query['from'][$collection];
		$schema = column_info($table, $field);
		if ($schema['entity'] !== $table) {
			$entity_collection = $collection."_".$schema['entity'];
			if (!isset($this->query['from'][$entity_collection])) {
				$base_entity = entity_base($table);
				$this->join($schema['entity']." as ".$entity_collection);
				if ($schema['entity'] === $base_entity) $this->on($entity_collection.".id=".$collection.".".$base_entity."_id");
				else $this->on($entity_collection.".".$base_entity."_id=".$collection.".".$base_entity."_id");
			}
			$table = $schema['entity'];
			$collection = $entity_collection;
		}
		if (isset($schema['references'])) {
			$ref = explode(" ", $schema['references']);
			$type = "";
			if (isset($schema['null'])) $type = "left";
			$this->join($ref[0]." as ".$collection."_".$alias)->on($collection."_".$alias.".".$ref[1]."=".$collection.".".$field);
		} else if ($this->models->has($schema['type'])) {
			$type_schema = $this->config->get($schema['type'], 'json');
			if (is_null($token)) $token = empty($type_schema['label_select']) ? $collection."_".$alias.".id" : str_replace($schema['type'], $collection."_".$alias, $type_schema['label_select']);
			else $token = $collection."_".$alias.".".$token;
			if (empty($schema['table'])) $schema['table'] = $table."_".$field;
			if ($schema['table'] == $schema['type']) {
				//no lookup required
				if ($mode == "select") {
					$return = "(SELECT GROUP_CONCAT(".$token.") FROM ".P($schema['type'])." ".$collection."_".$alias." WHERE ".$collection."_".$alias.".".$table."_id=".$collection.".id)";
				} else if ($mode == "where" || $mode == "condition") {
					$return = "(SELECT ".$token." FROM ".P($schema['type'])." ".$collection."_".$alias." WHERE ".$collection."_".$alias.".".$table."_id=".$collection.".id)";
				} else if ($mode == "group" || $mode == "set") {
					$this->join($schema['type']." as ".$collection."_".$alias)
								->on($collection."_".$alias.".".$table."_id=".$collection.".id");
				}
			} else {
				//use lookup table
				if ($mode == "select") {
					$return = "(SELECT GROUP_CONCAT(".$token.") FROM ".P($schema['table'])." ".$collection."_".$alias."_lookup INNER JOIN ".P($schema['type'])." ".$collection."_".$alias." ON ".$collection."_".$alias.".id=".$collection."_".$alias."_lookup.".$field."_id WHERE ".$collection."_".$alias."_lookup.".$table."_id=".$collection.".id)";
				} else if ($mode == "where" || $mode == "condition") {
					$return = "(SELECT ".$token." FROM ".P($schema['table'])." ".$collection."_".$alias."_lookup INNER JOIN ".P($schema['type'])." ".$collection."_".$alias." ON ".$collection."_".$alias.".id=".$collection."_".$alias."_lookup.".$field."_id WHERE ".$collection."_".$alias."_lookup.".$table."_id=".$collection.".id)";
				} else if ($mode == "group" || $mode == "set") {
					$this->join($schema['table']." as ".$collection."_".$alias."_lookup")
								->on($collection."_".$alias."_lookup.".$table."_id=".$collection.".id")
								->join($schema['type']." as ".$collection."_".$alias)
								->on($collection."_".$alias.".id=".$collection."_".$alias."_lookup.".$field."_id");
				}
			}
		}
		$this->dirty();
		return $return;
	}

	//CONDITIONS

	/**
	 * add a condition. you will probably want to use a more specific where or having function
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $op the operator (eg. '=', '<', '>')
	 * @param star $ops pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function condition($field, $value = "", $op = "=", $ops = array()) {
		if (is_array($field)) {
			foreach ($field as $k => $v) $this->condition($k, $v, $op, $ops);
			return $this;
		}
		if (is_null($value)) $value = "";
		$this->operation("condition", array("field" => $field, "value" => $value, "op" => $op, "ops" => $ops));
		$set = $this->set;
		$condition = array_merge(array("con" => "&&", "set" => $this->set, "value" => $value, "op" => $op), $this->parse_condition($field), star($ops));
		if (in_array($condition['op'], array("=", "!=", "IN", "NOT IN"))) $condition = array_merge($condition, $this->parse_field($condition['field'], "condition"));
		else $condition['field'] = $this->parse_field($condition['field'], "group");
		if (isset($condition['set']) && isset($condition['field'])) $set = $condition['set'];
		$this->query['where'][$set][] = $condition;
		$this->dirty();
		return $this;
	}
	function conditions($fields, $op = "=", $ops = array()) {
		if ($fields instanceof query) {
			foreach ($fields->operations as $set => $operations) {
				foreach ($operations as $operation) {
					if ($operation['operation'] == "condition") $this->condition($operation['field'], $operation['value'], $operation['op'], $operation['ops']);
				}
			}
			return $this;
		} else return $this->condition(star($fields), "", $op, $ops);
	}

	/**
	 * add a condition. you will probably want to use a more specific where or having function
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $op the operator (eg. '=', '<', '>')
	 * @param star $ops pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function andCondition($field, $value, $op = "=", $ops = array()) {
		return $this->condition($field, $value, $op, $ops);
	}

	/**
	 * add a condition. you will probably want to use a more specific where or having function
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $op the operator (eg. '=', '<', '>')
	 * @param star $ops pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function orCondition($field, $value, $op = "=", $ops = array()) {
		return $this->condition($field, $value, $op, array_merge(array("con" => "||"), star($ops)));
	}



	/**
	 * add a parameter
	 * @param string $name the parameter name
	 * @param mixed $value the parameter value
	 */
	function param($name, $value = null) {
		if (!is_array($name)) $name = array($name => $value);
		foreach ($name as $k => $v) $this->parameters[":".$k] = $v;
		return $this;
	}
	function params($name, $value = null) {
		return $this->param($name, $value);
	}

	/**
	 * add a where condition using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function where($clause, $options = array()) {
		$condition = array_merge($this->parse_condition($clause), array('con' => '&&', 'set' => $this->set), star($options));
		$condition['field'] = $this->parse_fields($condition['field'], "where");
		$this->query['where'][$condition['set']][] = $condition;
		$this->dirty();
		return $this;
	}

	/**
	 * add a WHERE condition using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function andWhere($clause, $options = array()) {
		return $this->where($clause, $options);
	}

	/**
	 * add a WHERE condition using || as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function orWhere($clause, $options = array()) {
		return $this->where($clause, array_merge(array('con' => '||'), star($options)));
	}

	/**
	 * add a field or fields to group by
	 * @param string $column the column or group by statement
	 */
	function group($column) {
		$this->query['group'][$this->parse_fields($column, "group")] = 1;
		$this->dirty();
		return $this;
	}

	/**
	 * add a having condition
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $op the operator (eg. '=', '<', '>')
	 * @param star $ops pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function havingCondition($field, $value, $op = "=", $ops = array()) {
		return $this->condition($field, $value, $op, array_merge(array("set" => ($this->set == "default" ? "having" : $this->set)), star($ops)));
	}

	/**
	 * add a condition using the && logical connective
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $op the operator (eg. '=', '<', '>')
	 * @param star $ops pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function andHavingCondition($field, $value, $op = "=", $ops = array()) {
		return $this->havingCondition($field, $value, $op, $ops);
	}

	/**
	 * add a having condition using the || logical connective
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $op the operator (eg. '=', '<', '>')
	 * @param star $ops pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function orHavingCondition($field, $value, $op = "=", $ops = array()) {
		return $this->havingCondition($field, $value, $op, array_merge(array("con" => "||"), star($ops)));
	}

	/**
	 * add a HAVING clause using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function having($clause, $options = array()) {
		return $this->where($clause, array_merge(array("set" => ($this->set == "default" ? "having" : $this->set)), star($options)));
	}

	/**
	 * add a HAVING clause using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function andHaving($clause, $options = array()) {
		return $this->having($clause, $options);
	}

	/**
	 * add a HAVING clause using || as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function orHaving($clause, $options = array()) {
		return $this->having($clause, array_merge(array("con" => "||"), star($options)));
	}

	function set($field, $value) {
		$this->fields[$this->parse_field($field, "set")] = $value;
		$this->dirty();
		return $this;
	}

	function fields($fields) {
		$fields = star($fields);
		foreach ($fields as $k => $v) $this->set($k, $v);
		return $this;
	}

	function open($set, $con = "&&", $nest = false) {
		$this->condition('#'.$set, null, "=", "con:".$con);
		if (!$nest && !empty($this->sets)) $this->close();
		$this->sets[] = $this->set;
		$this->set = $set;
		return $this;
	}

	function close() {
		if (!empty($this->sets)) $this->set = array_pop($this->sets);
		return $this;
	}

	/**
	 * add a field or fields to sort by
	 * @param string $column the column or ORDER BY statement
	 * @param int $direction (optional) sorting direction (-1 or 1)
	 */
	function sort($column, $direction = 0) {
		$this->query['sort'][$column] = $direction;
		$this->dirty();
		return $this;
	}

	/**
	 * add a limit
	 * @param int/string $limit the limit or limit statement
	 */
	function limit($limit) {
		if (false !== strpos($limit, ",")) {
			list($skip, $limit) = explode(",", $limit);
			$this->skip($skip);
		}
		$this->query['limit'] = trim($limit);
		$this->dirty();
		return $this;
	}

	/**
	 * set the number of records to skip
	 * @param int $skip the number of records to skip
	 */
	function skip($skip) {
		$this->query['skip'] = trim($skip);
		$this->dirty();
		return $this;
	}

	/**
	 * add search conditions to search one or more fields for some words
	 * @param string $keywords a natural language search string which can include operators 'and' and 'or' and quotes for exact matches
	 * @param string $fields a comma delimited list of columns to search on (you can escape a comma with a backslash)
	 * examples,
	 *
	 * search string: 'beef and broccoli'
	 * fields: 'name,description'
	 * conditions: ((name LIKE '%beef%' OR description LIKE '%beef%') and (name LIKE '%broccoli%' OR description LIKE '%broccoli%'))
	 */
	function search($keywords = "", $fields = "") {
		//if there are no search terms, there's nothing to do
		if (empty($keywords)) return $this;

		//if no fields are passed, search the default fields of all tables in the query
		if (empty($fields)) {
			$fieldsets = array();
			foreach ($this->query['from'] as $alias => $model) {
				$schema = $this->config->get($model, 'json');
				if (!empty($schema['search']) && !isset($fieldsets[$model])) $fieldsets[$model] = $schema['search'];
			}
			$fields = implode(",", $fieldsets);
		}

		//split tokens (allowing escaped commas)
		$search_fields = preg_split('~(?<!\\\)' . preg_quote(",", '~') . '~', $fields);
		//unescape those commas
		foreach ($search_fields as $sfk => $sfv) $search_fields[$sfk] = str_replace("\,", ",", $sfv);


		//generate the conditions
		$this->where($this->search_clause($keywords, $search_fields));

		return $this;
	}

	function action($action, $collection = "") {
		if (empty($collection)) $collection = $this->last_collection;
		if ($this->has($collection)) {
			$type = $this->query['from'][$collection];
			$base_type = entity_base($type);
			$join = true;
		} else {
			$type = $collection;
			$join = false;
		}

		$columns = column_info($type);
		$user_columns = column_info("users");

		if ($join) {
			//join permits - match table and action
			if (!$this->has("permits")) $this->innerJoin("permits")->on("'".$type."' LIKE permits.related_table && '".$action."' LIKE permits.action");
			//global or object permit
			$this->where("('global' LIKE permits.priv_type || (permits.priv_type='object' && permits.related_id=".$collection.".id))");

			//determine what relationships the object must bear - defined by object_access fields
			foreach ($columns as $cname => $column) {
				if (isset($column['object_access'])) {
					if ($this->models->has($column['type'])) {
						//multiple reference
						$object_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
						$permit_field = "object_".$cname;
						$ref = $cname."_id";
						$target = ($type == $column['entity']) ? "id" : $column['entity']."_id";
						$this->where("(permits.".$permit_field." is null || permits.".$permit_field." IN (SELECT ".$ref." FROM ".P($object_table)." o WHERE o.".$column['entity']."_id=".$collection.".".$target."))");
					} else {
						//single reference
						$object_field = $cname;
						$permit_field = "object_".$cname;
						$this->where("(permits.".$permit_field." is null || permits.".$permit_field."=".$collection.".".$object_field.")");
					}
				}
			}

		} else {
			//table permit
			$this->where("'table' LIKE permits.priv_type && '".$type."' LIKE permits.related_table && '".$action."' LIKE permits.action");
		}

		//determine what relationships the user must bear - defined by user_access fields
		foreach ($user_columns as $cname => $column) {
			if (isset($column['user_access'])) {
				$permit_field = "user_".$cname;
				if (!$this->db->hasUser()) {
					$this->where("permits.".$permit_field." is null");
				}else if ($this->models->has($column['type'])) {
					//multiple reference
					$user_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
					$ref = $cname."_id";
					$this->where("(permits.".$permit_field." is null || permits.".$permit_field." IN (SELECT ".$ref." FROM ".P($user_table)." u WHERE u.users_id=".$this->db->getUser()."))");
				} else {
					//single reference
					$user_field = $cname;
					$this->where("(permits.".$permit_field." is null || permits.".$permit_field." IN (SELECT ".$user_field." FROM ".P("users")." u WHERE u.id=".$this->db->getUser()."))");
				}
			}
		}

		//generate a condition for each role a permit can have. One of these must be satisfied
		$this->open("roles");
		//everyone - no restriction
		$this->where("permits.role='everyone'");
		//user - a specific user
		$this->orWhere("permits.role='user' && permits.who='".$this->db->getUser()."'");

		if ($join) {
			//self - permit for user actions
			if ($type == "users") $this->orWhere("permits.role='self' && ".$collection.".id='".$this->db->getUser()."'");
			//owner - grant access to owner of object
			$this->orWhere("permits.role='owner' && ".$collection.".owner='".$this->db->getUser()."'");
			//[user_access field] - requires users and objects to share the same terms for the given relationship
			foreach ($user_columns as $cname => $column) {
				if (isset($column['user_access']) && isset($columns[$cname])) {
					if ($this->models->has($column['type'])) {
						//multiple reference
						$user_table = empty($column['table']) ? $column['entity']."_".$cname : $column['table'];
						$object_table = empty($columns[$cname]['table']) ? $columns[$cname]['entity']."_".$cname : $columns[$cname]['table'];
						$ref = $cname."_id";
						$target = ($type == $columns[$cname]['entity']) ? "id" : $columns[$cname]['entity']."_id";
						if ($this->db->hasUser()) {
							$this->orWhere("permits.role='".$cname."' && (EXISTS (".
									"SELECT ".$ref." FROM ".P($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target." && o.".$ref." IN (".
										"SELECT ".$ref." FROM ".P($user_table)." u WHERE u.users_id=".$this->db->getUser().
									")".
								") || NOT EXISTS (SELECT ".$ref." FROM ".P($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target."))"
							);
						} else {
							$this->orWhere("permits.role='".$cname."' && NOT EXISTS (SELECT ".$ref." FROM ".P($object_table)." o WHERE o.".$columns[$cname]['entity']."_id=".$collection.".".$target.")");
						}
					} else {
						//single reference
						if ($this->db->hasUser()) {
							$this->orWhere("permits.role='".$cname."' && (".$collection.".".$cname." is null || ".$collection.".".$cname." IN (SELECT ".$cname." FROM ".P("users")." id=".$this->db->getUser()."))");
						} else {
							$this->orWhere("permits.role='".$cname."' && ".$collection.".".$cname." is null");
						}
					}
				}
			}
		}
		$this->close();
		return $this;
	}

	/**************************************************************
	 * query compiling functions
	 **************************************************************/

	/**
	 * builds the query for execution
	 * @param bool $force set true to force building, otherwise building will only occur if the query is dirty
	 * @return string SQL query
	 */
	function build($force = false) {
		if (!$this->dirty && !$force) return $this->sql;

		$sql = array();

		if ($this->mode == "insert" && !empty($this->parameters)) {
			$this->parameters = array();
		}

		if (!$this->raw && $this->mode == "insert" || $this->mode == "update") {
			if (!$this->validated) $this->validate(query::PHASE_VALIDATION);
			$this->validate(query::PHASE_STORE);
		}

		$query = $this->build_query();

		if ($this->mode === "query") {
			if (empty($query['SELECT'])) $this->error("Missing SELECT clause for query.", "global");
		} else if ($this->mode === "update") {
			if (empty($query['SET'])) $this->error("Missing SET clause for update query.", "global");
		}

		foreach ($query as $key => $clause) $sql[$key] = $key." ".$clause;

		$this->sql = implode(' ', $sql);

		unset($sql['LIMIT']);
		unset($sql['ORDER BY']);
		if (!empty($query['HAVING'])) {
			$this->count_sql = "SELECT COUNT(*) as count FROM (".implode(' ', $sql).") as c";
		} else if (!empty($query['GROUP BY'])) {
			$sql['SELECT'] = "SELECT COUNT(DISTINCT ".$query['GROUP BY'].") as count";
			unset($sql['GROUP BY']);
			$this->count_sql = implode(' ', $sql);
		} else {
			$sql['SELECT'] = "SELECT COUNT(".((false !== strpos(strtolower($query['SELECT']), 'distinct')) ? $query['SELECT'] : "*").") as count";
			$this->count_sql = implode(' ', $sql);
		}

		$this->dirty = false;

		return $this->sql;
	}

	function build_query() {
		$query = array(
			'SELECT' => '', //query
			'DELETE' => '', //delete
			'INSERT INTO' => '', //insert
			'UPDATE' => '', //update
			'TRUNCATE TABLE' => '', //truncate
			'FROM' => '', //query, delete
			'SET' => '', //insert, update
			'WHERE' => '', //query, delete, update
			'GROUP BY' => '', //query
			'HAVING' => '', //query
			'ORDER BY' => '', //query, delete (single table), update (single table)
			'LIMIT' => '' //query, delete (single table), update (single table)
		);

		//select, delete, or set
		if ($this->mode == "query") $query['SELECT'] = $this->build_select();
		else if ($this->mode == "delete") $query['DELETE'] = $this->build_select();
		else if ($this->mode == "insert" || $this->mode == "update") $query['SET'] = $this->build_set();

		//where
		if ($this->mode == "query" || $this->mode == "update" || $this->mode == "delete") $query['WHERE'] = $this->build_condition_set("default");

		//group
		if ($this->mode == "query") $query['GROUP BY'] = $this->build_group();

		//having
		if ($this->mode == "query") $query['HAVING'] = $this->build_condition_set("having");

		//order
		if ($this->mode == "query" || $this->mode == "update" || $this->mode == "delete") $query['ORDER BY'] = $this->build_sort();

		//limit
		if ($this->mode == "query" || $this->mode == "update" || $this->mode == "delete") $query['LIMIT'] = $this->build_limit();

		//from
		if ($this->mode == "query" || $this->mode == "delete") $query['FROM'] = $this->build_from();
		else if ($this->mode == "insert") $query['INSERT INTO'] = $this->build_from();
		else if ($this->mode == "update") $query['UPDATE'] = $this->build_from();
		else if ($this->mode == "truncate") $query['TRUNCATE TABLE'] = $this->build_from();

		foreach ($query as $key => $clause) if (empty($clause)) unset($query[$key]);

		return $query;
	}

	/**
	 * add search conditions to search one or more fields for some words
	 * @param string $keywords a natural language search string which can include operators 'and' and 'or' and quotes for exact matches
	 * @param string $fields a comma delimited list of columns to search on (you can escape a comma with a backslash)
	 * examples,
	 *
	 * search string: 'beef and broccoli'
	 * fields: 'name,description'
	 * conditions: ((name LIKE '%beef%' OR description LIKE '%beef%') and (name LIKE '%broccoli%' OR description LIKE '%broccoli%'))
	 */
	function build_select() {
		$select = array();
		if (empty($this->query['select'])) $select[] = "`".$this->base_collection."`.*";
		else {
			foreach ($this->query['select'] as $alias => $field) {
				$select[] = $field.(($alias == $field) ? "" : " as ".$alias);
			}
		}
		return implode(", ", $select);
	}

	function build_from() {
		$relations = array();
		$from = $last_collection = $last_alias = "";
		foreach ($this->query['from'] as $alias => $collection) {
			if (empty($from)) {
				$from = "`".P($collection)."`";
				if ($this->mode != "insert" && $this->mode != "truncate") $from .= " AS `".$alias."`";
			} else {
				if (empty($this->query['join'][$alias])) $this->query['join'][$alias] = "LEFT";
				$collection_segment = ("(" === substr($collection, 0, 1)) ? $collection : "`".P($collection)."`";
				$segment = " ".$this->query['join'][$alias]." JOIN ".$collection_segment." AS `".$alias."`";
				if (empty($this->query['on'][$alias])) {
					$relations = sb($collection)->relations;
					$relator = $last_alias;
					$rel = array();
					if (isset($relations[$last_collection])) {
						if (isset($relations[$last_collection][$last_collection])) $rel = reset(end($relations[$last_collection][$last_collection]));
						else if (isset($relations[$last_collection][$collection])) $rel = reset(end($relations[$last_collection][$collection]));
						else if (isset($relations[$last_collection][$this->model])) $rel = reset(end($relations[$last_collection][$this->model]));
						else if (isset($relations[$last_collection])) $rel = reset(end(reset($relations[$last_collection])));
					} else {
						$relator = $this->base_collection;
						if (isset($relations[$this->model][$last_collection])) $rel = reset(end($relations[$this->model][$last_collection]));
						else if (isset($relations[$this->model][$collection])) $rel = reset(end($relations[$this->model][$collection]));
						else if (isset($relations[$this->model][$this->model])) $rel = reset(end($relations[$this->model][$this->model]));
						else if (isset($relations[$this->model])) $rel = reset(end(reset($relations[$this->model])));
					}
					if (!empty($rel)) {
						if ($rel['type'] == "one") $this->query['on'][$alias] = (($rel['lookup'] == $collection) ? $alias : $rel['lookup']).".$rel[ref]=".(($rel['lookup'] == $collection) ? $relator : $alias).".".$rel['hook'];
						else if ($rel['type'] == "many") {
							if ($rel['lookup']) {
								if (!isset($this->query['from'][$rel['lookup']])) {
									$this->query['from'][$rel['lookup']] = $rel['lookup'];
									$this->query['join'][$rel['lookup']] = $this->query['join'][$alias];
									$this->query['on'][$rel['lookup']] = $relator.".id=$rel[lookup].$rel[ref]";
									$segment = " ".$this->query['join'][$rel['lookup']]." JOIN ".P($rel['lookup'])." AS $rel[lookup] ON ".$this->query['on'][$rel['lookup']].$segment;
								}
								$this->query['on'][$alias] = "$rel[lookup].$rel[hook]=$alias.id";
							} else {
								$this->query['on'][$alias] = $relator.".$rel[hook]=$alias.id";
							}
						}
					}
				}
				if (!empty($this->query['on'][$alias])) {
					$segment .= " ON ".$this->query['on'][$alias];
					if (isset($this->query['where']["on_".$alias])) $segment .= " && ".$this->build_condition_set("on_".$alias);
				}
				$from .= $segment;
			}
			$last_collection = $collection;
			$last_alias = $alias;
		}
		return $from;
	}

	function build_set() {
		$set = array();
		foreach ($this->fields as $k => $v) {
			if (!isset($this->exclusions[$k]) || true != $this->exclusions[$k]) {
				if ($v == "NULL") $v = null;
				$idx = $this->increment_parameter_index("set");
				$set[] = "`".str_replace(".", "`.`", str_replace('`', '', $k))."` = :set".$idx;
				$this->param("set".$idx, $v);
			}
		}
		return implode(", ", $set);
	}

	function build_condition_set($set) {
		$conditions = "";
		$this->parameter_count[$set] = 0;
		if (empty($this->query['where'][$set])) return $conditions;
		foreach ($this->query['where'][$set] as $idx => $condition) {
			if ($idx > 0) $conditions .= " ".$condition['con']." ";
			if (empty($condition['field'])) $conditions .= "(".$this->build_condition_set($condition['set']).")";
			else {
				if ($condition['ornull'] && $condition['op'] === "!=") $conditions .= "(".$condition['field']." is NULL || ";
				if (!$condition['invert']) $conditions .= $condition['field'];
				if (!is_null($condition['value'])) {
					if (is_array($condition['value'])) {
						$condition['op'] = str_replace(array('!', '='), array("NOT ", "IN"), $condition['op']);
						if ($condition['invert']) {
							$conditions .= "(";
							foreach ($condition['value'] as $vdx => $condition_value) {
								$index = $this->increment_parameter_index($set);
								if ($vdx > 0) $conditions .= " || ";
								$conditions .= ":".$set.$index." ".$condition['op']." ".$condition['field'];
								$this->param($set.$index, $condition_value);
							}
							$conditions .= ")";
						} else {
							$conditions .= ' '.$condition['op'].' (';
							foreach ($condition['value'] as $vdx => $condition_value) {
								$index = $this->increment_parameter_index($set);
								if ($vdx > 0) $conditions .= ", ";
								$conditions .= ":".$set.$index;
								$this->param($set.$index, $condition_value);
							}
							$conditions .= ')';
						}
					} else if ($condition['value'] === "NULL") {
						$condition['op'] = str_replace(array('!=', '='), array("IS NOT ", "IS"), $condition['op']);
						$conditions .= ' '.$condition['op'].' NULL';
					} else {
						$index = $this->increment_parameter_index($set);
						if ($condition['invert']) {
							$condition['op'] = str_replace(array('!', '='), array("NOT ", "IN"), $condition['op']);
							$conditions .= ":".$set.$index." ".$condition['op']." ".$condition['field'];
						} else $conditions .= ' '.$condition['op'].' :'.$set.$index;
						$this->param($set.$index, $condition['value']);
					}
				}
				if ($condition['ornull'] && $condition['op'] === "!=") $conditions .= ")";
			}
		}
		return $conditions;
	}

	function build_group() {
		return implode(', ', array_keys($this->query['group']));
	}

	function build_sort() {
		$sort = array();
		foreach ($this->query['sort'] as $column => $direction) {
			if ($direction === -1) $column .= " DESC";
			else if ($direction === 1) $column .= " ASC";
			$sort[] = $column;
		}
		return implode(', ', $sort);
	}

	function build_limit() {
		$limit = array();
		if (!empty($this->query['skip'])) $limit[] = $this->query['skip'];
		if (!empty($this->query['limit'])) $limit[] = $this->query['limit'];
		return implode(', ', $limit);
	}

	function raw($raw = true) {
		$this->raw = $raw;
		return $this;
	}

	/**************************************************************
	 * parsing functions
	 **************************************************************/
	function parse_collections($collections) {
		$collections = preg_split('/([,\<\>]+)/', $collections, -1, PREG_SPLIT_DELIM_CAPTURE);
		$results = array($this->parse_collection($collections[0]));
		$count = count($collections);
		for ($i = 2; $i < $count; $i += 2) {
			$collection = $collections[$i];
			$type = str_replace(array(",", "<>", "<", ">"), array("INNER", "OUTER", "LEFT", "RIGHT"), trim($collections[$i-1]));
			$results[] = array_merge($this->parse_collection($collection), array("join" => $type));
		}
		return $results;
	}

	function parse_collection($name) {
		$parts = explode(' ', $name);
		$on = $join = "";
		$alias = $name;
		$count = count($parts);
		if ($count > 2 && strtolower($parts[$count-2]) == "on") {
			$on = array_pop($parts);
			array_pop($parts);
			$count -= 2;
			$alias = implode(' ', $parts);
		}
		if ($count > 2 && strtolower($parts[$count-2]) == "as") {
			$alias = array_pop($parts);
			array_pop($parts);
			$count -= 2;
		}
		$name = implode(' ', $parts);
		return array("collection" => $name, "alias" => $alias, "on" => $on);
	}

	function parse_condition($name) {
		if (0 === strpos($name, "#")) return array("set" => substr($name, 1));
		else return array("field" => $name);
	}

	function parse_fields($fields, $mode = "select") {
		preg_match_all('/[a-zA-Z_\.\*]+/', $fields, $matches, PREG_OFFSET_CAPTURE);
		$offset = 0;
		foreach ($matches[0] as $match) {
			if (false !== strpos($match[0], ".")) {
				$prefix = substr($fields, 0, $match[1]+$offset);
				if (((substr_count($prefix, "'") - substr_count($prefix, "\\'")) % 2 == 0) && ((substr_count($prefix, '"') - substr_count($prefix, '\\"')) % 2 == 0)) {
					$replacement = $this->parse_field($match[0], $mode);
					$source_length = strlen($match[0]);
					$replacement_length = strlen($replacement);
					$fields = substr_replace($fields, $replacement, $match[1]+$offset, $source_length);
					$offset += $replacement_length - $source_length;
				}
			}
		}
		return $fields;
	}

	function parse_field($field, $mode = "select") {
		//split the field into parts
		$parts = explode(".", $field);
		$count = count($parts);
		//invert indicates that the operands should be flipped. eg 'some value' NOT IN (..sub query..)
		//in the example above the value is on the left
		$invert = false;
		//a condition may be added to a set other than the default where clause
		$set = false;
		$ornull = false;
		//we only proceed if there is more than one token, meaning this field name has a '.'
		if ($count > 1) {
			//the first token is either a collection name or the name of a column that references another collection
			//if it's a column, then we'll assume it's a column of our base collection
			$collection = $this->base_collection;
			//if it's a collection, we'll use it
			if (!empty($this->query['from'][$parts[0]]) || $this->models->has($parts[0])) {
				$token = array_shift($parts); //remove from tokens
				if (empty($this->query['from'][$token])) $this->join($token); //join if needed
				$collection = $token;	//set the collection
			}
			//now we start a loop to process the remaining tokens
			while (!empty($parts)) {
				//shift off the first token
				$token = array_shift($parts);
				//if there are no more tokens, then this is the final column name
				if (empty($parts)) {
					//get the field string and table name
					$field = "`".$collection."`.".$token;
					$table = $this->query['from'][$collection];
					//in a select query, the token may be '*'
					if ($token == "*") {
						//WHICH WE LEAVE AS IS FOR NOW
						//$schema = oldschemafunction($table.".fields");
						//foreach ($schema as $n => $f) {
							//if ($f['type'] == "terms" || $f['type'] == "category") $this->select($collection.".".$n);
						//}
					} else {
						//otherwise we look at the field schema
						$schema = column_info($table, $token);
						if (!empty($schema)) {
							if ($schema['entity'] !== $table) {
								$entity_collection = $collection."_".$schema['entity'];
								if (!isset($this->query['from'][$entity_collection])) {
									$base_entity = entity_base($table);
									$this->join($schema['entity']." as ".$entity_collection);
									if ($schema['entity'] === $base_entity) $this->on($entity_collection.".id=".$collection.".".$base_entity."_id");
									else $this->on($entity_collection.".".$base_entity."_id=".$collection.".".$base_entity."_id");
								}
								$field = "`".$entity_collection."`.".$token;
								$collection = $entity_collection;
							}
							if ($schema['type'] == "terms") {
								//if it's a category reference field or a terms field in group mode
								//we send it back around with the same token and 'slug' as the column name
								$parts = array($token, "slug");
							}
						}
					}
				} else {
					//otherwise we need to join this reference, and pass along the next token
					$result = $this->with($token, $collection, $mode, $parts[0]);
					if ($result) {
						//$this->with may return a sub query instead of performing a join. If it does, that means we're done
						//and we can end the loop. In the case of a condition, it also means that the operands should be inverted
						$field = $result;
						$parts = array();
						$invert = true;
					}
					//if the loop continues, the current token becomes the next collection
					$column_info = column_info($this->query['from'][$collection], $token);
					if (!empty($column_info) && $column_info['entity'] !== $this->query['from'][$collection]) {
						$table = $column_info['entity'];
						$collection = $collection."_".$column_info['entity'];
					}
					$collection = isset($this->query['from'][$collection.'_'.$token]) ? $collection.'_'.$token : $token;
					//if ($column_info['type'] == "category") $ornull = true;//$set = "on_".$collection;
				}
			}
		}
		if ($mode == "condition") {
			$field = array("field" => $field, "invert" => $invert);
			if (false !== $set) $field["set"] = $set;
			if (false !== $ornull) $field["ornull"] = true;
		}
		return $field;
	}

	/**
	 * build a search clause to be put into a WHERE clause
	 * @param string $text a natural language search string which can include operators 'and' and 'or' and quotes for exact matches
	 * @param array $fields a list of columns to search on
	 * @return string SQL WHERE component
	 * examples,
	 *
	 * search string: 'beef and broccoli'
	 * fields: array('name', 'description')
	 * return: ((name LIKE '%beef%' OR description LIKE '%beef%') and (name LIKE '%broccoli%' OR description LIKE '%broccoli%'))
	 */
	function search_clause($text, $fields) {
		$text = strtolower(trim(str_replace("\\\"", "&quot;", $text)));
		//tokenize the text
		$output = array();
		$output2 = array();
		$arr = explode("&quot;", $text);
		for ($i = 0; $i < count($arr); $i++) {
			if ($i % 2 == 0) $output = array_merge($output, explode(" ", $arr[$i]));
			else $output[] = $arr[$i];
		}
		foreach ($output as $token) if (trim($token) != "") $words[] = $token;
		//generate condition string
		$conditions = "(";
		for ($word = 0; $word < count($words); $word++) {
			$w = $words[$word];
			if ($w!="") {
				if ($w!="and" && $w!="or") {
					$conditions .= "(";
					for ($field = 0; $field < count($fields); $field++) {
						$conditions .= $fields[$field]." LIKE '%".$w."%'";
						if ($field<(count($fields)-1)) {
							$conditions .= " OR ";
						} else {
							$conditions .= ")";
						}
					}
					if ($word < (count($words)-1)) {
						if ($words[$word+1] == "and" || $words[$word+1] == "or") {
							$conditions .= " ".$words[$word+1]." ";
						} else {
							$conditions .= " AND ";
						}
					}
				}
			}
		}
		$conditions .= ")";
		return $conditions;
	}

	/**
	 * Replaces any parameter placeholders in a query with the value of that
	 * parameter. Useful for debugging. Assumes anonymous parameters from
	 * $params are are in the same order as specified in $query
	 *
	 * @param string $query The sql query with parameter placeholders
	 * @param array $params The array of substitution parameters
	 * @return string The interpolated query
	 */
	public function interpolate($query = null, $params = null) {
		if (is_null($query)) $query = $this->build();
		if (is_null($params)) $params = $this->parameters;
		$keys = array();
		$values = $params;

			# build a regular expression for each parameter
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}

			if (is_array($value)) $values[$key] = implode(',', $value);

			if (is_null($value)) $values[$key] = 'NULL';
		}
		// Walk the array to see if we can add single-quotes to strings
		array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));

		$query = preg_replace($keys, $values, $query, 1, $count);

		return $query;
	}

	/**************************************************************
	 * data validation
	 **************************************************************/

	function exclude($key) {
		$this->exclusions[$key] = true;
		return $this;
	}

	function validate($phase = query::PHASE_VALIDATION) {
		if ($phase == query::PHASE_VALIDATION && !$this->validated) $this->unvalidated = $this->fields;
		$model = $this->models->get($this->model);
		foreach ($model->hooks as $column => $hooks) {
			if (!isset($hooks['required']) && !isset($hooks['default']) && !isset($hooks['null']) && !isset($hooks['optional'])) $hooks['required'] = "";
			foreach ($hooks as $hook => $argument) {
				$this->invoke_hook($phase, $column, $hook, $argument);
			}
		}
		if ($phase == query::PHASE_VALIDATION) $this->validated = true;
	}

	function invoke_hook($phase, $column, $hook, $argument) {
		$key = false;
		if (isset($this->fields[$column])) $key = $column;
		else if (isset($this->fields[$this->model.".".$column])) $key = $this->model.".".$column;
		else if (isset($this->fields[$this->base_collection.".".$column])) $key = $this->base_collection.".".$column;
		if (!isset($this->hooks[$column."_".$hook])) $this->hooks[$column."_".$hook] = $this->hook_builder->get("store/".$hook);
		foreach ($this->hooks[$column."_".$hook] as $hook) {
			//hooks are invoked in 3 phases
			//0 = validate (before)
			//1 = store (during)
			//2 = after
			if ($phase == query::PHASE_VALIDATION) {
				if ($key == false) {
					if ($this->mode == "insert") $hook->empty_before_insert($this, $column, $argument);
					else if ($this->mode == "update") $hook->empty_before_update($this, $column, $argument);
					$hook->empty_validate($this, $column, $argument);
				} else {
					if ($this->mode == "insert") $this->fields[$key] = $hook->before_insert($this, $key, $this->fields[$key], $column, $argument);
					else if ($this->mode == "update") $this->fields[$key] = $hook->before_update($this, $key, $this->fields[$key], $column, $argument);
					$this->fields[$key] = $hook->validate($this, $key, $this->fields[$key], $column, $argument);
				}
			} else if ($phase == query::PHASE_STORE && $key != false) {
				if ($this->mode == "insert") $this->fields[$key] = $hook->insert($this, $key, $this->fields[$key], $column, $argument);
				else if ($this->mode == "update") $this->fields[$key] = $hook->update($this, $key, $this->fields[$key], $column, $argument);
				$this->fields[$key] = $hook->store($this, $key, $this->fields[$key], $column, $argument);
			} else if ($phase == query::PHASE_AFTER_STORE && $key != false) {
				if ($this->mode == "insert") $hook->after_insert($this, $key, $this->fields[$key], $column, $argument);
				else if ($this->mode == "update") $hook->after_update($this, $key, $this->fields[$key], $column, $argument);
				$hook->after_store($this, $key, $this->fields[$key], $column, $argument);
			} else if ($phase == query::PHASE_BEFORE_DELETE) {
				$hook->before_delete($this, $column, $argument);
			} else if ($phase == query::PHASE_AFTER_DELETE) {
				$hook->after_delete($this, $column, $argument);
			}
		}
	}

	public function errors($key = "", $values = false) {
		return $this->db->errors($key, $values);
	}

	public function error($error, $field = "global", $model="") {
		if (empty($model)) $model = $this->model;
		$this->db->error($error, $field, $model);
	}

	public function success($action) {
		$args = func_get_args();
		if (count($args) == 1) $args = array($this->model, $args[0]);
		return $this->db->success($args[0], $args[1]);
	}

	public function failure($action) {
		$args = func_get_args();
		if (count($args) == 1) $args = array($this->model, $args[0]);
		return $this->db->failure($args[0], $args[1]);
	}

	/**************************************************************
	 * query execution
	 **************************************************************/

	/**
	 * execute the query and get back the rows
	 * @param array $params the query parameters
	 */
	function execute($params = array(), $debug = false) {
		$this->build();
		if ($this->errors() && $this->mode != "query" && false === $this->store_on_errors) return false;
		if (empty($params)) $params = $this->parameters;
		if ($debug) {
			echo $this->interpolate();
			exit();
		}
		if ($this->mode == "delete") $this->validate(query::PHASE_BEFORE_DELETE);
		$records = $this->db->prepare($this->sql);
		$records->execute($params);
		$this->executed = true;
		if ($this->mode == "query") {
			$rows = $records->fetchAll(PDO::FETCH_ASSOC);
			$this->result = $rows;
			return ((!empty($this->query['limit'])) && ($this->query['limit'] == 1)) ? $rows[0] : $rows;
		} else {
			$this->record_count = $records->rowCount();
			if ($this->mode == "insert") {
				$this->insert_id = $this->db->lastInsertId();
				sb($this->model)->insert_id = $this->insert_id;
			}
			if (!$this->raw) {
				if ($this->mode == "delete") $this->validate(query::PHASE_AFTER_DELETE);
				else $this->validate(query::PHASE_AFTER_STORE);
			}
			return $this->record_count;
		}
	}

	function one() {
		return $this->limit(1)->execute();
	}

	function all() {
		$records = $this->execute();
		return ((!empty($this->query['limit'])) && ($this->query['limit'] == 1)) ? array($records) : $records;
	}

	function delete($run = true) {
		if ($this->mode != "delete") $this->dirty();
		$this->mode = "delete";
		if ($run) return $this->execute();
	  else return $this;
	}

	function insert($run = true) {
		if ($this->mode != "insert") $this->dirty();
		$this->mode = "insert";
		if ($run) return $this->execute();
		else return $this;
	}

	function update($run = true) {
		if ($this->mode != "update") $this->dirty();
		$this->mode = "update";
		if ($run) return $this->execute();
		else return $this;
	}

	function truncate($run = true) {
		if ($this->mode != "truncate") $this->dirty();
		$this->mode = "truncate";
		if ($run) return $this->execute();
		else return $this;
	}

	function unsafe_truncate() {
		if ($this->mode != "truncate") $this->dirty();
		$this->mode = "truncate";
		$this->db->exec("SET FOREIGN_KEY_CHECKS=0");
		$payload = $this->execute();
		$this->db->exec("SET FOREIGN_KEY_CHECKS=1");
		return $payload;
	}

	function count($params = array()) {
		$this->build();
		if ($this->errors()) return false;
		if (empty($params)) $params = $this->parameters;
		$records = $this->db->prepare($this->count_sql);
		$records->execute($params);
		$count = $records->fetchColumn();
		return $count;
	}

	/**************************************************************
	 * misc. functions
	 **************************************************************/

	/**
	 * set the query mode.
	 * @param string $mode one of: query, delete, insert, update
	 */
	function mode($mode) {
		$this->mode = $mode;
		return $this;
	}

	/**
	 * check if a collection or model is already in the query
	 * @param string $collection, the alias or table name of the collection, depending on the value of $alias
	 * @param bool $alias, pass false if specifying the table name instead of the alias
	 */
	function has($collection, $alias = true) {
		if ($alias) return isset($this->query['from'][$collection]);
		else return in_array($collection, $this->query['from'], true);
	}

	/**
	 * page the results
	 * @param int $page, the page number you want the results from
	 * @param bool $force, pass true to force re-querying the count
	 * @return pager
	 */
	function pager($page, $force = false) {
		if ($force || is_null($this->pager)) {
			$this->pager = new pager($this->count(), $this->query['limit'], $page);
			$this->skip($this->pager->start);
		}
		return $this->pager;
	}

	/**
	 * internal function for incrementing a count to generate a unique placholder string for parameters
	 * @param string $set, the set you're adding a parameter to
	 * @return int the next number
	 */
	function increment_parameter_index($set = "default") {
		if (empty($this->parameter_count[$set])) $this->parameter_count[$set] = 0;
		return $this->parameter_count[$set]++;
	}

	function getId() {
		if ($this->mode == "insert") return $this->insert_id;
		else if ($this->mode == "update") {
			if (isset($this->fields["id"])) return $this->fields["id"];
			else {
				$record = query($this->model)->conditions($this)->one();
				return $record['id'];
			}
		} else if ($this->mode == "delete") {
			return $this->fields["id"];
		}
	}

	function dirty() {
		$this->dirty = true;
		$this->validated = false;
		$this->executed = false;
	}

	/**************************************************************
	 * interface functions
	 **************************************************************/

	/**
	 * Implements method from IteratorAggregate
	 * @return PDOStatement
	 */
	public function getIterator() {
		return new ArrayIterator($this->execute());
	}

	public function offsetExists($offset) {
		if (!$this->executed) $this->execute();
		if (!$this->result) return false;
		return isset($this->result[$offset]);
	}

	public function offsetGet($offset) {
		if (!$this->executed) $this->execute();
		if (!$this->result) return false;
		return isset($this->result[$offset]) ? $this->result[$offset] : null;
	}

	public function offsetSet($offset, $value) {
		if (!$this->executed) $this->execute();
		if (!$this->result) return false;
		if (is_null($offset)) $this->result[] = $value;
		else $this->result[$offset] = $value;
	}

	public function offsetUnset($offset) {
		if (!$this->executed) $this->execute();
		if (!$this->result) return false;
		unset($this->result[$offset]);
	}
}
