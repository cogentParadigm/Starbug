<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
class UrisTest extends PHPUnit_Framework_TestCase {
	
	function test_create() {
		sb("uris", "create", star("path:phpunit"));
		$object = get("uris", sb("insert_id"));
		//lets verify the explicit values were set
		$this->assertEquals($object['path'], "phpunit");
		$this->assertEquals($object['title'], "Phpunit");
	}

	function test_delete() {
		//first assert that the record exists
		$object = get("uris", array("path" => "phpunit"));
		$this->assertEquals(empty($object), false);
		
		//remove it and assert that the record is gone
		remove("uris", "id='".$object['id']."'");
		$user = get("uris", array("path" => "phpunit"));
		$this->assertEquals(empty($object), true);
	}

}
?>
