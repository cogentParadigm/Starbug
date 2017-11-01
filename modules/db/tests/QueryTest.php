<?php
namespace Starbug\Db\Tests;
use PHPUnit_Framework_TestCase;
class QueryTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		global $container;
		$this->db = $container->get("Starbug\Core\DatabaseInterface");
		$this->user = $container->get("Starbug\Core\IdentityInterface");
	}

	/**
	 * Test the IteratorAggregate interface
	 */
	function test_iteration() {
		$query = $this->db->query("users");
		foreach ($query as $user) {
			$this->assertArrayHasKey("id", $user);
			$this->assertArrayHasKey("email", $user);
			break;
		}
	}

	/**
	 * Test table aliases
	 */
	function test_alias() {
		$query = $this->db->query("users as people");

		//expected output
		$expected = "SELECT `people`.* FROM `".$this->db->prefix("users")."` AS `people`";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test join delimiters:
	 * , - comma for inner join
	 * < - left angle bracket for left join
	 * > - right angle bracket for right join
	 */
	function test_inner_join() {
		$query = $this->db->query("settings,users");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` INNER JOIN `".$this->db->prefix("users")."` AS `users`";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_left_join() {
		$query = $this->db->query("settings<users");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `users`";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_right_join() {
		$query = $this->db->query("settings>users");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` RIGHT JOIN `".$this->db->prefix("users")."` AS `users`";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}


	/**
	 * Test ON clauses in query models
	 */
	function test_join_on() {
		$query = $this->db->query("settings,users on settings.id=users.id");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` INNER JOIN `".$this->db->prefix("users")."` AS `users` ON settings.id=users.id";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}


	/**
	 * Test aliases in query models
	 */
	function test_join_aliases() {
		$query = $this->db->query("settings as pages,users as people");

		//expected output
		$expected = "SELECT `pages`.* FROM `".$this->db->prefix("settings")."` AS `pages` INNER JOIN `".$this->db->prefix("users")."` AS `people`";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test ON clauses and aliases together in query models
	 */
	function test_join_on_and_aliases() {
		$query = $this->db->query("settings as pages,users as people on pages.id=people.id");

		//expected output
		$expected = "SELECT `pages`.* FROM `".$this->db->prefix("settings")."` AS `pages` INNER JOIN `".$this->db->prefix("users")."` AS `people` ON pages.id=people.id";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test ON clauses and aliases together using extended syntax
	 */
	function test_join_on_and_aliases_extended() {
		$query = $this->db->query("settings as pages");

		$query->innerJoin("users as people")->on("pages.id=people.id");
		//this is also valid
		//$query->innerJoin("users as people on pages.id=people.id");

		//expected output
		$expected = "SELECT `pages`.* FROM `".$this->db->prefix("settings")."` AS `pages` INNER JOIN `".$this->db->prefix("users")."` AS `people` ON pages.id=people.id";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test select
	 */
	function test_select() {
		$query = $this->db->query("users");
		$query->select("CONCAT(id, ' ', email) as label");

		// the code above should produce the query below
		$expected = "SELECT CONCAT(id, ' ', email) as label FROM `".$this->db->prefix("users")."` AS `users`";

		//compare queries
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test expansions in select clauses made by using the dot syntax on reference fields.
	 * For example, settings.owner references users.id, and therefore you can select fields of the references row using the syntax:
	 * SELECT settings.owner.first_name
	 */
	function test_select_expansion() {
		$query = $this->db->query("settings");
		$query->select("CONCAT(owner.first_name, ' ', owner.last_name) as name");

		// the code above should produce the query below
		$expected = "SELECT CONCAT(`settings_owner`.first_name, ' ', `settings_owner`.last_name) as name ".
			"FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `settings_owner` ON settings_owner.id=settings.owner";

		//compare queries
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test where clauses
	 */
	function test_where() {
		$query = $this->db->query("users");
		$query->where("users.email LIKE '%email%'");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE `users`.email LIKE '%email%'";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test parameterized conditions
	 */
	function test_condition() {
		$query = $this->db->query("users");
		$query->condition("users.email", "%email%", "LIKE");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE `users`.email LIKE :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("%email%", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion() {
		$query = $this->db->query("settings");
		$query->condition("settings.owner.email", "root");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `settings_owner` ON settings_owner.id=settings.owner WHERE `settings_owner`.email = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("root", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion_select() {
		$query = $this->db->query("settings")->select("settings.owner.email")->condition("settings.owner.email", "root");


		//expected output
		$expected = "SELECT `settings_owner`.email FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `settings_owner` ON settings_owner.id=settings.owner WHERE `settings_owner`.email = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("root", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion_many() {
		$query = $this->db->query("terms");
		$query->condition("images.mime_type", "image/png");

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` WHERE :default0 IN (SELECT terms_images.mime_type FROM ".$this->db->prefix("terms_images")." terms_images_lookup INNER JOIN ".$this->db->prefix("files")." terms_images ON terms_images.id=terms_images_lookup.images_id WHERE terms_images_lookup.terms_id=terms.id)";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("image/png", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 * when not using =, !=, IN, or NOT IN the sub-query comparison does not work, and therefore the query should join to obtain the field for comparison
	 */
	function test_condition_expansion_many_join() {
		$query = $this->db->query("terms");
		$query->condition("images.mime_type", "image/%", "LIKE")->group("terms.id");

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` LEFT JOIN `".$this->db->prefix("terms_images")."` AS `terms_images_lookup` ON terms_images_lookup.terms_id=terms.id LEFT JOIN `".$this->db->prefix("files")."` AS `terms_images` ON terms_images.id=terms_images_lookup.images_id WHERE `terms_images`.mime_type LIKE :default0 GROUP BY `terms`.id";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("image/%", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion_category() {
		$query = $this->db->query("settings");
		$query->condition("settings.category.slug", "general");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("terms")."` AS `settings_category` ON settings_category.id=settings.category WHERE `settings_category`.slug = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("general", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion_category_explicit() {
		$query = $this->db->query("settings");
		$query->condition("settings.category.term", "General");

		//expected output
		$expected = "SELECT `settings`.* FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("terms")."` AS `settings_category` ON settings_category.id=settings.category WHERE `settings_category`.term = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("General", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion_terms() {
		$query = $this->db->query("users");
		$query->condition("users.groups", "user", "!=");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE :default0 NOT IN (SELECT users_groups.slug FROM ".$this->db->prefix("users_groups")." users_groups_lookup INNER JOIN ".$this->db->prefix("terms")." users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id)";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in condition fields and specify the comparator field explicitly
	 */
	function test_condition_expansion_terms_explicit_comparator() {
		$query = $this->db->query("users");
		$query->condition("users.groups.term", "User", '!=');

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE :default0 NOT IN (SELECT users_groups.term FROM ".$this->db->prefix("users_groups")." users_groups_lookup INNER JOIN ".$this->db->prefix("terms")." users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id)";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("User", $query->parameters[":default0"]);
	}

	/**
	 * Test expansions in where clauses
	 */
	function test_where_expansion_terms() {
		$query = $this->db->query("users");
		$query->where(":group NOT IN users.groups")->param("group", "user");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE :group NOT IN (SELECT users_groups.slug FROM ".$this->db->prefix("users_groups")." users_groups_lookup INNER JOIN ".$this->db->prefix("terms")." users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id)";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $query->parameters[":group"]);
	}

	/**
	 * Test grouping
	 */
	function test_grouping() {
		$query = $this->db->query("users");
		$query->select("COUNT(*) as count")->group("MONTH(created)");

		//expected output
		$expected = "SELECT COUNT(*) as count FROM `".$this->db->prefix("users")."` AS `users` GROUP BY MONTH(created)";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test expansions in GROUP BY clauses
	 */
	function test_grouping_expansion() {
		$query = $this->db->query("settings");
		$query->select("COUNT(*) as count")->group("owner.first_name");

		//expected output
		$expected = "SELECT COUNT(*) as count FROM `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `settings_owner` ON settings_owner.id=settings.owner GROUP BY `settings_owner`.first_name";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test expansions in GROUP BY clauses
	 */
	function test_grouping_expansion_terms() {
		$query = $this->db->query("users");
		$query->select("COUNT(*) as count")->group("users.groups");

		//expected output
		$expected = "SELECT COUNT(*) as count FROM `".$this->db->prefix("users")."` AS `users` LEFT JOIN `".$this->db->prefix("users_groups")."` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `".$this->db->prefix("terms")."` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id GROUP BY `users_groups`.slug";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test having
	 */
	function test_having_condition() {
		$query = $this->db->query("terms");
		$query->group("terms.taxonomy")->select("terms.taxonomy,COUNT(*) as count")->havingCondition("count", "0", ">");

		//expected output
		$expected = "SELECT `terms`.taxonomy,COUNT(*) as count FROM `".$this->db->prefix("terms")."` AS `terms` GROUP BY `terms`.taxonomy HAVING count > :having0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("0", $query->parameters[":having0"]);
	}

	/**
	 * Test having
	 */
	function test_sorting() {
		$query = $this->db->query("terms");
		$query->sort("taxonomy")->sort("slug", 1)->sort("created", -1);

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` ORDER BY taxonomy, slug ASC, created DESC";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test limit
	 */
	function test_limit() {
		$query = $this->db->query("terms");
		$query->limit(5)->skip(10);

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` LIMIT 10, 5";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_or_condition() {
		$query = $this->db->query("terms");
		$query->condition("taxonomy", "groups")->orCondition("taxonomy", "statuses");

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` WHERE taxonomy = :default0 || taxonomy = :default1";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $query->parameters[":default0"]);
		$this->assertSame("statuses", $query->parameters[":default1"]);
	}

	function test_or_where() {
		$query = $this->db->query("terms");
		$query->where("taxonomy = :tax1")->orWhere("taxonomy = :tax2")->params(array("tax1" => "groups", "tax2" => "statuses"));

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` WHERE taxonomy = :tax1 || taxonomy = :tax2";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $query->parameters[":tax1"]);
		$this->assertSame("statuses", $query->parameters[":tax2"]);
	}

	function test_or_where_shorter() {
		$query = $this->db->query("terms");
		$query->where("taxonomy = :tax1 || taxonomy = :tax2")->params(array("tax1" => "groups", "tax2" => "statuses"));

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` WHERE taxonomy = :tax1 || taxonomy = :tax2";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $query->parameters[":tax1"]);
		$this->assertSame("statuses", $query->parameters[":tax2"]);
	}

	function test_or_condition_set() {
		$query = $this->db->query("terms");

		$query->condition("taxonomy", "groups");

		//recent is an arbritrary set name
		$query->open("recent", "||");
		$query->condition("created", date("Y-m-d")." 00:00:00", ">=")->andCondition("taxonomy", "statuses");

		//expected output
		$expected = "SELECT `terms`.* FROM `".$this->db->prefix("terms")."` AS `terms` WHERE taxonomy = :default0 || (created >= :recent0 && taxonomy = :recent1)";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $query->parameters[":default0"]);
		$this->assertSame(date("Y-m-d")." 00:00:00", $query->parameters[":recent0"]);
		$this->assertSame("statuses", $query->parameters[":recent1"]);
	}

	function test_search_and() {
		$query = $this->db->query("users");
		$query->search("ali gangji", "first_name,last_name");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE ((first_name LIKE '%ali%' OR last_name LIKE '%ali%') AND (first_name LIKE '%gangji%' OR last_name LIKE '%gangji%'))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_search_or() {
		$query = $this->db->query("users");
		$query->search("ali or gangji", "first_name,last_name");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE ((first_name LIKE '%ali%' OR last_name LIKE '%ali%') or (first_name LIKE '%gangji%' OR last_name LIKE '%gangji%'))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_search_fields() {
		$query = $this->db->query("users");
		$query->search("ali", "first_name");
		$query->search("gangji", "last_name");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE ((first_name LIKE '%ali%')) && ((last_name LIKE '%gangji%'))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_multivalue_term_expansion() {
		$query = $this->db->query("users");
		$query->condition("users.groups", array("user", "admin"));
		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE (:default0 IN (SELECT users_groups.slug FROM ".$this->db->prefix("users_groups")." users_groups_lookup INNER JOIN ".$this->db->prefix("terms")." users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id) || :default1 IN (SELECT users_groups.slug FROM ".$this->db->prefix("users_groups")." users_groups_lookup INNER JOIN ".$this->db->prefix("terms")." users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $query->parameters[":default0"]);
		$this->assertSame("admin", $query->parameters[":default1"]);
	}

	function test_action() {
		$query = $this->db->query("users");
		$this->user->setUser(array("id" => 2));
		$query->action("read");

		//expected output
		$expected = "SELECT `users`.* ".
								"FROM `".$this->db->prefix("users")."` AS `users` ".
								"INNER JOIN `".$this->db->prefix("permits")."` AS `permits` ON 'users' LIKE permits.related_table && 'read' LIKE permits.action ".
								"WHERE ".
									"('global' LIKE `permits`.priv_type || (`permits`.priv_type='object' && `permits`.related_id=`users`.id)) && ".
									"(`permits`.object_deleted is null || `permits`.object_deleted=`users`.deleted) && ".
									"(`permits`.user_groups is null || `permits`.user_groups IN (SELECT groups_id FROM ".$this->db->prefix("users_groups")." u WHERE `u`.users_id=2)) && ".
									"(`permits`.role='everyone' || `permits`.role='user' && `permits`.who='2' || `permits`.role='self' && `users`.id='2' || `permits`.role='owner' && `users`.owner='2' || `permits`.role='groups' && (EXISTS (SELECT groups_id FROM ".$this->db->prefix("users_groups")." o WHERE `o`.users_id=`users`.id && `o`.groups_id IN (SELECT groups_id FROM ".$this->db->prefix("users_groups")." u WHERE `u`.users_id=2)) || NOT EXISTS (SELECT groups_id FROM ".$this->db->prefix("users_groups")." o WHERE `o`.users_id=`users`.id)))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_remove() {
		//the delete method is normally an execution method
		//passing false prevents the query from actually running
		$query = $this->db->query("users");
		$query->condition("email", "phpunit")->delete(false);

		//expected output
		$expected = "DELETE `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE email = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("phpunit", $query->parameters[":default0"]);
	}

	function test_insert() {
		//the insert method is normally an execution method
		//passing false prevents the query from actually running
		$query = $this->db->query("users");
		$query->set("first_name", "PHPUnit")->insert(false);

		//expected output (actual output contains extra fields due to validation)
		$expected = "INSERT INTO `".$this->db->prefix("users")."` SET `first_name` = :set0";

		//compare
		$actual = reset(explode(",", $query->build()));
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $query->parameters[":set0"]);
	}

	function test_update() {
		//the update method is normally an execution method
		//passing false prevents the query from actually running
		$query = $this->db->query("users");
		$query->set("first_name", "PHPUnit");
		$query->condition("email", "phpunit");
		$query->update(false);

		//expected output
		$expected = "UPDATE `".$this->db->prefix("users")."` AS `users` SET `first_name` = :set0, `modified` = :set1 WHERE email = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $query->parameters[":set0"]);
		$this->assertSame("phpunit", $query->parameters[":default0"]);
	}

	function test_update_condition_expansion() {
		//the update method is normally an execution method
		//passing false prevents the query from actually running
		$query = $this->db->query("settings");
		$query->set("settings.value", "PHPUnit");
		$query->condition("owner.first_name", "phpunit");
		$query->update(false);

		//expected output
		$expected = "UPDATE `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `settings_owner` ON settings_owner.id=settings.owner SET `settings`.`value` = :set0, `modified` = :set1 WHERE `settings_owner`.first_name = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $query->parameters[":set0"]);
		$this->assertSame("phpunit", $query->parameters[":default0"]);
	}

	function test_update_set_expansion() {
		//the update method is normally an execution method
		//passing false prevents the query from actually running
		$query = $this->db->query("settings");
		$query->set("owner.first_name", "PHPUnit");
		$query->condition("settings.value", "phpunit");
		$query->update(false);

		//expected output
		$expected = "UPDATE `".$this->db->prefix("settings")."` AS `settings` LEFT JOIN `".$this->db->prefix("users")."` AS `settings_owner` ON settings_owner.id=settings.owner SET `settings_owner`.`first_name` = :set0, `modified` = :set1 WHERE `settings`.value = :default0";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $query->parameters[":set0"]);
		$this->assertSame("phpunit", $query->parameters[":default0"]);
	}
}
