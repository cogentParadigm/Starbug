<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/db/Fixture.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup db
 */
class Fixture {
	var $type = '';
	var $records = array();
	var $ids = array();
	/**
	 * overridable function
	 * set $records here to prevent storing all records
	 */
	function setUp() {}
	/**
	 * overridable function
	 * unset $records to prevent removing records
	 */
	function tearDown() {}
	/**
	 * setUp function
	 */
	function _setUp() {
		$this->storeAll();
		$this->setUp();
	}
	/**
	 * tearDown function
	 */
	function _tearDown() {
		$this->tearDown();
		$this->removeAll();
	}
	
	function store($idx) {
		store($this->type, $this->records[$idx]);
		$this->ids[$idx] = sb("insert_id");
	}
	
	function storeAll() {
		foreach ($this->records as $idx => $r) $this->store($idx);
	}
	
	function remove($idx) {
		if (isset($this->ids[$idx])) {
			remove($this->type, "id='".$this->ids[$idx]."'");
			unset($this->ids[$idx]);
		}
	}
	
	function removeAll() {
		foreach ($this->ids as $idx => $id) $this->remove($idx);
	}

	public function __call($method, $args) {
		global $harness;
		if(method_exists($harness, $method)) return call_user_func_array(array($harness, $method), $args);
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}
}
?>
