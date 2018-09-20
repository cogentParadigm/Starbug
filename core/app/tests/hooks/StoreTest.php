<?php
namespace Starbug\Core;

/**
 * The Fixture class. Fixtures hold data sets used by the testing harness.
 */
class StoreTest extends DatabaseTestCase {

  public function getDataSet() {
    return $this->createMySQLXMLDataSet(dirname(__FILE__).'/../fixture.xml');
  }

  public function setUp() {
    parent::setUp();
    global $container;
    $this->db = $container->get("Starbug\Core\DatabaseInterface");
    $this->models = $container->get("Starbug\Core\ModelFactoryInterface");
    $this->user = $container->get("Starbug\Core\IdentityInterface");
    $this->session = $container->get("Starbug\Core\SessionHandlerInterface");
  }

  /**
   * StoreAddslashesHook
   */
  public function testAddslashes() {
    // store a value with a quote
    $this->db->store("hook_store_addslashes", ["value" => "phpunit's"]);

    // retrieve the record
    $id = $this->models->get("hook_store_addslashes")->insert_id;
    $record = $this->db->query("hook_store_addslashes")->condition("id", $id)->one();

    // verify the quote is escaped
    $this->assertSame($record['value'], "phpunit\'s");

    // truncate the table
    $this->db->query("hook_store_addslashes")->truncate();
  }

  /**
   * StoreAliasHook
   */
  public function testAlias() {
    // obtain users from fixture
    $admin = $this->db->query("users")->condition("email", "admin@localhost")->one();
    $abdul = $this->db->query("users")->condition("email", "abdul@localhost")->one();

    // verify the users are there
    $this->assertArrayHasKey("id", $admin);
    $this->assertArrayHasKey("id", $abdul);

    // verify abdul's name is as we expect
    $this->assertSame("Abdul", $abdul['first_name']);
    $this->assertSame("User", $abdul['last_name']);

    // store record
    $q = $this->db->query("hook_store_alias");
    $q->set("by_email", "admin@localhost");
    $q->set("by_name", "Abdul User");
    $q->insert();

    // retrieve the record
    $id = $this->models->get("hook_store_alias")->insert_id;
    $record = $this->db->query("hook_store_alias")->condition("id", $id)->one();

    // verify that the values were converted properly
    $this->assertSame($admin['id'], $record["by_email"]);
    $this->assertSame($abdul['id'], $record["by_name"]);
  }

  /**
   * StoreCategoryHook
   */
  public function testGroup() {
    // get the user term
    $user = $this->db->query("terms")->conditions([
      "taxonomy" => "groups",
      "slug" => "user"
    ])->one();

    // get the admin term
    $admin = $this->db->query("terms")->conditions([
      "taxonomy" => "groups",
      "slug" => "admin"
    ])->one();

    // store a category
    // category fields have an alias of %taxonomy% %slug% (see the alias hook)
    // this means we can use the alias instead of an id, but we'll use the id
    // since we only want to test the category hook
    $this->db->store("hook_store_category", ["value" => "user"]);

    // retrieve the record
    $rid = $this->models->get("hook_store_category")->insert_id;
    $record = $this->db->get("hook_store_category", $rid);

    // verify the correct id is set
    $this->assertSame($user['id'], $record["value"]);

    // update the record
    $this->db->store("hook_store_category", ["id" => $rid, "value" => "admin"]);

    // retrieve the updated record
    $record = $this->db->get("hook_store_category", $rid);

    // verify the term id was updated
    $this->assertSame($admin['id'], $record["value"]);
  }

  /**
   * StoreConfirmHook
   */
  public function testConfirm() {
    // try to store with values that don't match
    $this->db->store("hook_store_confirm", ["value" => "one", "value_confirm" => "two"]);

    // verify the error exists
    $this->assertSame("Your value fields do not match", $this->db->errors["hook_store_confirm"]["value"][0]);

    // clear errors
    $this->db->errors = [];

    // store with matching values
    $this->db->store("hook_store_confirm", ["value" => "one", "value_confirm" => "one"]);

    // assert the lack of errors
    $this->assertFalse($this->db->errors());
  }

