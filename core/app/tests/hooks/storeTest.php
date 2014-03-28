<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/tests/hooks/store/addslashes.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup test
 */
import("lib/test/UnitTest", "core");
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup test
 */
class storeTest extends UnitTest {
	
	var $fixtures = array("users");

	/**
	 * hook_store_addslashes
	 */
	function test_addslashes() {
		//store a value with a quote
		store("hook_store_addslashes", "value:phpunit's");
		
		//retrieve the record
		$id = sb("hook_store_addslashes")->insert_id;
		$record = query("hook_store_addslashes")->condition("id", $id)->one();
		
		//verify the quote is escaped
		$this->assertSame($record['value'], "phpunit\'s");
		
		//truncate the table
		query("hook_store_addslashes")->truncate();
	}

	/**
	 * hook_store_alias
	 */	
	function test_alias() {
		//obtain users from fixture
		$admin = query("users")->condition("email", "admin@localhost")->one();
		$abdul = query("users")->condition("email", "abdul@localhost")->one();
		
		//verify the users are there
		$this->assertArrayHasKey("id", $admin);
		$this->assertArrayHasKey("id", $abdul);
		
		//verify abdul's name is as we expect
		$this->assertSame("Abdul", $abdul['first_name']);
		$this->assertSame("User", $abdul['last_name']);
		
		//store record
		$q = query("hook_store_alias");
		$q->set("by_email", "admin@localhost");
		$q->set("by_name", "Abdul User");
		$q->insert();

		//retrieve the record
		$id = sb("hook_store_alias")->insert_id;
		$record = query("hook_store_alias")->condition("id", $id)->one();
		
		//verify that the values were converted properly
		$this->assertSame($admin['id'], $record["by_email"]);
		$this->assertSame($abdul['id'], $record["by_name"]);
		
		//truncate the table
		query("hook_store_alias")->truncate();
	}
	
	/**
	 * hook_store_category
	 */
	function test_category() {
		//get the published term
		$term = query("terms")->conditions(array(
			"taxonomy" => "statuses",
			"slug" => "published"
		))->one();
		
		//get the deleted term
		$del = query("terms")->conditions(array(
			"taxonomy" => "statuses",
			"slug" => "deleted"
		))->one();
		
		//store a category
		//category fields have an alias of %taxonomy% %slug% (see the alias hook)
		//this means we can use the alias instead of an id, but we'll use the id
		//since we only want to test the category hook
		store("hook_store_category", "value:".$term['id']);
		
		//retrieve the record
		$rid = sb("hook_store_category")->insert_id;
		$record = get("hook_store_category", $rid);
		
		//retrieve the terms_index entry
		$tid = sb("terms_index")->insert_id;
		$category = get("terms_index", $tid);
		
		//verify the terms_index record is what we expect
		$this->assertSame("hook_store_category", $category["type"]);
		$this->assertSame($record["id"], $category["type_id"]);
		$this->assertSame($term['id'], $category["terms_id"]);
		$this->assertSame("value", $category["rel"]);
		
		//update the record
		store("hook_store_category", "id:$rid  value:".$del['id']);
		
		//retrieve the updated record
		$category = get("terms_index", $tid);
		
		//verify the terms_index record was updated
		$this->assertSame("hook_store_category", $category["type"]);
		$this->assertSame($record["id"], $category["type_id"]);
		$this->assertSame($del['id'], $category["terms_id"]);
		$this->assertSame("value", $category["rel"]);
		
		//truncate the table
		query("terms_index")->condition("type", "hook_store_category")->delete();
		query("hook_store_category")->truncate();
	}
	
