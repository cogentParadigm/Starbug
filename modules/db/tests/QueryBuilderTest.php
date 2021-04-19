<?php
namespace Starbug\Db\Tests;

use Starbug\Db\Query\Extensions\Search;

class QueryBuilderTest extends QueryBuilderTestBase {

  /**
   * Test table aliases.
   *
   * @return void
   */
  public function testAlias() {
    $this->createQuery()->from("users as people");

    // Expected output.
    $expected = "SELECT `people`.* FROM `test_users` AS `people`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Demonstrate joining from one table to another.
   *
   * @return void
   */
  public function testJoin() {
    $this->createQuery()->from("pages")->join("users");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` JOIN `test_users` AS `users`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Demonstrate the same join with an explicit inner join.
   *
   * @return void
   */
  public function testInnerJoin() {
    $this->createQuery()->from("pages")->innerJoin("users");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` INNER JOIN `test_users` AS `users`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Same again with a left.
   *
   * @return void
   */
  public function testLeftJoin() {
    $this->createQuery()->from("pages")->leftJoin("users");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `users`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * The same join again with a right join.
   *
   * @return void
   */
  public function testRightJoin() {
    $this->createQuery()->from("pages")->rightJoin("users");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` RIGHT JOIN `test_users` AS `users`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * An inner join, followed by an explicit ON condition for the join.
   *
   * @return void
   */
  public function testJoinCondition() {
    $this->createQuery()->from("pages")->innerJoin("users")->on("pages.id=users.id");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` INNER JOIN `test_users` AS `users` ON pages.id=users.id";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }


  /**
   * Table aliases for base table and joined table.
   *
   * @return void
   */
  public function testJoinAliases() {
    $this->createQuery()->from("pages as p")->innerJoin("users as u");

    // Expected output.
    $expected = "SELECT `p`.* FROM `test_pages` AS `p` INNER JOIN `test_users` AS `u`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test ON clauses and aliases together in query models
   *
   * @return void
   */
  public function testJoinOnAndAliases() {
    $this->createQuery()->from("pages as p")->innerJoin("users as u")->on("s.id=u.id");

    // Expected output.
    $expected = "SELECT `p`.* FROM `test_pages` AS `p` INNER JOIN `test_users` AS `u` ON s.id=u.id";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test single reference relationship (RelationshipType::ONE)
   *
   * @return void
   */
  public function testJoinOne() {
    $this->createQuery()->from("pages")->joinOne("pages.owner", "users");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `users` ON users.id=pages.owner";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test selection clause.
   *
   * @return void
   */
  public function testSelect() {
    $this->createQuery()->from("users")->select("CONCAT(id, ' ', email) as label");

    // The code above should produce the query below.
    $expected = "SELECT CONCAT(id, ' ', email) AS label FROM `test_users` AS `users`";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test expansions in select clauses made by using the dot syntax on reference fields.
   * For example, pages.owner references users.id, and therefore you can select fields of the references row using the syntax:
   * SELECT pages.owner.first_name
   *
   * @return void
   */
  public function testSelectExpansion() {
    $this->createQuery()->from("pages")->select("CONCAT(owner.first_name, ' ', owner.last_name) as name");

    // The code above should produce the query below.
    $expected = "SELECT CONCAT(`pages_owner`.first_name, ' ', `pages_owner`.last_name) AS name ".
      "FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test where clauses.
   *
   * @return void
   */
  public function testWhere() {
    $this->createQuery()->from("users")->where("users.email LIKE '%email%'");

    // Expected output.
    $expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE '%email%'";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test parameterized conditions.
   *
   * @return void
   */
  public function testCondition() {
    $this->createQuery()->from("users")->condition("users.email", "%email%", "LIKE");

    // Expected output.
    $expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE `users`.email LIKE :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("%email%", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields.
   *
   * @return void
   */
  public function testConditionExpansion() {
    $this->createQuery()->from("pages")->condition("pages.owner.email", "root");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner WHERE `pages_owner`.email = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("root", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields.
   *
   * @return void
   */
  public function testConditionExpansionSelect() {
    $this->createQuery()->from("pages")->select("pages.owner.email")->condition("pages.owner.email", "root");

    // Expected output.
    $expected = "SELECT `pages_owner`.email FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner WHERE `pages_owner`.email = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("root", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields.
   *
   * @return void
   */
  public function testConditionExpansionMany() {
    $this->createQuery()->from("pages")->subquery("images.mime_type", function ($sub, $query) {
      $query->condition($sub, "image/png");
    });

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` WHERE :default0 IN (".
      "SELECT `files`.mime_type FROM `test_pages_images` AS `pages_images` ".
      "LEFT JOIN `test_files` AS `files` ON files.id=pages_images.images_id ".
      "WHERE `pages_images`.pages_id=`pages`.id)";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("image/png", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields.
   *
   * @return void
   */
  public function testConditionExpansionManyJoin() {
    $this->createQuery()->from("pages")->condition("images.mime_type", "image/%", "LIKE")->group("pages.id");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_pages_images` AS `pages_images_lookup` ON pages_images_lookup.pages_id=pages.id LEFT JOIN `test_files` AS `pages_images` ON pages_images.id=pages_images_lookup.images_id WHERE `pages_images`.mime_type LIKE :default0 GROUP BY `pages`.id";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("image/%", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields.
   *
   * @return void
   */
  public function testConditionExpansionCategory() {
    $this->createQuery()->from("pages")->condition("pages.category.slug", "general");

    // Expected output.
    $expected = "SELECT `pages`.* FROM `test_pages` AS `pages` LEFT JOIN `test_terms` AS `pages_category` ON pages_category.id=pages.category WHERE `pages_category`.slug = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("general", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields and specify the comparator field explicitly.
   *
   * @return void
   */
  public function testConditionExpansionTermsJoin() {
    $this->createQuery()->from("users")->condition("users.groups.slug", "user", "!=")->group("users.id");

    // Expected output.
    $expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id WHERE `users_groups`.slug != :default0 GROUP BY `users`.id";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("user", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in condition fields.
   *
   * @return void
   */
  public function testConditionExpansionTermsSubquery() {
    $this->createQuery()->from("users")->subquery("users.groups.slug", function ($sub, $query) {
      $query->condition($sub, "user", "!=");
    });

    // Expected output.
    $expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE :default0 NOT IN (SELECT `terms`.slug FROM `test_users_groups` AS `users_groups` LEFT JOIN `test_terms` AS `terms` ON terms.id=users_groups.groups_id WHERE `users_groups`.users_id=`users`.id)";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("user", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test expansions in where clauses.
   *
   * @return void
   */
  public function testWhereExpansionTermsJoin() {
    $this->createQuery()->from("users")->where("users.groups.slug != :group")->group("users.id")->bind("group", "user");

    // Expected output.
    $expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id WHERE `users_groups`.slug != :group GROUP BY `users`.id";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("user", $this->builder->getQuery()->getParameter("group"));
  }

  /**
   * Test grouping.
   *
   * @return void
   */
  public function testGrouping() {
    $this->createQuery()->from("users")->select("COUNT(*) as count")->group("MONTH(created)");

    // Expected output.
    $expected = "SELECT COUNT(*) AS count FROM `test_users` AS `users` GROUP BY MONTH(created)";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test expansions in GROUP BY clauses.
   *
   * @return void
   */
  public function testGroupingExpansion() {
    $this->createQuery()->from("pages")->select("COUNT(*) as count")->group("owner.first_name");

    // Expected output.
    $expected = "SELECT COUNT(*) AS count FROM `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner GROUP BY `pages_owner`.first_name";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test expansions in GROUP BY clauses.
   *
   * @return void
   */
  public function testGroupingExpansionTerms() {
    $this->createQuery()->from("users")->select("COUNT(*) as count")->group("users.groups.slug");

    // Expected output.
    $expected = "SELECT COUNT(*) AS count FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id GROUP BY `users_groups`.slug";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test having.
   *
   * @return void
   */
  public function testHavingCondition() {
    $this->createQuery()->from("terms")
      ->select(["terms.taxonomy", "COUNT(*) as count"])
      ->group("terms.taxonomy")
      ->havingCondition("count", "0", ">");

    // Expected output.
    $expected = "SELECT `terms`.taxonomy, COUNT(*) AS count FROM `test_terms` AS `terms` GROUP BY `terms`.taxonomy HAVING count > :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("0", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test having.
   *
   * @return void
   */
  public function testSorting() {
    $this->createQuery()->from("terms")
      ->sort("taxonomy")
      ->sort("slug", 1)
      ->sort("created", -1);

    // Expected output.
    $expected = "SELECT `terms`.* FROM `test_terms` AS `terms` ORDER BY taxonomy, slug ASC, created DESC";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test limit.
   *
   * @return void
   */
  public function testLimit() {
    $this->createQuery()->from("terms")->limit(5)->skip(10);

    // Expected output.
    $expected = "SELECT `terms`.* FROM `test_terms` AS `terms` LIMIT 5 OFFSET 10";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  /**
   * Test or conditions.
   *
   * @return void
   */
  public function testOrCondition() {
    $query = $this->createQuery()->from("terms");
    $conditions = $query->createOrCondition();
    $conditions->condition("taxonomy", "groups");
    $conditions->condition("taxonomy", "statuses");
    $query->condition($conditions);

    // Expected output.
    $expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :default0 OR taxonomy = :default1)";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("groups", $this->builder->getQuery()->getParameter("default0"));
    $this->assertSame("statuses", $this->builder->getQuery()->getParameter("default1"));
  }

  /**
   * Test or conditions using where method on child condition.
   *
   * @return void
   */
  public function testOrWhere() {
    $query = $this->createQuery()->from("terms");
    $conditions = $query->createOrCondition()
      ->where("taxonomy = :tax1")
      ->where("taxonomy = :tax2");
    $query->condition($conditions)->bind(["tax1" => "groups", "tax2" => "statuses"]);

    // Expected output.
    $expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :tax1 OR taxonomy = :tax2)";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("groups", $this->builder->getQuery()->getParameter("tax1"));
    $this->assertSame("statuses", $this->builder->getQuery()->getParameter("tax2"));
  }

  /**
   * Test or conditions using where method on query builder and explicit OR in the string clause.
   *
   * @return void
   */
  public function testOrWhereShorter() {
    $this->createQuery()->from("terms")
      ->where("taxonomy = :tax1 OR taxonomy = :tax2")
      ->bind(["tax1" => "groups", "tax2" => "statuses"]);

    // Expected output.
    $expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE taxonomy = :tax1 OR taxonomy = :tax2";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("groups", $this->builder->getQuery()->getParameter("tax1"));
    $this->assertSame("statuses", $this->builder->getQuery()->getParameter("tax2"));
  }

  /**
   * Test a nested expression.
   *
   * @return void
   */
  public function testOrConditionSet() {
    $query = $this->createQuery()->from("terms");

    // This is the clause we want to create:
    // WHERE (taxonomy = 'groups' OR (created >= '00:00:00' AND taxonomy = 'statuses'))

    // More generically, this is the structure we're looking for:
    // WHERE (A OR (B AND C))

    // For illustrative purposes we'll create the nested condition first
    // the nested condition is an AND between B and C
    // $recent represents (B AND C)
    $recent = $query->createCondition(); // create AND condition
    $recent->condition("created", date("Y-m-d")." 00:00:00", ">="); // add B
    $recent->condition("taxonomy", "statuses"); // add C

    // Now we create the outer condition which is an OR between A and (B AND C)
    $conditions = $query->createOrCondition(); // create OR condition
    $conditions->condition("taxonomy", "groups"); // add A
    $conditions->condition($recent); // add (B AND C)

    $query->condition($conditions);

    // Expected output.
    $expected = "SELECT `terms`.* FROM `test_terms` AS `terms` WHERE (taxonomy = :default0 OR (created >= :default1 AND taxonomy = :default2))";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("groups", $this->builder->getQuery()->getParameter("default0"));
    $this->assertSame(date("Y-m-d")." 00:00:00", $this->builder->getQuery()->getParameter("default1"));
    $this->assertSame("statuses", $this->builder->getQuery()->getParameter("default2"));
  }

  /**
   * Test conditions expanded through multiple reference fields.
   *
   * @return void
   */
  public function testMultiValueTermExpansion() {
    $this->createQuery()->from("users")->condition("users.groups.slug", ["user", "admin"]);

    // Expected output.
    $expected = "SELECT `users`.* FROM `test_users` AS `users` LEFT JOIN `test_users_groups` AS `users_groups_lookup` ON users_groups_lookup.users_id=users.id LEFT JOIN `test_terms` AS `users_groups` ON users_groups.id=users_groups_lookup.groups_id WHERE `users_groups`.slug IN (:default0, :default1)";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("user", $this->builder->getQuery()->getParameter("default0"));
    $this->assertSame("admin", $this->builder->getQuery()->getParameter("default1"));
  }

  /**
   * Test delete mode.
   *
   * @return void
   */
  public function testRemove() {
    $this->createQuery()->from("users")->condition("email", "phpunit")->mode("delete");

    // Expected output.
    $expected = "DELETE `users`.* FROM `test_users` AS `users` WHERE email = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test insert mode.
   *
   * @return void
   */
  public function testInsert() {
    $this->createQuery()->from("users")->set("first_name", "PHPUnit")->mode("insert");

    // Expected output.
    $expected = "INSERT INTO `test_users` SET `first_name` = :set0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
  }

  /**
   * Test SET clause.
   *
   * @return void
   */
  public function testUpdate() {
    $this->createQuery()->from("users")->condition("email", "phpunit")->set("first_name", "PHPUnit")->mode("update");

    // Expected output.
    $expected = "UPDATE `test_users` AS `users` SET `users`.`first_name` = :set0 WHERE email = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
    $this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test conditions through expanded references.
   *
   * @return void
   */
  public function testUpdateConditionExpansion() {
    $this->createQuery()->from("pages")->set("pages.content", "PHPUnit")->condition("owner.first_name", "phpunit")->mode("update");

    // Expected output.
    $expected = "UPDATE `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner SET `pages`.`content` = :set0 WHERE `pages_owner`.first_name = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
    $this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
  }

  /**
   * Test setting values through expanded references.
   *
   * @return void
   */
  public function testUpdateSetExpansion() {
    $this->createQuery()->from("pages")->set("owner.first_name", "PHPUnit")->condition("pages.content", "phpunit")->mode("update");

    // Expected output.
    $expected = "UPDATE `test_pages` AS `pages` LEFT JOIN `test_users` AS `pages_owner` ON pages_owner.id=pages.owner SET `pages_owner`.`first_name` = :set0 WHERE `pages`.content = :default0";

    // Compare.
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame("PHPUnit", $this->builder->getQuery()->getParameter("set0"));
    $this->assertSame("phpunit", $this->builder->getQuery()->getParameter("default0"));
  }
}