  /**
   * StoreDatetimeHook
   */
  public function testDatetime() {
    // store a value
    // anything strtotime can interpret will work
    $this->db->store("hook_store_datetime", ["value" => "February 12th, 1988"]);

    // retrieve the record
    $id = $this->models->get("hook_store_datetime")->insert_id;
    $record = $this->db->get("hook_store_datetime", $id);

    // assert that it has the correct value
    $this->assertSame("1988-02-12 00:00:00", $record["value"]);
  }

  /**
   * StoreDefaultHook
   */
  public function testDefault() {
    // store a record
    $this->db->store("hook_store_default", []);

    // retrieve the record
    $id = $this->models->get("hook_store_default")->insert_id;
    $record = $this->db->get("hook_store_default", $id);

    // assert that the default values have been stored
    $this->assertSame("test", $record['value']);
    $this->assertSame("", $record['value2']);
  }

  /**
   * StoreLengthHook
   */
  public function testLength() {
    // the length of this field is 128
    $over = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eleifend justo id adipiscing cursus. Maecenas placerat cras amet. Over.";
    $under = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi eleifend justo id adipiscing cursus. Maecenas placerat cras amet.";

    // try to store a string over 128 chars
    $this->db->store("hook_store_length", ["value" => $over]);

    // verify the error exists
    $this->assertSame("This field must be between 0 and 128 characters long.", $this->db->errors["hook_store_length"]["value"][0]);

    // clear errors
    $this->db->errors = [];

    // store with matching values
    $this->db->store("hook_store_length", ["value" => $under]);

    // assert the lack of errors
    $this->assertFalse($this->db->errors());
  }

  /**
   * StoreMaterializedPathHook
   */
  public function testMaterializedPath() {
    // store first record
    $this->db->store("hook_store_materialized_path", []);

    // retrieve the record
    $l1 = $this->models->get("hook_store_materialized_path")->insert_id;
    $l1_record = $this->db->get("hook_store_materialized_path", $l1);

    // the materialized path field should be empty for top level items
    $this->assertEmpty($l1_record["value_field"]);

    // store record 2, child of record 1
    $this->db->store("hook_store_materialized_path", ["parent" => $l1]);

    // retrieve the record
    $l2 = $this->models->get("hook_store_materialized_path")->insert_id;
    $l2_record = $this->db->get("hook_store_materialized_path", $l2);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1."-", $l2_record["value_field"]);

    // store record 3, child of record 2
    $this->db->store("hook_store_materialized_path", ["parent" => $l2]);