	/**
	 * hook_store_confirm
	 */
	function test_confirm() {
		//try to store with values that don't match
		store("hook_store_confirm", "value:one  value_confirm:two");
		
		//verify the error exists
		$this->assertSame("Your value fields do not match", sb()->errors["hook_store_confirm"]["value"][0]);
		
		//clear errors
		sb()->errors = array();
		
		//store with matching values
		store("hook_store_confirm", "value:one  value_confirm:one");
		
		//assert the lack of errors
		$this->assertFalse(errors());
		
		//truncate the table
		query("hook_store_confirm")->truncate();
	}
	
	/**
	 * hook_store_datetime
	 */
	function test_datetime() {
		//store a value
		//anything strtotime can interpret will work
		store("hook_store_datetime", "value:February 12th, 1988");
		
		//retrieve the record
		$id = sb("hook_store_datetime")->insert_id;
		$record = get("hook_store_datetime", $id);
		
		//assert that it has the correct value
		$this->assertSame("1988-02-12 00:00:00", $record["value"]);
		
		//truncate the table
		query("hook_store_datetime")->truncate();
	}
	
	/**
	 * hook_store_default
	 */
	function test_default() {
		//store a record
		store("hook_store_default", array());
		
		//retrieve the record
		$id = sb("hook_store_default")->insert_id;
		$record = get("hook_store_default", $id);
		
		//assert that the default values have been stored
		$this->assertSame("test", $record['value']);
		$this->assertSame("", $record['value2']);
		
		//truncate the table
		query("hook_store_default")->truncate();
	}
	
	/**
	 * hook_store_length
	 */
	function test_length() {
		//the length of this field is 128
		$over = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eleifend justo id adipiscing cursus. Maecenas placerat cras amet. Over.";
		$under = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eleifend justo id adipiscing cursus. Maecenas placerat cras amet.";
		
		//try to store a string over 128 chars
		store("hook_store_length", array("value" => $over));
		
		//verify the error exists
		$this->assertSame("This field must be between 0 and 128 characters long.", sb()->errors["hook_store_length"]["value"][0]);
		
		//clear errors
		sb()->errors = array();
		
		//store with matching values
		store("hook_store_length", array("value" => $under));
		
		//assert the lack of errors
		$this->assertFalse(errors());
		
		//truncate the table
		query("hook_store_length")->truncate();
	}
	
	/**
	 * hook_store_materialized_path
	 */
	function test_materialized_path() {
		//store first record
		store("hook_store_materialized_path", array());
		
		//retrieve the record
		$l1 = sb("hook_store_materialized_path")->insert_id;
		$l1_record = get("hook_store_materialized_path", $l1);
		
		//the materialized path field should be empty for top level items
		$this->assertEmpty($l1_record["value_field"]);
		
		//store record 2, child of record 1
		store("hook_store_materialized_path", "parent:".$l1);

		//retrieve the record
		$l2 = sb("hook_store_materialized_path")->insert_id;
		$l2_record = get("hook_store_materialized_path", $l2);
		
		//the materialized path field should show the correct ancestry
		$this->assertSame("-".$l1."-", $l2_record["value_field"]);
		
		//store record 3, child of record 2
		store("hook_store_materialized_path", "parent:".$l2);

		//retrieve the record
		$l3 = sb("hook_store_materialized_path")->insert_id;
		$l3_record = get("hook_store_materialized_path", $l3);
		
		//the materialized path field should show the correct ancestry
		$this->assertSame("-".$l1."-".$l2."-", $l3_record["value_field"]);
		
		//store record 4, child of record 3
		store("hook_store_materialized_path", "parent:".$l3);

		//retrieve the record
		$l4 = sb("hook_store_materialized_path")->insert_id;
		$l4_record = get("hook_store_materialized_path", $l4);
		
		//the materialized path field should show the correct ancestry
		$this->assertSame("-".$l1."-".$l2."-".$l3."-", $l4_record["value_field"]);
		
		//store record 5, child of record 4
		store("hook_store_materialized_path", "parent:".$l4);

		//retrieve the record
		$l5 = sb("hook_store_materialized_path")->insert_id;
		$l5_record = get("hook_store_materialized_path", $l5);
		
		//the materialized path field should show the correct ancestry
		$this->assertSame("-".$l1."-".$l2."-".$l3."-".$l4."-", $l5_record["value_field"]);
		
		//truncate the table
		query("hook_store_materialized_path")->truncate();
	}
	
