<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/db/Migration.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Migration
 */
/**
 * @defgroup Migration
 * the Migration
 * @ingroup db
 */
$sb->provide("core/db/Migration");
/**
 * The Migration class. Interacts with the Schemer to manipulate the schema
 * @ingroup Migration
 */
class Migration {
	/**
	 * @copydoc Schemer::table
	 */
	function table($arg) {
		global $schemer;
		$args = func_get_args();
		call_user_func_array(array($schemer, "table"), $args);
	}
	/**
	 * @copydoc Schemer::column
	 */
	function column($table, $col) {
		global $schemer;
		$args = func_get_args();
		call_user_func_array(array($schemer, "column"), $args);
	}
	/**
	 * @copydoc Schemer::drop
	 */
	function drop($table, $col="") {
		global $schemer;
		$schemer->drop($table, $col);
	}
	/**
	 * @copydoc Schemer::uri
	 */
	function uri($path, $args="") {
		global $schemer;
		$schemer->uri($path, $args);
	}
	/**
	 * @copydoc Schemer::permit
	 */
	function permit($on, $args) {
		global $schemer;
		$schemer->permit($on, $args);
	}
	/**
	 * @copydoc Schemer::store
	 */
	function store($table, $match, $others="") {
		global $schemer;
		$schemer->store($table, $match, $others);
	}
	/**
	 * @copydoc Schemer::before
	 */
	function before($name, $trig, $each=true) {
		global $schemer;
		$schemer->before($name, $trig, $each);
	}
	/**
	 * @copydoc Schemer::after
	 */
	function after($name, $trig, $each=true) {
		global $schemer;
		$schemer->after($name, $trig, $each);
	}
	/**
	 * Called when moving forward from one migration to the next
	 */
	function up() {}
	/**
	 * Called when moving backwards from one migration to the previous
	 */
	function down() {}
	/**
	 * Called after schema is updated if this migration was run up
	 */
	function created() {}
	/**
	 * Called after schema is updated if this migration was run down
	 */
	function removed() {}

	public function __call($method, $args) {
		global $schemer;
		if(method_exists($schemer, $method)) return call_user_func_array(array($schemer, $method), $args);
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}
}
?>
