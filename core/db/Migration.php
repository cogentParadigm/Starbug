<?php
/**
 * This file is part of StarbugPHP
 * @file core/db/Migration.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * The Migration class. Interacts with the Schemer to manipulate the schema
 * @ingroup db
 */
class Migration {
	//ADD TABLE DESCRIPTION
	function table($arg) {
		global $schemer;
		$args = func_get_args();
		call_user_func_array(array($schemer, "table"), $args);
	}
	//ADD COLUMN TO DESCRIPTION
	function column($table, $col) {
		global $schemer;
		$schemer->column($table, $col);
	}
	//DROP TABLE OR COLUMN FROM DESCRIPTION
	function drop($table, $col="") {
		global $schemer;
		$schemer->drop($table, $col);
	}
	function insert($table, $keys, $values) {
		global $schemer;
		$schemer->insert($table, $keys, $values);
	}
	function up() {}
	function down() {}
	function created() {}
	function removed() {}
}
?>
