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

class query implements IteratorAggregate, ArrayAccess {
	
	const PHASE_VALIDATION = 0;
	const PHASE_STORE = 1;
	const PHASE_AFTER_STORE = 2;
	const PHASE_AFTER_DELETE = 3;
	
	//query array, holds the parts of the query
	var $query = array(
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
	
	var $db; //pdo instance
	var $model; //model name
	var $base_collection; //base collection
	var $last_collection; //last added collection
	var $mode = "query"; // mode (query, delete, insert, update)
	
	var $clauses = array();
	var $statements = array();
	var $parameters = array();
	var $fields = array();
	var $exclusions = array();
	var $result = false;
	var $pager = null;
	
	var $parameter_count = array();
	
	var $sets = array();
	var $set = "default";
	
	var $sql = null;
	var $count_sql = null;
	var $dirty = true;
	var $validated = false;
	var $executed = false;
	var $op = "default";
	var $tags = array();
	var $operations = array();
	var $hooks = array();
	
	var $raw = false;
	
	/**
	 * create a new query
	 * @param string $collection the name of the primary table/collection to query
	 * @param array $params parameters to merge into the query
	 */
	function __construct($collection, $params=array()) {
		$this->db = sb()->db;
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
	function operation($name, $args=array()) {
		$args["operation"] = $name;
		$args['tag'] = $this->op;
		$this->operations[$this->op][] = $args;
	}
	
	/**************************************************************
	 * Query building functions
	 **************************************************************/

	//SELECT, DELETE

	/**
	 * specify fields for selection
	 * @param string $field the name of the field
	 */
	function select($field, $prefix="") {
		if (is_array($field)) {
			foreach ($field as $f) $this->select($f, $prefix);
 		} else {
 			if (!empty($prefix)) $field = $prefix.".".$field;
 			$field = $this->parse_collection($field);
 			$this->query['select'][$field['alias']] = $this->parse_fields($field['collection']);
 			$this->operation("select", $field);
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
	function join($collection, $type="") {
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
	function on($expr, $collection="") {
		efault($collection, $this->last_collection);
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
	function with($field, $collection="", $mode="select", $token=null) {
		$return = false;
		$parsed = $this->parse_collection($field);
		list($field, $alias) = array($parsed['collection'], $parsed['alias']);
		efault($collection, $this->last_collection);
		$table = $this->query['from'][$collection];
		$schema = schema($table.".fields.".$field);
		if ($schema['type'] == "category" || ($schema['type'] == "terms" && ($mode == "group" || $mode == "set"))) {
			$this->join("terms_index as ".$collection."_".$alias."_lookup")
						->on($collection."_".$alias."_lookup.type='".$table."' && ".$collection."_".$alias."_lookup.type_id=".$collection.".id && ".$collection."_".$alias."_lookup.rel='".$field."'")
						->join("terms as ".$collection."_".$alias)
						->on($collection."_".$alias.".id=".$collection."_".$alias."_lookup.terms_id");
		} else if ($schema['type'] == "terms") {
			if (is_null($token)) $token = "slug";
			if ($mode == "select") {
				$return = "(SELECT GROUP_CONCAT(t.$token) FROM ".P("terms_index")." ti INNER JOIN ".P("terms")." t ON t.id=ti.terms_id WHERE ti.type='".$table."' && ti.type_id=".$collection.".id && ti.rel='".$field."' GROUP BY ti.type, ti.type_id, ti.rel)";
			} else if ($mode == "where" || $mode == "condition") {
				$return = "(SELECT t.$token FROM ".P("terms_index")." ti INNER JOIN ".P("terms")." t ON t.id=ti.terms_id WHERE ti.type='".$table."' && ti.type_id=".$collection.".id && ti.rel='".$field."' GROUP BY ti.type, ti.type_id, ti.rel)";
			}
		} else if (isset($schema['references'])) {
			$ref = explode(" ", $schema['references']);
			$type = "";
			if (isset($schema['null'])) $type = "left";
			$this->join($ref[0]." as ".$collection."_".$alias)->on($collection."_".$alias.".".$ref[1]."=".$collection.".".$field);
		} else if ($this->db->has($schema['type'])) {
			$type_schema = schema($schema['type']);
			if (is_null($token)) $token = empty($type_schema['label_select']) ? $collection."_".$alias.".id" : str_replace($schema['type'], $collection."_".$alias, $type_schema['label_select']);
			if ($mode == "select") {
				$return = "(SELECT GROUP_CONCAT($token) FROM ".P($table."_".$field)." ".$collection."_".$alias."_lookup INNER JOIN ".P($schema['type'])." ".$collection."_".$alias." ON ".$collection."_".$alias.".id=".$collection."_".$alias."_lookup.".$schema['type']."_id WHERE ".$collection."_".$alias."_lookup.".$table."_id=".$collection.".id)";
			} else if ($mode == "where" || $mode == "condition") {
				$return = "(SELECT $token FROM ".P($table."_".$field)." ".$collection."_".$alias."_lookup INNER JOIN ".P($schema['type'])." ".$collection."_".$alias." ON ".$collection."_".$alias.".id=".$collection."_".$alias."_lookup.".$schema['type']."_id WHERE ".$collection."_".$alias."_lookup.".$table."_id=".$collection.".id)";
			} else if ($mode == "group" || $mode == "set") {
				$this->join($table."_".$field." as ".$collection."_".$alias."_lookup")
							->on($collection."_".$alias."_lookup.".$table."_id=".$collection.".id")
							->join($schema['type']." as ".$collection."_".$alias)
							->on($collection."_".$alias.".id=".$collection."_".$alias."_lookup.".$schema['type']."_id");
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
	function condition($field, $value="", $op="=", $ops=array()) {
		if (is_array($field)) {
			foreach ($field as $k => $v) $this->condition($k, $v, $op, $ops);
			return $this;
		}
		$this->operation("condition", array("field" => $field, "value" => $value, "op" => $op, "ops" => $ops));
		$set = $this->set;
		$condition = array_merge(array("con" => "&&", "set" => $this->set, "value" => $value, "op" => $op), $this->parse_condition($field), star($ops));
		if (isset($condition['set']) && isset($condition['field'])) $set = $condition['set'];
		if (in_array($condition['op'], array("=", "!=", "IN", "NOT IN"))) $condition = array_merge($condition, $this->parse_field($condition['field'], "condition"));
		else $condition['field'] = $this->parse_field($condition['field'], "group");
		$this->query['where'][$set][] = $condition;
		$this->dirty();
		return $this;
	}
	function conditions($fields, $op="=", $ops=array()) {
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
	function andCondition($field, $value, $op="=", $ops=array()) {
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
	function orCondition($field, $value, $op="=", $ops=array()) {
		return $this->condition($field, $value, $op, array_merge(array("con" => "||"), star($ops)));
	}
	
	
	
	/**
	 * add a parameter
	 * @param string $name the parameter name
	 * @param mixed $value the parameter value
	 */	
	function param($name, $value=null) {
		if (!is_array($name)) $name = array($name => $value);
		foreach ($name as $k => $v) $this->parameters[":".$k] = $v;
		return $this;
	}
	function params($name, $value=null) {
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
	function where($clause, $options=array()) {
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
	function andWhere($clause, $options=array()) {
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
	function orWhere($clause, $options=array()) {
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
	function havingCondition($field, $value, $op="=", $ops=array()) {
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
	function andHavingCondition($field, $value, $op="=", $ops=array()) {
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
	function orHavingCondition($field, $value, $op="=", $ops=array()) {
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
	function having($clause, $options=array()) {
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
	function andHaving($clause, $options=array()) {
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
	function orHaving($clause, $options=array()) {
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
	
	function open($set, $con="&&", $nest=false) {
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
	function sort($column, $direction=0) {
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
	function search($keywords="", $fields="") {
		//if there are no search terms, there's nothing to do
		if (empty($keywords)) return $this;
		
		//if no fields are passed, search the default fields of all tables in the query
		if (empty($fields)) {
			$fieldsets = array();
			foreach ($this->query['from'] as $alias => $model) {
				$schema = schema($model);
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
	
	function action($action, $collection="") {
		efault($collection, $this->last_collection);
		if ($this->has($collection)) {
			$type = $this->query['from'][$collection];
			$join = true;
		} else {
			$type = $collection;
			$join = false;
		}
		
		if ($join) {
			//join permits - match table and action
			if (!$this->has("permits")) $this->innerJoin("permits")->on("'".P($type)."' LIKE permits.related_table && '".$action."' LIKE permits.action");
			//global or object permit
			$this->where("('global' LIKE permits.priv_type || (permits.priv_type='object' && permits.related_id=".$collection.".id))");
			//associate terms with permits without using the 'roles' relationship and it will require objects to bear those terms as well
			$this->where("NOT EXISTS (".
				"SELECT COUNT(*) as count ".
				"FROM ".P("terms_index")." as ti ".
				"WHERE ti.terms_id IN (SELECT terms_id FROM ".P("terms_index")." WHERE type='permits' && type_id=permits.id && rel!='roles') ".
				"&& ((ti.type='permits' && ti.type_id=permits.id) || (ti.type='".$type."' && ti.type_id=".$collection.".id)) ".
				"GROUP BY ti.terms_id ".
				"HAVING count=1".
			")");
		} else {
			//table permit
			$this->where("'table' LIKE permits.priv_type && '".P($type)."' LIKE permits.related_table && '".$action."' LIKE permits.action");
		}
		
		//generate a condition for each role a permit can have. One of these must be satisfied
		$this->open("roles");
		//everyone - no restriction
		$this->where("permits.role='everyone'");
		//user - a specific user
		$this->orWhere("permits.role='user' && permits.who='".sb()->user['id']."'");
		//taxonomy - associate terms with the permit to restrict access to users with the same associated terms
		//						for example, group is a taxonomy. so tagging a permit with the 'admin' term means that this permit applies to users tagged with the 'admin' term.
		$this->orWhere("permits.role='taxonomy' && NOT EXISTS (".
			"SELECT COUNT(*) as count ".
			"FROM ".P("terms_index")." as ti ".
			"WHERE ti.terms_id IN (SELECT terms_id FROM ".P("terms_index")." WHERE type='permits' && type_id=permits.id && rel='roles') ".
			"&& ((ti.type='permits' && ti.type_id=permits.id) || (ti.type='users' && ti.type_id='".sb()->user['id']."')) ".
			"GROUP BY ti.terms_id ".
			"HAVING count=1".
		")");
		if ($join) {
			//self - permit for user actions
			if ($type == "users") $this->orWhere("permits.role='self' && ".$collection.".id='".sb()->user['id']."'");
			//owner - grant access to owner of object
			$this->orWhere("permits.role='owner' && ".$collection.".owner='".sb()->user['id']."'");
			//[relationship/taxonomy] - requires users and objects to share the same terms for the given relationship
			$this->orWhere("permits.role NOT IN ('everyone', 'self', 'owner', 'user', 'taxonomy') && (NOT EXISTS(".
				"SELECT COUNT(*) as count ".
				"FROM ".P("terms_index")." as ti ".
				"WHERE ti.terms_id IN (SELECT terms_id FROM ".P("terms_index")." WHERE type='".$type."' && type_id=".$collection.".id && rel=permits.role) ".
				"&& ((ti.type='users' && ti.type_id='".sb()->user['id']."') || (ti.type='".$type."' && ti.type_id=".$collection.".id)) ".
				"GROUP BY ti.terms_id ".
				"HAVING count=1) ".
			"|| EXISTS (".
				"SELECT COUNT(*) as count ".
				"FROM ".P("terms_index")." as ti ".
				"WHERE ti.terms_id IN (SELECT terms_id FROM ".P("terms_index")." WHERE type='".$type."' && type_id=".$collection.".id && rel=permits.role) ".
				"&& ((ti.type='users' && ti.type_id='".sb()->user['id']."') || (ti.type='".$type."' && ti.type_id=".$collection.".id)) ".
				"GROUP BY ti.terms_id ".
				"HAVING count=2)".
			")");
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
	function build($force=false) {
		if (!$this->dirty && !$force) return $this->sql;
		
		$sql = array();
		
		if ($this->mode == "insert" && !empty($this->parameters)) {
			$this->parameters = array();
		}
		
		if (!$this->raw && $this->mode == "insert" || $this->mode == "update") {
			if (!$this->validated) $this->validate(0);
			$this->validate(1);
		}
		
		$query = $this->build_query();
		
		if ($this->mode == "query") {
			if (empty($query['SELECT'])) error("Missing SELECT clause for query.", "global");
		} else if ($this->mode == "insert") {
			if (empty($query['SET'])) error("Missing SET clause for insert query.", "global");
		} else if ($this->mode == "update") {
			if (empty($query['SET'])) error("Missing SET clause for update query.", "global");
		} else if ($this->mode == "delete") {
			
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
		if (empty($this->query['select'])) $select[] = $this->base_collection.".*";
		else {
			foreach ($this->query['select'] as $alias => $field) {
				$select[] = $field.(($alias == $field) ? "" : " as ".$alias);
			}
		}
		return implode(", ", $select);
	}
	
	function build_from() {
		$relations = array(); $from = $last_collection = $last_alias = "";
		foreach ($this->query['from'] as $alias => $collection) {
			if (empty($from)) {
				$from = "`".P($collection)."`";
				if ($this->mode != "insert" && $this->mode != "truncate") $from .= " AS `".$alias."`";
			} else {
				efault($this->query['join'][$alias], "LEFT");
				$collection_segment = ("(" === substr($collection, 0, 1)) ? $collection : "`".P($collection)."`";
				$segment = " ".$this->query['join'][$alias]." JOIN ".$collection_segment." AS `".$alias."`";
				if (empty($this->query['on'][$alias])) {
					$relations = db::model($collection)->relations;
					$relator = $last_alias;
					$rel = array();
					if (isset($relations[$last_collection])) $rel = reset(end(reset($relations[$last_collection])));
					else {
						$relator = $this->base_collection;
						if (isset($relations[$this->model][$last_collection])) $rel = reset(end($relations[$this->model][$last_collection]));
						else if (isset($relations[$this->model][$collection])) $rel = reset(end($relations[$this->model][$collection]));
						else if (isset($relations[$this->model][$this->model])) $rel = reset(end($relations[$this->model][$this->model]));
						else if (isset($relations[$this->model])) $rel = reset(end(reset($relations[$this->model])));
					}
					if (!empty($rel)) {
						if ($rel['type'] == "one") $this->query['on'][$alias] = "$rel[lookup].$rel[ref]=".(($rel['lookup'] == $collection) ? $relator : $alias).".id";
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
				$segment .= " ON ".$this->query['on'][$alias];
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
				$set[] = "`".str_replace(".", "`.`", $k)."` = :set".$idx;
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
	
	function raw($raw=true) {
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
		for ($i=2;$i<$count;$i+=2) {
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
		$alias = end(explode(".", $alias));
		if ($alias == "*") $alias = $name;
		return array("collection" => $name, "alias" => $alias, "on" => $on);
	}

	function parse_condition($name) {
		if (0 === strpos($name, "#")) return array("set" => substr($name, 1));
		else return array("field" => $name);
	}
	
	function parse_fields($fields, $mode="select") {
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
	
	function parse_field($field, $mode="select") {
		$parts = explode(".", $field);
		$count = count($parts);
		$invert = false;
		if ($count > 1) {
			$collection = $this->base_collection;
			if (!empty($this->query['from'][$parts[0]]) || $this->db->has($parts[0])) {
				$token = array_shift($parts);
				if (empty($this->query['from'][$token])) $this->join($token); 
				$collection = $token;				
			}
			while (!empty($parts)) {
				$token = array_shift($parts);
				if (empty($parts)) {
					$field = $collection.".".$token;
					$table = $this->query['from'][$collection];
					if ($token == "*") {
						$schema = schema($table.".fields");
						foreach ($schema as $n => $f) {
							//if ($f['type'] == "terms" || $f['type'] == "category") $this->select($collection.".".$n);
						}
					} else {
						$schema = schema($table.".fields.".$token);
						if (!empty($schema)) {
							if ($mode == "set" && ($schema['type'] == "category" || $this->db->has($schema['type']))) {
								//do nothing
							} else if ($schema['type'] == "category" || ($schema['type'] == "terms" && $mode == "group")) {
								$parts = array($token, "slug");
							} else if ($schema['type'] == "terms") {
								$field = $this->with($token, $collection, $mode, "slug");
								$invert = true;
							}
						}
					}
				} else {
					$result = $this->with($token, $collection, $mode, $parts[0]);
					if ($result) {
						$field = $result;
						$parts = array();
						$invert = true;
					}
					$collection = isset($this->query['from'][$collection.'_'.$token]) ? $collection.'_'.$token : $token;
				}
			}
		}
		if ($mode == "condition") {
			$field = array("field" => $field, "invert" => $invert);
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
		$text = strtolower(trim(str_replace("\\\"","&quot;",$text)));
		//tokenize the text
		$output = array();
		$output2 = array();
		$arr = explode("&quot;",$text);
		for ($i=0;$i<count($arr);$i++){
			if ($i%2==0) $output=array_merge($output,explode(" ",$arr[$i]));
			else $output[] = $arr[$i];
		}
		foreach($output as $token) if (trim($token)!="") $words[]=$token;
		//generate condition string
		$conditions = "(";
		for($word=0;$word<count($words);$word++) {
			$w = $words[$word];
			if ($w!="") {
				if ($w!="and" && $w!="or") {
					$conditions .= "(";
					for($field=0;$field<count($fields);$field++) {
						$conditions .= $fields[$field]." LIKE '%".$w."%'";
						if ($field<(count($fields)-1)) {
							$conditions .= " OR ";
						} else {
							$conditions .= ")";
						}
					}
					if ($word<(count($words)-1)) {
						if ($words[$word+1]=="and" || $words[$word+1]=="or") {
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
	public function interpolate($query=null, $params=null) {
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

					if (is_array($value))
							$values[$key] = implode(',', $value);

					if (is_null($value))
							$values[$key] = 'NULL';
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
	}
	 
	function validate($phase=query::PHASE_VALIDATION) {
		$oldscope = error_scope();
		error_scope($this->model);
		foreach (db::model($this->model)->hooks as $column => $hooks) {
			if (!isset($hooks['required']) && !isset($hooks['default']) && !isset($hooks['null']) && !isset($hooks['optional'])) $hooks['required'] = "";
			foreach ($hooks as $hook => $argument) {
				$this->invoke_hook($phase, $column, $hook, $argument);
			}
		}
		error_scope($oldscope);
		if ($phase == query::PHASE_VALIDATION) $this->validated = true;
	}
	
	function invoke_hook($phase, $column, $hook, $argument) {
		$key = false;
		if (isset($this->fields[$column])) $key = $column;
		else if (isset($this->fields[$this->model.".".$column])) $key = $this->model.".".$column;
		else if (isset($this->fields[$this->base_collection.".".$column])) $key = $this->base_collection.".".$column;
		
		if (!isset($this->hooks[$column."_".$hook])) $this->hooks[$column."_".$hook] = build_hook("store/".$hook, "classes/QueryHook", "db");
		$hook = $this->hooks[$column."_".$hook];
		
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
		}
	}
	
	/**************************************************************
	 * query execution
	 **************************************************************/
	
	/**
	 * execute the query and get back the rows
	 * @param array $params the query parameters
	 */
	function execute($params=array(), $debug=false) {
		$this->build();
		if (errors() && $this->mode != "query") return false;
		if (empty($params)) $params = $this->parameters;
		if ($debug) {
			echo $this->interpolate();
			exit();
		}
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
				$this->db->model($this->model)->insert_id = $this->insert_id;
			}
			if (!$this->raw && $this->mode != "delete") $this->validate(2);
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
	
	 function delete($run=true) {
		 if ($this->mode != "delete") $this->dirty();
		 $this->mode = "delete";
		  if ($run) return $this->execute();
		  else return $this;
	 }
	 
	 function insert($run=true) {
		 if ($this->mode != "insert") $this->dirty();
		 $this->mode = "insert";
		 if ($run) return $this->execute();
		 else return $this;
	 }
	 
	 function update($run=true) {
		 if ($this->mode != "update") $this->dirty();
		 $this->mode = "update";
		 if ($run) return $this->execute();
		 else return $this;
	 }
	 
	 function truncate($run=true) {
		 if ($this->mode != "truncate") $this->dirty();
		 $this->mode = "truncate";
		 if ($run) return $this->execute();
		 else return $this;
	 }
	
	function count($params=array()) {
		$this->build();
		if (errors()) return false;
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
	function has($collection, $alias=true) {
		if ($alias) return isset($this->query['from'][$collection]);
		else return in_array($collection, $this->query['from'], true);
	}

	/**
	 * page the results
	 * @param int $page, the page number you want the results from
	 * @param bool $force, pass true to force re-querying the count
	 * @return pager
	 */		 	
	function pager($page, $force=false) {
		import("pager");
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
	function increment_parameter_index($set="default") {
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
