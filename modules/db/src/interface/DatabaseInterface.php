<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file modules/db/src/interface/DatabaseInterface.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
/**
* query builder factory interface
*/
interface DatabaseInterface {
	/**
	 * get records or columns
	 * @ingroup data
	 * @param string $model the name of the model
	 * @param mixed $id/$conditions the id or an array of conditions
	 * @param string $column optional column name
	 */
	function get($collection, $conditions = array(), $options = array());
	/**
	 * query the database
	 * @param string $froms comma delimeted list of tables to join. 'users' or 'uris,system_tags'
	 * @param string $args starbug query string for params: select, where, limit, and action/priv_type
	 * @param bool $mine optional. if true, joining models will be checked for relationships and ON statements will be added
	 * @return array record or records
	 */
	function query($collection);
	/**
	 * store data in the database
	 * @param string $name the name of the table
	 * @param string/array $fields keypairs of columns/values to be stored
	 * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
	 * @return array validation errors
	 */
	function store($name, $fields = array(), $from = "auto");
	/**
	 * queue data to be stored in the database pending validation of other data
	 * @param string $name the name of the table
	 * @param string/array $fields keypairs of columns/values to be stored
	 * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
	 * @return array validation errors
	 */
	function queue($name, $fields = array(), $from = "auto", $unshift = false);
	/**
	 * proccess the queue of data for storage
	 */
	function store_queue();
	/**
	 * remove from the database
	 * @param string $from the name of the table
	 * @param string $where the WHERE conditions on the DELETE
	 */
	function remove($from, $where);
	public function set_debug($debug);
	public function exec($statement);
	public function errors($key = "", $values = false);
	public function error($error, $field = "global", $scope = "global");
	public function success($model, $action);
	public function failure($model, $action);
	public function getUser();
	public function setUser($user);
	public function hasUser();
}
?>
