<?php
namespace Starbug\Db\Tests;
use Starbug\Db\Query\Extensions\Search;
class QueryBuilderTest extends QueryBuilderTestBase {

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
		$this->createQuery()->from("pages")->join("users");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testInnerJoin() {
		$this->createQuery()->from("pages")->innerJoin("users");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` INNER JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testLeftJoin() {
		$this->createQuery()->from("pages")->leftJoin("users");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testRightJoin() {
		$this->createQuery()->from("pages")->rightJoin("users");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` RIGHT JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test ON clauses in query models
	 */
	function testJoinCondition() {
		$this->createQuery()->from("pages")->innerJoin("users")->on("pages.id=users.id");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` INNER JOIN `test_users` AS `users` ON pages.id=users.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}


	/**
	 * Test aliases in query models
	 */
	function testJoinAliases() {
		$this->createQuery()->from("pages as p")->innerJoin("users as u");

		//expected output
		$expected = "SELECT `p`.* FROM `test_pages` AS `p` INNER JOIN `test_users` AS `u`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test ON clauses and aliases together in query models
	 */
	function testJoinOnAndAliases() {
		$this->createQuery()->from("pages as p")->innerJoin("users as u")->on("s.id=u.id");

		//expected output
		$expected = "SELECT `p`.* FROM `test_pages` AS `p` INNER JOIN `test_users` AS `u` ON s.id=u.id";

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
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `users` ON users.id=pages.owner";

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
	 * For example, pages.owner references users.id, and therefore you can select fields of the references row using the syntax:
	 * SELECT pages.owner.first_name
	 */
	function testSelectExpansion() {
		$this->createQuery()->from("pages")->select("CONCAT(owner.first_name, ' ', owner.last_name) as name");

		// the code above should produce the query below
		$expected = "SELECT CONCAT(`pages_owner`.first_name, ' ', `pages_owner`.last_name) AS name ".
			"FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner";

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
		$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE '%email%'";

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
		$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("%email%", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function testConditionExpansion() {
		$this->createQuery()->from("pages")->condition("pages.owner.email", "root");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner WHERE `pages_owner`.email = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("root", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function testConditionExpansionSelect() {
		$this->createQuery()->from("pages")->select("pages.owner.email")->condition("pages.owner.email", "root");

		//expected output
		$expected = "SELECT `pages_owner`.email FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner WHERE `pages_owner`.email = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("root", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function testConditionExpansionMany() {
		$this->createQuery()->from("pages")->subquery("images.mime_type", function($sub, $query) {
			$query->condition($sub, "image/png");
		});

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` WHERE :default0 IN (".
			"SELECT `files`.mime_type FROM `test_pages_images` AS `pages_images` ".
			"LEFT JOIN `test_files` AS `files` ON files.id=pages_images.images_id ".
			"WHERE `pages_images`.pages_id=`pages`.id)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("image/png", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function testConditionExpansionManyJoin() {
		$this->createQuery()->from("pages")->condition("images.mime_type", "image/%", "LIKE")->group("pages.id");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_pages_images` AS `pages_images_lookup` ON pages_images_lookup.pages_id=pages.id LEFT JOIN `test_files` AS `pages_images` ON pages_images.id=pages_images_lookup.images_id WHERE `pages_images`.mime_type LIKE :default0 GROUP BY `pages`.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("image/%", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function testConditionExpansionCategory() {
		$this->createQuery()->from("pages")->condition("pages.category.slug", "general");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_terms` AS `pages_category` ON pages_category.id=pages.category WHERE `pages_category`.slug = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("general", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields and specify the comparator field explicitly
	 */
	function testConditionExpansionTermsJoin() {
		$this->createQuery()->from("users")->condition("users.groups.slug", "user", "!=")->group("users.id");

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id WHERE `users_groups`.slug != :default0 GROUP BY `users`.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in condition fields
	 */
	function testConditionExpansionTermsSubquery() {
		$this->createQuery()->from("users")->subquery("users.groups.slug", function($sub, $query) {
			$query->condition($sub, "user", "!=");
		});

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE :default0 NOT IN (SELECT `terms`.slug FROM `test_users_groups` AS `users_groups` LEFT JOIN `test_terms` AS `terms` ON terms.id=users_groups.groups_id WHERE `users_groups`.users_id=`users`.id)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $this->builder->getQuery()->getParameter("default0"));
	}

	/**
	 * Test expansions in where clauses
	 */
	function testWhereExpansionTermsJoin() {
		$this->createQuery()->from("users")->where("users.groups.slug != :group")->group("users.id")->bind("group", "user");

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id WHERE `users_groups`.slug != :group GROUP BY `users`.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $this->builder->getQuery()->getParameter("group"));
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
	function testGroupingExpansion() {
		$this->createQuery()->from("pages")->select("COUNT(*) as count")->group("owner.first_name");

		//expected output
		$expected = "SELECT COUNT(*) AS count FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner GROUP BY `pages_owner`.first_name";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test expansions in GROUP BY clauses
	 */
	function testGroupingExpansionTerms() {
		$this->createQuery()->from("users")->select("COUNT(*) as count")->group("users.groups.slug");

		//expected output
		$expected = "SELECT COUNT(*) AS count FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id GROUP BY `users_groups`.slug";

		//compare
		$actual = $this->compile();
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
		$expected = "SELECT `terms`.taxonomy, COUNT(*) AS count FROM `test_terms` AS `terms` GROUP BY `terms`.taxonomy HAVING count > :default0";

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

	function testMultiValueTermExpansion() {
		$this->createQuery()->from("users")->condition("users.groups.slug", ["user", "admin"]);

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id WHERE `users_groups`.slug IN (:default0, :default1)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $this->builder->getQuery()->getParameter("default0"));
		$this->assertSame("admin", $this->builder->getQuery()->getParameter("default1"));
	}

	function testMultiValueTermExpansionSubquery() {
		return;
		$query = $this->db->query("users");
		$query->condition("users.groups", array("user", "admin"));
		//expected output
		$expected = "SELECT `users`.* FROM `users` AS `users` WHERE (:default0 IN (SELECT users_groups.slug FROM `users_groups` users_groups_lookup INNER JOIN `terms` users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id) || :default1 IN (SELECT users_groups.slug FROM `users_groups` users_groups_lookup INNER JOIN `terms` users_groups ON users_groups.id=users_groups_lookup.groups_id WHERE users_groups_lookup.users_id=users.id))";

		//compare
		$actual = $query->build();
		$this->assertSame($expected, $actual);
		$this->assertSame("user", $query->parameters[":default0"]);
		$this->assertSame("admin", $query->parameters[":default1"]);
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

	function testUpdateConditionExpansion() {
		$this->createQuery()->from("pages")->set("pages.content", "PHPUnit")->condition("owner.first_name", "phpunit")->mode("update");

		//expected output
		$expected = "UPDATE `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner SET `pages`.`content` = :set0 WHERE `pages_owner`.first_name = :default0";
		//$expected = "UPDATE `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner SET `pages`.`content` = :set0, `modified` = :set1 WHERE `pages_owner`.first_name = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
		$this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
	}

	function test_update_set_expansion() {
		$this->createQuery()->from("pages")->set("owner.first_name", "PHPUnit")->condition("pages.content", "phpunit")->mode("update");

		//expected output
		$expected = "UPDATE `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner SET `pages_owner`.`first_name` = :set0 WHERE `pages`.content = :default0";
		//$expected = "UPDATE `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner SET `pages_owner`.`first_name` = :set0, `modified` = :set1 WHERE `pages`.content = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
		$this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
	}
}