    // retrieve the record
    $l3 = $this->models->get("hook_store_materialized_path")->insert_id;
    $l3_record = $this->db->get("hook_store_materialized_path", $l3);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1."-".$l2."-", $l3_record["value_field"]);

    // store record 4, child of record 3
    $this->db->store("hook_store_materialized_path", ["parent" => $l3]);

    // retrieve the record
    $l4 = $this->models->get("hook_store_materialized_path")->insert_id;
    $l4_record = $this->db->get("hook_store_materialized_path", $l4);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1."-".$l2."-".$l3."-", $l4_record["value_field"]);

    // store record 5, child of record 4
    $this->db->store("hook_store_materialized_path", ["parent" => $l4]);

    // retrieve the record
    $l5 = $this->models->get("hook_store_materialized_path")->insert_id;
    $l5_record = $this->db->get("hook_store_materialized_path", $l5);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1."-".$l2."-".$l3."-".$l4."-", $l5_record["value_field"]);
  }

  /**
   * StoreMd5Hook
   */
  public function testMd5() {
    $str = "test";

    // store record
    $this->db->store("hook_store_md5", ["value" => $str]);

    // retrieve the record
    $id = $this->models->get("hook_store_md5")->insert_id;
    $record = $this->db->get("hook_store_md5", $id);

    // ensure the value has been md5 encoded
    $this->assertSame(md5($str), $record["value"]);
  }

  /**
   * StoreOptionalUpdate
   */
  public function testOptionalUpdate() {
    // store the value
    $this->db->store("hook_store_optional_update", ["value" => "starbug"]);

    // retrieve the record
    $id = $this->models->get("hook_store_optional_update")->insert_id;
    $record = $this->db->get("hook_store_optional_update", $id);

    // a ssert that the initial value was stored
    $this->assertSame("starbug", $record['value']);

    // update the record with an empty value
    $this->db->store("hook_store_optional_update", ["id" => $id, "value" => ""]);

    // retrieve the record
    $record = $this->db->get("hook_store_optional_update", $id);

    // assert that the initial value was not changed
    $this->assertSame("starbug", $record['value']);
  }

  /**
   * StoreOrderedHook
   */
  public function testOrdered() {
    // store 5 items
    $this->db->store("hook_store_ordered", []);
    $id1 = $this->models->get("hook_store_ordered")->insert_id;
    $r1 = $this->db->get("hook_store_ordered", $id1);
    $this->db->store("hook_store_ordered", []);
    $id2 = $this->models->get("hook_store_ordered")->insert_id;
    $r2 = $this->db->get("hook_store_ordered", $id2);
    $this->db->store("hook_store_ordered", []);
    $id3 = $this->models->get("hook_store_ordered")->insert_id;
    $r3 = $this->db->get("hook_store_ordered", $id3);
    $this->db->store("hook_store_ordered", []);
    $id4 = $this->models->get("hook_store_ordered")->insert_id;
    $r4 = $this->db->get("hook_store_ordered", $id4);
    $this->db->store("hook_store_ordered", []);
    $id5 = $this->models->get("hook_store_ordered")->insert_id;
    $r5 = $this->db->get("hook_store_ordered", $id5);

    // assert that they have incrementing values
    $this->assertSame("1", $r1['value']);
    $this->assertSame("2", $r2['value']);
    $this->assertSame("3", $r3['value']);
    $this->assertSame("4", $r4['value']);
    $this->assertSame("5", $r5['value']);
  }

  /**
   * StoreOwnerHook
   */
  public function testOwner() {
    // become nobody
    $this->user->clearUser();
    // store the record
    $this->db->store("hook_store_owner", []);

    // retrieve the record
    $id = $this->models->get("hook_store_owner")->insert_id;
    $record = $this->db->get("hook_store_owner", $id);

    // assert that the owner was stored
    $this->assertSame(null, $record['value']);

    // become root
    $this->user->setUser(["id" => 1, "groups" => ["admin", "root"]]);

    // store the record
    $this->db->store("hook_store_owner", []);

    // retrieve the record
    $id = $this->models->get("hook_store_owner")->insert_id;
    $record = $this->db->get("hook_store_owner", $id);

    // assert that the owner was stored
    $this->assertSame("1", $record['value']);

    // restore anonymous root
    $this->user->setUser(["id" => "NULL", "groups" => ["root"]]);
  }

  /**
   * StorePasswordHook
   */
  public function testPassword() {
    $pass = "myPassword";

    // Store record.
    $this->db->store("hook_store_password", ["value" => $pass]);

    // Retrieve record.
    $id = $this->models->get("hook_store_password")->insert_id;
    $record = $this->db->get("hook_store_password", $id);

    // Assert that the hashed password was stored.
    $this->assertTrue(strlen($record['value']) > 64);
  }

  /**
   * StoreReferencesHook
   */
  public function testReferences() {
    // store a user
    $this->db->store("users", ["email" => "hook_store_references"]);
    $uid = $this->models->get("users")->insert_id;

    // store a record
    $this->db->store("hook_store_references", ["value" => ""]);

    // retrieve record
    $id = $this->models->get("hook_store_references")->insert_id;
    $record = $this->db->get("hook_store_references", $id);

    // assert that the record contains the last inserted users id
    $this->assertSame($uid, $record['value']);

    // remove uri and truncate table
    $this->db->query("hook_store_references")->unsafe_truncate();
    $this->db->query("users")->condition("email", "hook_store_references")->delete();
  }

  /**
   * StoreRequiredHook
   */
  public function testRequired() {
    // attempt insert
    $this->db->store("hook_store_required");

    // verify the error exists
    $this->assertSame("This field is required.", $this->db->errors["hook_store_required"]["value"][0]);

    // clear errors
    $this->db->errors = [];

    // store a record
    $this->db->store("hook_store_required", ["value" => "value"]);

    // retrieve the record
    $id = $this->models->get("hook_store_required")->insert_id;
    $record = $this->db->get("hook_store_required", $id);

    // assert that the value is what we stored
    $this->assertSame("value", $record['value']);

    // try to update it without specifying the value
    $this->db->store("hook_store_required", ["id" => $id]);

    // verify the error exists
    $this->assertSame("This field is required.", $this->db->errors["hook_store_required"]["value"][0]);

    // clear errors
    $this->db->errors = [];

    // do a successful update
    $record['value'] = "changed";
    $this->db->store("hook_store_required", $record);

    // retrieve the record
    $row = $this->db->get("hook_store_required", $id);

    // assert that the value is what we stored
    $this->assertSame("changed", $record['value']);
  }

  /**
   * StoreSlugHook
   */
  public function testSlug() {
    // store the record
    $this->db->store("hook_store_slug", ["title_field" => "Abdul's House of Rugs"]);

    // retrieve the record
    $id = $this->models->get("hook_store_slug")->insert_id;
    $record = $this->db->get("hook_store_slug", $id);

    // assert that the slug is stored correctly
    $this->assertSame("abduls-house-of-rugs", $record['slug_field']);
  }

  /**
   * StoreTermsHook
   */
  public function testTerms() {
    // store terms
    $this->db->store("hook_store_terms", ["value" => "user,admin"]);

    // get the id
    $id = $this->models->get("hook_store_terms")->insert_id;

    // retrieve the entries
    $terms = $this->db->query("hook_store_terms_value")->conditions(["hook_store_terms_id" => $id])->select("value_id.slug as slug")->sort("slug")->all();

    // verify the records are what we expect
    $this->assertSame("admin", $terms[0]["slug"]);
    $this->assertSame("user", $terms[1]["slug"]);

    // update the terms (remove deleted)
    $this->db->store("hook_store_terms", ["id" => $id, "value" => "-admin"]);

    // retrieve the entries
    $terms = $this->db->query("hook_store_terms_value")->conditions(["hook_store_terms_id" => $id])->select("value_id.slug as slug")->sort("slug")->all();

    // verify the records are what we expect
    $this->assertSame("user", $terms[0]["slug"]);

    // update the terms (add deleted, remove others)
    $this->db->store("hook_store_terms", ["id" => $id, "value" => "admin"]);

    // retrieve the entries
    $terms = $this->db->query("hook_store_terms_value")->conditions(["hook_store_terms_id" => $id])->select("value_id.slug as slug")->sort("slug")->all();

    // verify the records are what we expect
    $this->assertSame("admin", $terms[0]["slug"]);
  }

  /**
   * StoreTimeHook
   */
  public function testTime() {
    // store a record
    $before = time();
    $this->db->store("hook_store_time");
    $after = time();

    // retrieve the record
    $id = $this->models->get("hook_store_time")->insert_id;
    $record = $this->db->get("hook_store_time", $id);

    // verify that 2 time stamps were stored
    $create = strtotime($record['creation_stamp']);
    $update = strtotime($record['update_stamp']);
    $this->assertTrue(($create >= $before && $create <= $after));
    $this->assertTrue(($update >= $before && $update <= $after));

    // sleep for 1 second so the update time is different
    sleep(1);

    // update the record
    $before_update = time();
    $this->db->store("hook_store_time", ["id" => $id]);
    $after_update = time();

    // retrieve the record
    $record = $this->db->get("hook_store_time", $id);

    // verify that 2 time stamps were stored
    $create = strtotime($record['creation_stamp']);
    $update = strtotime($record['update_stamp']);
    $this->assertTrue(($create >= $before && $create <= $after));
    $this->assertFalse(($update >= $before && $update <= $after));
    $this->assertTrue(($update >= $before_update && $update <= $after_update));
  }

  /**
   * StoreUniqueHook
   */
  public function testUnique() {
    // store a value
    $this->db->store("hook_store_unique", ["value" => "one"]);

    // assert that there are no errors
    $this->assertFalse($this->db->errors());

    // try it again
    $this->db->store("hook_store_unique", ["value" => "one"]);

    // verify the error exists
    $this->assertSame("That value already exists.", $this->db->errors["hook_store_unique"]["value"][0]);

    // clear errors
    $this->db->errors = [];
  }
}
