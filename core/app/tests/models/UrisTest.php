<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
import("lib/test/ModelTest", "core");
class UrisTest extends ModelTest {
	
	var $model = "uris";
	
	function test_create() {
		$this->action("create", array("path" => "phpunit")); 
		$object = get("uris", sb("uris")->insert_id);
		//lets verify the explicit values were set
		$this->assertEquals("phpunit", $object['path']);
	}

	function test_delete() {
		//first assert that the record exists
		$object = get("uris", array("path" => "phpunit"));
		$this->assertEquals(empty($object), false);
		
		//remove it and assert that the record is gone
		$this->action("delete", $object);
		$object = get("uris", array("path" => "phpunit"));
		$this->assertEquals(empty($object), true);
	}

}
?>
