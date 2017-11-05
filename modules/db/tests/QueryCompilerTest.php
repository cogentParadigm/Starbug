<?php
namespace Starbug\Db\Tests;
use Starbug\Db\Query\Query;
use Starbug\Db\Query\Compiler;
use PHPUnit_Framework_TestCase;
class QueryCompilerTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->query = $this->createQuery();
		$this->compiler = $this->createCompiler();
	}

	function compile(Query $query = null) {
		if (is_null($query)) $query = $this->query;
		return $this->compiler->build($query)->getSql();
	}

	function createQuery() {
		return new Query();
	}

	function createCompiler() {
		return new Compiler(new MockDatabase());
	}

	/**
	 * Test table aliases
	 */
	function testAlias() {
		$this->query->setTable("users", "people");

		//expected output
		$expected = "SELECT `people`.* FROM `test_users` AS `people`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testJoin() {
		$this->query->setTable("settings");
		$this->query->addJoin("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testInnerJoin() {
		$this->query->setTable("settings");
		$this->query->addInnerJoin("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` INNER JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testLeftJoin() {
		$this->query->setTable("settings");
		$this->query->addLeftJoin("users");

		//expected output
		$expected = "SELECT `settings`.* FROM `test_settings` AS `settings` LEFT JOIN `test_users` AS `users`";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testRightJoin() {
		$this->query->setTable("settings");
		$this->query->addRightJoin("users");

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
		$this->query->setTable("settings");
		$this->query->addInnerJoin("users")->where("settings.id=users.id");

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
		$this->query->setTable("settings", "s");
		$this->query->addInnerJoin("users", "u");

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
		$this->query->setTable("settings", "s");
		$this->query->addInnerJoin("users", "u")->where("s.id=u.id");

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
		$this->query->setTable("pages");
		$this->query->addJoinOne("pages.owner", "users");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `users` ON users.id=pages.owner";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test single reference relationship (RelationshipType::ONE)
	 */
	function testJoinOneAlias() {
		$this->query->setTable("pages");
		$this->query->addJoinOne("pages.owner", "users", "pages_owner");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test each to many relationship (RelationshipType::MANY)
	 */
	function testJoinMany() {
		$this->query->setTable("pages");
		$this->query->addJoinMany("pages", "comments");

		//expected output
		$expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_comments` AS `comments` ON comments.pages_id=pages.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test each to many relationship (RelationshipType::MANY) with aliases
	 */
	function testJoinManyAlias() {
		$this->query->setTable("pages", "p");
		$this->query->addJoinMany("p", "comments", "c");

		//expected output
		$expected = "SELECT `p`.* FROM `test_pages` AS `p` LEFT JOIN `test_comments` AS `c` ON c.pages_id=p.id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test each to many relationship (RelationshipType::MANY) followed by each to one relationship (RelationshipType::MANY)
	 * This describes a common join table scenario. users -(many)> users_groups -(one)> groups
	 * The relationship type describes the direction of the individual join
	 */
	function testJoinManyOne() {
		$this->query->setTable("users");
		$this->query->addJoinMany("users", "users_groups");
		$this->query->addJoinOne("users_groups.groups_id", "groups");

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups` ON users_groups.users_id=users.id ".
								"LEFT JOIN `test_groups` AS `groups` ON groups.id=users_groups.groups_id";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test select
	 */
	function testSelect() {
		$this->query->setTable("users");
		$this->query->addSelection("CONCAT(id, ' ', email)", "label");

		// the code above should produce the query below
		$expected = "SELECT CONCAT(id, ' ', email) AS label FROM `test_users` AS `users`";

		//compare queries
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test select subquery
	 */
	function testSelectSubquery() {
		$children = $this->createQuery();
		$children->setTable("pages", "child");
		$children->addSelection("COUNT(*)");
		$children->addWhere("child.parent=pages.id");

		$this->query->setTable("pages");
		$this->query->addSelection($children, "children");

		//expected output
		$expected = "SELECT (SELECT COUNT(*) FROM `test_pages` AS `child` WHERE child.parent=pages.id) AS children FROM `test_pages` AS `pages`";

		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test select subquery
	 */
	function testSelectSubqueryShorter() {
		$this->query->setTable("pages");
		$children = $this->query->addSubquery("COUNT(*)", "children");
		$children->setTable("pages", "child");
		$children->addWhere("child.parent=pages.id");

		//expected output
		$expected = "SELECT (SELECT COUNT(*) FROM `test_pages` AS `child` WHERE child.parent=pages.id) AS children FROM `test_pages` AS `pages`";

		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test where clauses
	 */
	function testWhere() {
		$this->query->setTable("users");
		$this->query->addWhere("users.email LIKE '%email%'");

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
		$this->query->setTable("users");
		$this->query->addCondition("users.email", "%email%", "LIKE");

		//expected output
		$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE users.email LIKE :default0";
		//$expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("%email%", $this->query->getParameter("default0"));
	}

	/**
	 * Test grouping
	 */
	function testGrouping() {
		$this->query->setTable("users");
		$this->query->addSelection("COUNT(*)", "count");
		$this->query->addGroup("MONTH(created)");

		//expected output
		$expected = "SELECT COUNT(*) AS count FROM `test_users` AS `users` GROUP BY MONTH(created)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	/**
	 * Test having
	 */
	function testHavingCondition() {
		$this->query->setTable("terms");
		$this->query->addSelection("terms.taxonomy");
		$this->query->addSelection("COUNT(*)", "count");
		$this->query->addGroup("terms.taxonomy");
		$this->query->addHavingCondition("count", "0", ">");

		//expected output
		$expected = "SELECT terms.taxonomy, COUNT(*) AS count FROM `test_terms` AS `terms` GROUP BY terms.taxonomy HAVING count > :default0";
		//$expected = "SELECT `terms`.taxonomy,COUNT(*) as count FROM `test_terms` AS `terms` GROUP BY `terms`.taxonomy HAVING count > :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("0", $this->query->getParameter("default0"));
	}

	/**
	 * Test having
	 */
	function testSorting() {
		$this->query->setTable("terms");
		$this->query->addSort("taxonomy");
		$this->query->addSort("slug", 1);
		$this->query->addSort("created", -1);

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
		$this->query->setTable("terms");
		$this->query->setLimit(5);
		$this->query->setSkip(10);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` LIMIT 10, 5";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
	}

	function testOrCondition() {
		$this->query->setTable("terms");
		$condition = $this->query->createOrCondition();
		$condition->condition("taxonomy", "groups");
		$condition->condition("taxonomy", "statuses");
		$this->query->addCondition($condition);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :default0 OR taxonomy = :default1)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->query->getParameter("default0"));
		$this->assertSame("statuses", $this->query->getParameter("default1"));
	}

	function testOrWhere() {
		$this->query->setTable("terms");
		$condition = $this->query->createOrCondition();
		$condition->where("taxonomy = :tax1");
		$condition->where("taxonomy = :tax2");
		$this->query->setParameter("tax1", "groups");
		$this->query->setParameter("tax2", "statuses");
		$this->query->addCondition($condition);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :tax1 OR taxonomy = :tax2)";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->query->getParameter("tax1"));
		$this->assertSame("statuses", $this->query->getParameter("tax2"));
	}

	function testOrWhereShorter() {
		$this->query->setTable("terms");
		$this->query->addWhere("taxonomy = :tax1 OR taxonomy = :tax2");
		$this->query->setParameter("tax1", "groups");
		$this->query->setParameter("tax2", "statuses");

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE taxonomy = :tax1 OR taxonomy = :tax2";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->query->getParameter("tax1"));
		$this->assertSame("statuses", $this->query->getParameter("tax2"));
	}

	function testOrConditionSet() {
		//This is the clause we want to create:
		//WHERE (taxonomy = 'groups' OR (created >= '00:00:00' AND taxonomy = 'statuses'))

		//more generically, this is the structure we're looking for:
		// WHERE (A OR (B AND C))

		//For illustrative purposes we'll create the nested condition first
		//the nested condition is an AND between B and C
		//$recent represents (B AND C)
		$recent = $this->query->createCondition(); //create AND condition
		$recent->condition("created", date("Y-m-d")." 00:00:00", ">="); //add B
		$recent->condition("taxonomy", "statuses"); //add C

		//Now we create the outer condition which is an OR between A and (B AND C)
		$condition = $this->query->createOrCondition(); //create OR condition
		$condition->condition("taxonomy", "groups"); //add A
		$condition->condition($recent); //add (B AND C)

		//
		$this->query->setTable("terms");

		$this->query->addCondition($condition);

		//expected output
		$expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :default0 OR (created >= :default1 AND taxonomy = :default2))";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("groups", $this->query->getParameter("default0"));
		$this->assertSame(date("Y-m-d")." 00:00:00", $this->query->getParameter("default1"));
		$this->assertSame("statuses", $this->query->getParameter("default2"));
	}

	function test_remove() {
		$this->query->setTable("users");
		$this->query->addCondition("email", "phpunit");
		$this->query->setMode("delete");

		//expected output
		$expected = "DELETE `users`.* FROM `test_users` AS `users` WHERE email = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("phpunit", $this->query->getParameter("default0"));
	}

	function testInsert() {
		$this->query->setTable("users");
		$this->query->setValue("first_name", "PHPUnit");
		$this->query->setMode("insert");

		//expected output
		$expected = "INSERT INTO `test_users` SET `first_name` = :set0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $this->query->getParameter("set0"));
	}

	function testUpdate() {
		$this->query->setTable("users");
		$this->query->addCondition("email", "phpunit");
		$this->query->setValue("first_name", "PHPUnit");
		$this->query->setMode("update");

		//expected output
		$expected = "UPDATE `test_users` AS `users` SET `first_name` = :set0 WHERE email = :default0";

		//compare
		$actual = $this->compile();
		$this->assertSame($expected, $actual);
		$this->assertSame("PHPUnit", $this->query->getParameter("set0"));
		$this->assertSame("phpunit", $this->query->getParameter("default0"));
	}
}
