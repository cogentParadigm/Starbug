<?php
namespace Starbug\Core;

use Starbug\Auth\Identity;
use Starbug\Db\DatabaseInterface;

/**
 * The Fixture class. Fixtures hold data sets used by the testing harness.
 */
class StoreTest extends DatabaseTestCase {

  protected function getDataSets() {
    return [
      $this->createYamlDataSet(dirname(__FILE__)."/../fixture.yml")
    ];
  }

  public function setUp() : void {
    parent::setUp();
    global $container;
    $this->db = $container->get(DatabaseInterface::class);
    $this->session = $container->get("Starbug\Auth\SessionHandlerInterface");
  }

  /**
   * StoreAddslashesHook
   */
  public function testAddslashes() {
    // store a value with a quote
    $this->db->store("hook_store_addslashes", ["value" => "phpunit's"]);

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_addslashes");
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
    $query = $this->db->query("hook_store_alias");
    $query->set("by_email", "admin@localhost");
    $query->set("by_name", "Abdul User");
    $query->insert();

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_alias");
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
    $rid = $this->db->getInsertId("hook_store_category");
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
    $this->db->errors->set([]);

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
    $id = $this->db->getInsertId("hook_store_datetime");
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
    $id = $this->db->getInsertId("hook_store_default");
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
    $this->db->errors->set([]);

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
    $l1_id = $this->db->getInsertId("hook_store_materialized_path");
    $l1_record = $this->db->get("hook_store_materialized_path", $l1_id);

    // the materialized path field should be empty for top level items
    $this->assertEmpty($l1_record["value_field"]);

    // store record 2, child of record 1
    $this->db->store("hook_store_materialized_path", ["parent" => $l1_id]);

    // retrieve the record
    $l2_id = $this->db->getInsertId("hook_store_materialized_path");
    $l2_record = $this->db->get("hook_store_materialized_path", $l2_id);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1_id."-", $l2_record["value_field"]);

    // store record 3, child of record 2
    $this->db->store("hook_store_materialized_path", ["parent" => $l2_id]);

