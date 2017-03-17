<?php
namespace Starbug\Db\Tests;
use Starbug\Db\Query\Query;
use Starbug\Db\Query\Builder;
use Starbug\Db\Query\Compiler;
use PHPUnit_Framework_TestCase;
class QueryBuilderTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->compiler = new Compiler("test_");
	}

	function createQuery() {
		$this->builder = new Builder(new Query());
		return $this->builder;
	}

	function compile(Builder $builder = null) {
		if (is_null($builder)) $builder = $this->builder;
		return $this->compiler->build($builder->getQuery());
	}

	/**
	 * Test table aliases
	 */
	function testAlias() {
		$this->createQuery()->from("users as people");

		//expected output
		$expected = "SELECT `people`.* FROM `test_users` AS `people`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testJoin() {
		$this->createQuery()->from("settings")->join("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testInnerJoin() {
		$this->createQuery()->from("settings")->innerJoin("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` INNER JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testLeftJoin() {
		$this->createQuery()->from("settings")->leftJoin("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` LEFT JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testRightJoin() {
		$this->createQuery()->from("settings")->rightJoin("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` RIGHT JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test ON clauses in query models
	 */
	function testJoinCondition() {
		$this->createQuery()->from("settings")->innerJoin("users")->on("settings.id=users.id");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` INNER JOIN `test_users` AS `users` ON settings.id=users.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}


	/**
	 * Test aliases in query models
	 */
	function testJoinAliases() {
		$this->createQuery()->from("settings as s")->innerJoin("users as u");

		//expected output
		$expected = "SELECT `s`.* FROM `test_settings` AS `s` INNER JOIN `test_users` AS `u`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test ON clauses and aliases together in query models
	 */
	function testJoinOnAndAliases() {
		$this->createQuery()->from("settings as s")->innerJoin("users as u")->on("s.id=u.id");

		//expected output
		$expected = "SELECT `s`.* FROM `test_settings` AS `s` INNER JOIN `test_users` AS `u` ON s.id=u.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test single reference relationship (RelationshipType::ONE)
	 */
	function testJoinOne() {
		$this->createQuery()->from("pages")->joinOne("pages.owner", "users");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` JOIN `test_users` AS `users` ON users.id=pages.owner";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test select
	 */
	function testSelect() {
		$this->createQuery()->from("users")->select("CONCAT(id, ' ', email) as label");

		// the code above should produce the query below
		$expected = "SELECT CONCAT(id, ' ', email) AS label FROM `test_users` AS `users`";

		//compare queries
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test expansions in select clauses made by using the dot syntax on reference fields.
	 * For example, settings.owner references users.id, and therefore you can select fields of the references row using the syntax:
	 * SELECT settings.owner.first_name
	 */
	function testSelectExpansion() {
		$this->createQuery()->from("settings")->select("CONCAT(owner.first_name, ' ', owner.last_name) as name");

		// the code above should produce the query below
		$expected = "SELECT CONCAT(`settings_owner`.first_name, ' ', `settings_owner`.last_name) AS name ".
			"FROM `test_settings` AS `settings` LEFT JOIN `test_users` AS `settings_owner` ON settings_owner.id=settings.owner";

		//compare queries
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test where clauses
	 */
	function testWhere() {
		$this->createQuery()->from("users")->where("users.email LIKE '%email%'");

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE users.email LIKE '%email%'";
		//$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE '%email%'";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test parameterized conditions
	 */
	function testCondition() {
		$this->createQuery()->from("users")->condition("users.email", "%email%", "LIKE");

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE users.email LIKE :default0";
		//$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("%email%", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function test_condition_expansion() {
		return;
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
		return;
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
		return;
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
		return;
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
		return;
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
		return;
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
		return;
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
		return;
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
		return;
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
	function testGrouping() {
		$this->createQuery()->from("users")->select("COUNT(*) as count")->group("MONTH(created)");

		//expected output
		$expected = "SELECT COUNT(*) AS count FROM `test_users` AS `users` GROUP BY MONTH(created)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test expansions in GROUP BY clauses
	 */
	function test_grouping_expansion() {
		return;
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
		return;
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
	function testHavingCondition() {
		$this->createQuery()->from("terms")
			->select(["terms.taxonomy", "COUNT(*) as count"])
			->group("terms.taxonomy")
			->havingCondition("count", "0", ">");

		//expected output
		$expected = "SELECT terms.taxonomy, COUNT(*) AS count FROM `test_terms` AS `terms` GROUP BY terms.taxonomy HAVING count > :default0";
		//$expected = "SELECT `terms`.taxonomy,COUNT(*) as count FROM `test_terms` AS `terms` GROUP BY `terms`.taxonomy HAVING count > :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("0", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test having
	 */
	function testSorting() {
		$this->createQuery()->from("terms")
			->sort("taxonomy")
			->sort("slug", 1)
			->sort("created", -1);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` ORDER BY taxonomy, slug ASC, created DESC";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test limit
	 */
	function testLimit() {
		$this->createQuery()->from("terms")->limit(5)->skip(10);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` LIMIT 10, 5";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testOrCondition() {
		$query = $this->createQuery()->from("terms");
		$conditions = $query->createOrCondition();
		$conditions->condition("taxonomy", "groups");
		$conditions->condition("taxonomy", "statuses");
		$query->condition($conditions);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :default0 OR taxonomy = :default1)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->builder->getQuery()->getParameter("default0"));
		$this->assertSame("statuses", $this->builder->getQuery()->getParameter("default1"));
	}

	function testOrWhere() {
		$query = $this->createQuery()->from("terms");
		$conditions = $query->createOrCondition()
			->where("taxonomy = :tax1")
			->where("taxonomy = :tax2");
		$query->condition($conditions)->bind(["tax1" => "groups", "tax2" => "statuses"]);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :tax1 OR taxonomy = :tax2)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->builder->getQuery()->getParameter("tax1"));
		$this->assertSame("statuses", $this->builder->getQuery()->getParameter("tax2"));
	}

	function testOrWhereShorter() {
		$this->createQuery()->from("terms")
			->where("taxonomy = :tax1 OR taxonomy = :tax2")
			->bind(["tax1" => "groups", "tax2" => "statuses"]);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE taxonomy = :tax1 OR taxonomy = :tax2";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->builder->getQuery()->getParameter("tax1"));
		$this->assertSame("statuses", $this->builder->getQuery()->getParameter("tax2"));
	}

	function testOrConditionSet() {
		$query = $this->createQuery()->from("terms");
		//This is the clause we want to create:
		//WHERE (taxonomy = 'groups' OR (created >= '00:00:00' AND taxonomy = 'statuses'))

		//more generically, this is the structure we're looking for:
		// WHERE (A OR (B AND C))

		//For illustrative purposes we'll create the nested condition first
		//the nested condition is an AND between B and C
		//$recent represents (B AND C)
		$recent = $query->createCondition(); //create AND condition
		$recent->condition("created", date("Y-m-d")." 00:00:00", ">="); //add B
		$recent->condition("taxonomy", "statuses"); //add C

		//Now we create the outer condition which is an OR between A and (B AND C)
		$conditions = $query->createOrCondition(); //create OR condition
		$conditions->condition("taxonomy", "groups"); //add A
		$conditions->condition($recent); //add (B AND C)
		
		$query->condition($conditions);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :default0 OR (created >= :default1 AND taxonomy = :default2))";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->builder->getQuery()->getParameter("default0"));
		$this->assertSame(date("Y-m-d")." 00:00:00", $this->builder->getQuery()->getParameter("default1"));
		$this->assertSame("statuses", $this->builder->getQuery()->getParameter("default2"));
	}

	function test_search_and() {
		return;
		$query = $this->db->query("users");
		$query->search("ali gangji", "first_name,last_name");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE ((first_name LIKE '%ali%' OR last_name LIKE '%ali%') AND (first_name LIKE '%gangji%' OR last_name LIKE '%gangji%'))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_search_or() {
		return;
		$query = $this->db->query("users");
		$query->search("ali or gangji", "first_name,last_name");

		//expected output
		$expected = "SELECT `users`.* FROM `".$this->db->prefix("users")."` AS `users` WHERE ((first_name LIKE '%ali%' OR last_name LIKE '%ali%') or (first_name LIKE '%gangji%' OR last_name LIKE '%gangji%'))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
	}

	function test_search_fields() {
		return;
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
		return;
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
		return;
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

	function testRemove() {
		$this->createQuery()->from("users")->condition("email", "phpunit")->mode("delete");

		//expected output
		$expected = "DELETE `users`.* FROM `test_users` AS `users` WHERE email = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
	}

	function testInsert() {
		$this->createQuery()->from("users")->set("first_name", "PHPUnit")->mode("insert");

		//expected output
		$expected = "INSERT INTO `test_users` SET `first_name` = :set0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
	}

	function testUpdate() {
		$this->createQuery()->from("users")->condition("email", "phpunit")->set("first_name", "PHPUnit")->mode("update");

		//expected output
		$expected = "UPDATE `test_users` AS `users` SET `first_name` = :set0 WHERE email = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
		$this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
	}

	function test_update_condition_expansion() {
		return;
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
		return;
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
