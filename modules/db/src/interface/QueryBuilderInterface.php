<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file modules/db/src/interface/QueryBuilderFactoryInterface.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;

use \IteratorAggregate;
use \ArrayAccess;
/**
* query builder factory interface
*/
interface QueryBuilderInterface extends IteratorAggregate, ArrayAccess {

/**************************************************************
* operation tagging, logging, and undoing
**************************************************************/

	/**
	 * Open a tag to start tagging operations so they can be referenced or canceled by a tag name
	 */
	function openTag($tag);
	/**
	 * Close the operation tag (sets it back to 'default')
	 */
	function closeTag();
	/**
	 * Log an operation
	 */
	function operation($name, $args = array());
	/**
	 * undo an operation
	 */
	function undo($name, $args = array());

	/**************************************************************
	 * Query building functions
	 **************************************************************/

	/**
	 * specify fields for selection
	 * @param string $field the name of the field
	 */
	function select($field, $prefix = "");
	/**
	 * add a collection or table to be queried
	 * @param string $collection the name of the table or collection
	 */
	function from($collections);
	/**
	 * join a collection or table to be queried
	 * @param string $collection the name of the table or collection
	 * @param string $join the join type
	 */
	function join($collection, $type = "");
	/**
	 * join a collection or table to be queried using an INNER join
	 * @param string $collection the name of the table or collection
	 */
	function innerJoin($collection);
	/**
	 * join a collection or table to be queried using a LEFT join
	 * @param string $collection the name of the table or collection
	 */
	function leftJoin($collection);
	/**
	 * join a collection or table to be queried using a RIGHT join
	 * @param string $collection the name of the table or collection
	 */
	function rightJoin($collection);
	/**
	 * specify the on clause for a join
	 * @param string $expr the ON expression (not including 'ON ')
	 * @param string $collection the name of the table or collection
	 */
	function on($expr, $collection = "");
	/**
	 * include a referenced item
	 * @param string $expr the ON expression (not including 'ON ')
	 * @param string $collection the name of the table or collection
	 */
	function with($field, $collection = "", $mode = "select", $token = null);
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
	function condition($field, $value = "", $op = "=", $ops = array());
	function conditions($fields, $op = "=", $ops = array());
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
	function andCondition($field, $value, $op = "=", $ops = array());
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
	function orCondition($field, $value, $op = "=", $ops = array());
	/**
	 * add a parameter
	 * @param string $name the parameter name
	 * @param mixed $value the parameter value
	 */
	function param($name, $value = null);
	function params($name, $value = null);
	/**
	 * add a where condition using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function where($clause, $options = array());
	/**
	* add a WHERE condition using && as the logical connective
	* @param string $clause the where expression(s)
	* @param star $options (optional) pass any of the following options
	* 									- set: the condition set to add this to ('default', 'having', or a custom set)
	* 									- op: the operator (eg. '=', '<', '>')
	* 									- con: the logical connective (eg. '&&', '||')
	*/
	function andWhere($clause, $options = array());
	/**
	* add a WHERE condition using || as the logical connective
	* @param string $clause the where expression(s)
	* @param star $options (optional) pass any of the following options
	* 									- set: the condition set to add this to ('default', 'having', or a custom set)
	* 									- op: the operator (eg. '=', '<', '>')
	* 									- con: the logical connective (eg. '&&', '||')
	*/
	function orWhere($clause, $options = array());
	/**
	* add a field or fields to group by
	* @param string $column the column or group by statement
	*/
	function group($column);
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
	function havingCondition($field, $value, $op = "=", $ops = array());
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
	function andHavingCondition($field, $value, $op = "=", $ops = array());
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
	function orHavingCondition($field, $value, $op = "=", $ops = array());
	/**
	 * add a HAVING clause using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function having($clause, $options = array());
	/**
	 * add a HAVING clause using && as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function andHaving($clause, $options = array());
	/**
	 * add a HAVING clause using || as the logical connective
	 * @param string $clause the where expression(s)
	 * @param star $options (optional) pass any of the following options
	 * 									- set: the condition set to add this to ('default', 'having', or a custom set)
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function orHaving($clause, $options = array());

	function set($field, $value);

	function fields($fields);

	function open($set, $con = "&&", $nest = false);

	function close();

	/**
	 * add a field or fields to sort by
	 * @param string $column the column or ORDER BY statement
	 * @param int $direction (optional) sorting direction (-1 or 1)
	 */
	function sort($column, $direction = 0);
	/**
	 * add a limit
	 * @param int/string $limit the limit or limit statement
	 */
	function limit($limit);
	/**
	 * set the number of records to skip
	 * @param int $skip the number of records to skip
	 */
	function skip($skip);
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
	function search($keywords = "", $fields = "");

	/**************************************************************
	 * query compiling functions
	 **************************************************************/

	/**
	 * builds the query for execution
	 * @param bool $force set true to force building, otherwise building will only occur if the query is dirty
	 * @return string SQL query
	 */
	function build($force = false);

	function raw($raw = true);

	/**
	 * Replaces any parameter placeholders in a query with the value of that
	 * parameter. Useful for debugging. Assumes anonymous parameters from
	 * $params are are in the same order as specified in $query
	 *
	 * @param string $query The sql query with parameter placeholders
	 * @param array $params The array of substitution parameters
	 * @return string The interpolated query
	 */
	public function interpolate($query = null, $params = null);

	/**************************************************************
	 * data validation
	 **************************************************************/

	function exclude($key);

	/**************************************************************
	 * query execution
	 **************************************************************/

	/**
	 * execute the query and get back the rows
	 * @param array $params the query parameters
	 */
	function execute($params = array(), $debug = false);

	function one();

	function all();

	function delete($run = true);

	function insert($run = true);

	function update($run = true);

	function truncate($run = true);

	function unsafe_truncate();

	function count($params = array());

	/**************************************************************
	 * misc. functions
	 **************************************************************/

	/**
	 * set the query mode.
	 * @param string $mode one of: query, delete, insert, update
	 */
	function mode($mode);

	/**
	 * check if a collection or model is already in the query
	 * @param string $collection, the alias or table name of the collection, depending on the value of $alias
	 * @param bool $alias, pass false if specifying the table name instead of the alias
	 */
	function has($collection, $alias = true);

	/**
	 * page the results
	 * @param int $page, the page number you want the results from
	 * @param bool $force, pass true to force re-querying the count
	 * @return pager
	 */
	function pager($page, $force = false);

	function getId();

	function dirty();
}
?>