    // retrieve the record
    $l3_id = $this->db->getInsertId("hook_store_materialized_path");
    $l3_record = $this->db->get("hook_store_materialized_path", $l3_id);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1_id."-".$l2_id."-", $l3_record["value_field"]);

    // store record 4, child of record 3
    $this->db->store("hook_store_materialized_path", ["parent" => $l3_id]);

    // retrieve the record
    $l4_id = $this->db->getInsertId("hook_store_materialized_path");
    $l4_record = $this->db->get("hook_store_materialized_path", $l4_id);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1_id."-".$l2_id."-".$l3_id."-", $l4_record["value_field"]);

    // store record 5, child of record 4
    $this->db->store("hook_store_materialized_path", ["parent" => $l4_id]);

    // retrieve the record
    $l5_id = $this->db->getInsertId("hook_store_materialized_path");
    $l5_record = $this->db->get("hook_store_materialized_path", $l5_id);

    // the materialized path field should show the correct ancestry
    $this->assertSame("-".$l1_id."-".$l2_id."-".$l3_id."-".$l4_id."-", $l5_record["value_field"]);
  }

  /**
   * StoreMd5Hook
   */
  public function testMd5() {
    $str = "test";

    // store record
    $this->db->store("hook_store_md5", ["value" => $str]);

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_md5");
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
    $id = $this->db->getInsertId("hook_store_optional_update");
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
    $id1 = $this->db->getInsertId("hook_store_ordered");
    $record1 = $this->db->get("hook_store_ordered", $id1);
    $this->db->store("hook_store_ordered", []);
    $id2 = $this->db->getInsertId("hook_store_ordered");
    $record2 = $this->db->get("hook_store_ordered", $id2);
    $this->db->store("hook_store_ordered", []);
    $id3 = $this->db->getInsertId("hook_store_ordered");
    $record3 = $this->db->get("hook_store_ordered", $id3);
    $this->db->store("hook_store_ordered", []);
    $id4 = $this->db->getInsertId("hook_store_ordered");
    $record4 = $this->db->get("hook_store_ordered", $id4);
    $this->db->store("hook_store_ordered", []);
    $id5 = $this->db->getInsertId("hook_store_ordered");
    $record5 = $this->db->get("hook_store_ordered", $id5);

    // assert that they have incrementing values
    $this->assertSame("1", $record1['value']);
    $this->assertSame("2", $record2['value']);
    $this->assertSame("3", $record3['value']);
    $this->assertSame("4", $record4['value']);
    $this->assertSame("5", $record5['value']);
  }

  /**
   * StoreOwnerHook
   */
  public function testOwner() {
    // become nobody
    $this->session->destroy();
    // store the record
    $this->db->store("hook_store_owner", []);

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_owner");
    $record = $this->db->get("hook_store_owner", $id);

    // assert that the owner was stored
    $this->assertSame(null, $record['value']);

    // become root
    $rootIdentity = new Identity(1, "", ["admin"]);
    $this->session->createSession($rootIdentity, false);

    // store the record
    $this->db->store("hook_store_owner", []);

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_owner");
    $record = $this->db->get("hook_store_owner", $id);

    // assert that the owner was stored
    $this->assertSame("1", $record['value']);

    // restore nobody
    $this->session->destroy();
  }

  /**
   * StorePasswordHook
   */
  public function testPassword() {
    $pass = "myPassword";

    // Store record.
    $this->db->store("hook_store_password", ["value" => $pass]);

    // Retrieve record.
    $id = $this->db->getInsertId("hook_store_password");
    $record = $this->db->get("hook_store_password", $id);

    // Assert that the hashed password was stored.
    $this->assertTrue(password_verify($pass, $record["value"]));
  }

  /**
   * StoreReferencesHook
   */
  public function testReferences() {
    // store a user
    $this->db->store("users", ["email" => "hook_store_references"]);
    $uid = $this->db->getInsertId("users");

    // store a record
    $this->db->store("hook_store_references", ["value" => ""]);

    // retrieve record
    $id = $this->db->getInsertId("hook_store_references");
    $record = $this->db->get("hook_store_references", $id);

    // assert that the record contains the last inserted users id
    $this->assertSame($uid, $record['value']);

    // remove uri and truncate table
    $this->db->query("hook_store_references")->unsafeTruncate();
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
    $this->db->errors->set([]);

    // store a record
    $this->db->store("hook_store_required", ["value" => "value"]);

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_required");
    $record = $this->db->get("hook_store_required", $id);

    // assert that the value is what we stored
    $this->assertSame("value", $record['value']);

    // try to update it without specifying the value
    $this->db->store("hook_store_required", ["id" => $id]);

    // verify the error exists
    $this->assertSame("This field is required.", $this->db->errors["hook_store_required"]["value"][0]);

    // clear errors
    $this->db->errors->set([]);

    // do a successful update
    $record['value'] = "changed";
    $this->db->store("hook_store_required", $record);

    // retrieve the record
    $row = $this->db->get("hook_store_required", $id);

    // assert that the value is what we stored
    $this->assertSame("changed", $row['value']);
  }

  /**
   * StoreSlugHook
   */
  public function testSlug() {
    // store the record
    $this->db->store("hook_store_slug", ["title_field" => "Abdul's House of Rugs"]);

    // retrieve the record
    $id = $this->db->getInsertId("hook_store_slug");
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
    $id = $this->db->getInsertId("hook_store_terms");

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
    $id = $this->db->getInsertId("hook_store_time");
    $record = $this->db->get("hook_store_time", $id);

    // verify that 2 time stamps were stored
    $create = strtotime($record['creation_stamp']);
    $update = strtotime($record['update_stamp']);
    $this->assertTrue(($create >= $before && $create <= $after));
    $this->assertTrue(($update >= $before && $update <= $after));

    // sleep for 1 second so the update time is different
    sleep(1);

    // update the record
    $beforeUpdate = time();
    $this->db->store("hook_store_time", ["id" => $id]);
    $afterUpdate = time();

    // retrieve the record
    $record = $this->db->get("hook_store_time", $id);

    // verify that 2 time stamps were stored
    $create = strtotime($record['creation_stamp']);
    $update = strtotime($record['update_stamp']);
    $this->assertTrue(($create >= $before && $create <= $after));
    $this->assertFalse(($update >= $before && $update <= $after));
    $this->assertTrue(($update >= $beforeUpdate && $update <= $afterUpdate));
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
    $this->db->errors->set([]);
  }
}