	/**
	 * hook_store_md5
	 */
	function test_md5() {
		$str = "test";
		
		//store record
		store("hook_store_md5", "value:".$str);
		
		//retrieve the record
		$id = sb("hook_store_md5")->insert_id;
		$record = get("hook_store_md5", $id);
		
		//ensure the value has been md5 encoded
		$this->assertSame(md5($str), $record["value"]);
		
		//truncate the table
		query("hook_store_md5")->truncate();
	}
	
	/**
	 * hook_store_optional_update
	 */
	function test_optional_update() {
		//store the value
		store("hook_store_optional_update", "value:starbug");
		
		//retrieve the record
		$id = sb("hook_store_optional_update")->insert_id;
		$record = get("hook_store_optional_update", $id);
		
		//assert that the initial value was stored
		$this->assertSame("starbug", $record['value']);
		
		//update the record with an empty value
		store("hook_store_optional_update", array("id" => $id, "value" => ""));
		
		//retrieve the record
		$record = get("hook_store_optional_update", $id);

		//assert that the initial value was not changed
		$this->assertSame("starbug", $record['value']);

		//truncate the table
		query("hook_store_optional_update")->truncate();
	}
	
	/**
	 * hook_store_ordered
	 */
	function test_ordered() {
		//store 5 items
		store("hook_store_ordered", array());
		$id1 = sb("hook_store_ordered")->insert_id;
		$r1 = get("hook_store_ordered", $id1);
		store("hook_store_ordered", array());
		$id2 = sb("hook_store_ordered")->insert_id;
		$r2 = get("hook_store_ordered", $id2);
		store("hook_store_ordered", array());
		$id3 = sb("hook_store_ordered")->insert_id;
		$r3 = get("hook_store_ordered", $id3);
		store("hook_store_ordered", array());
		$id4 = sb("hook_store_ordered")->insert_id;
		$r4 = get("hook_store_ordered", $id4);
		store("hook_store_ordered", array());
		$id5 = sb("hook_store_ordered")->insert_id;
		$r5 = get("hook_store_ordered", $id5);

		//assert that they have incrementing values
		$this->assertSame("1", $r1['value']);
		$this->assertSame("2", $r2['value']);
		$this->assertSame("3", $r3['value']);
		$this->assertSame("4", $r4['value']);
		$this->assertSame("5", $r5['value']);

		//truncate the table
		query("hook_store_ordered")->truncate();
	}
	
	/**
	 * hook_store_owner
	 */
	function test_owner() {
		//store the record
		store("hook_store_owner", array());
		
		//retrieve the record
		$id = sb("hook_store_owner")->insert_id;
		$record = get("hook_store_owner", $id);
		
		//assert that the owner was stored
		$this->assertSame("1", $record['value']);
		
		//truncate the table
		query("hook_store_owner")->truncate();
	}
	
	/**
	 * hook_store_password
	 */
	function test_password() {
		$pass = "myPassword";
		
		//store record
		store("hook_store_password", "value:".$pass);
		
		//retrieve record
		$id = sb("hook_store_password")->insert_id;
		$record = get("hook_store_password", $id);
		
		//assert that the hashed password was stored
		$this->assertTrue(Session::authenticate($record['value'], $pass, "0", Etc::HMAC_KEY));
		
		//truncate table
		query("hook_store_password")->truncate();
	}
	
	/**
	 * hook_store_references
	 */
	function test_references() {
		//store a uri
		store("uris", "path:hook_store_references");
		$uid = sb("uris")->insert_id;
		
		//store a record
		store("hook_store_references", array("value" => ""));
		
		//retrieve record
		$id = sb("hook_store_references")->insert_id;
		$record = get("hook_store_references", $id);
		
		//assert that the record contains the last inserted uris id
		$this->assertSame($uid, $record['value']);
		
		//remove uri and truncate table
		query("hook_store_references")->truncate();
		query("uris")->condition("path", "hook_store_references")->delete();
	}
	
	/**
	 * hook_store_required
	 */
	function test_required() {
		//attempt insert
		store("hook_store_required");
		
		//verify the error exists
		$this->assertSame("This field is required.", sb()->errors["hook_store_required"]["value"][0]);
		
		//clear errors
		sb()->errors = array();
		
		//store a record
		store("hook_store_required", "value:value");
		
		//retrieve the record
		$id = sb("hook_store_required")->insert_id;
		$record = get("hook_store_required", $id);
		
		//assert that the value is what we stored
		$this->assertSame("value", $record['value']);
		
		//try to update it without specifying the value
		store("hook_store_required", array("id" => $id));
		
		//verify the error exists
		$this->assertSame("This field is required.", sb()->errors["hook_store_required"]["value"][0]);
		
		//clear errors
		sb()->errors = array();
		
		//do a successful update
		$record['value'] = "changed";
		store("hook_store_required", $record);
		
		//retrieve the record
		$row = get("hook_store_required", $id);
		
		//assert that the value is what we stored
		$this->assertSame("changed", $record['value']);
		
		//empty the table
		query("hook_store_required")->truncate();
	}
	
	/**
	 * hook_store_slug
	 */
	function test_slug() {
		//store the record
		store("hook_store_slug", array("title_field" => "Abdul's House of Rugs"));
		
		//retrieve the record
		$id = sb("hook_store_slug")->insert_id;
		$record = get("hook_store_slug", $id);
		
		//assert that the slug is stored correctly
		$this->assertSame("abduls-house-of-rugs", $record['slug_field']);
		
		//empty the table
		query("hook_store_slug")->truncate();
	}
	
	/**
	 * hook_store_terms
	 */
	function test_terms() {
		//store terms
		store("hook_store_terms", "statuses:published,pending,deleted");
		
		//get the id
		$id = sb("hook_store_terms")->insert_id;
		
		//retrieve the terms_index entries
		$terms = query("terms_index")->conditions("type:hook_store_terms  rel:statuses  type_id:$id")->select("terms_id.slug as slug")->sort("slug")->all();
		
		//verify the terms_index records are what we expect
		$this->assertSame("deleted", $terms[0]["slug"]);
		$this->assertSame("pending", $terms[1]["slug"]);
		$this->assertSame("published", $terms[2]["slug"]);
		
		//update the terms (remove deleted)
		store("hook_store_terms", "id:$id  statuses:-deleted");
		
		//retrieve the terms_index entries
		$terms = query("terms_index")->conditions("type:hook_store_terms  rel:statuses  type_id:$id")->select("terms_id.slug as slug")->sort("slug")->all();
		
		//verify the terms_index records are what we expect
		$this->assertSame("pending", $terms[0]["slug"]);
		$this->assertSame("published", $terms[1]["slug"]);
		
		//update the terms (add deleted, remove others)
		store("hook_store_terms", "id:$id  statuses:deleted,-~");
		
		//retrieve the terms_index entries
		$terms = query("terms_index")->conditions("type:hook_store_terms  rel:statuses  type_id:$id")->select("terms_id.slug as slug")->sort("slug")->all();
		
		//verify the terms_index records are what we expect
		$this->assertSame("deleted", $terms[0]["slug"]);
		
		//truncate the table
		query("terms_index")->condition("type", "hook_store_terms")->delete();
		query("hook_store_terms")->truncate();
	}
	
	/**
	 * hook_store_time
	 */
	function test_time() {
		
	}
	
	/**
	 * hook_store_unique
	 */
	function test_unique() {
		
	}
	
}
?>
