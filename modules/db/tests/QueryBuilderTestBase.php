<?php
namespace Starbug\Db\Tests;

use Starbug\Db\Query\Query;
use Starbug\Db\Query\Builder;
use Starbug\Db\Query\Compiler;
use Starbug\Db\Schema\Schema;
use Starbug\Db\Schema\QueryCompilerHook;
use PHPUnit\Framework\TestCase;

class QueryBuilderTestBase extends TestCase {

  public function setUp() {
    $this->compiler = $this->createCompiler();
  }

  protected function createCompiler() {
    $compiler = new Compiler(new MockDatabase());
    $compiler->addHook(new QueryCompilerHook($this->createSchema()));
    return $compiler;
  }

  protected function createQuery() {
    $this->builder = new Builder(new MockExecutor());
    $this->builder->setSchema($this->createSchema());
    return $this->builder;
  }

  protected function compile(Builder $builder = null) {
    if (is_null($builder)) $builder = $this->builder;
    $query = $builder->getQuery();
    return $this->compiler->build($query)->getSql();
  }

  protected function createSchema() {
    $schema = new Schema();
    $schema->addTable("files",
      ["filename", "type" => "string"],
      ["mime_type", "type" => "string"],
      ["deleted", "type" => "bool", "default" => "0", "object_access" => true]
    );
    $schema->addTable("terms",
      ["name", "type" => "string"],
      ["slug", "type" => "string"],
      ["deleted", "type" => "bool", "default" => "0", "object_access" => true]
    );
    $schema->addTable("users",
      ["first_name", "type" => "string"],
      ["groups", "type" => "terms", "taxonomy" => "groups", "user_access" => true, "optional" => true],
      ["deleted", "type" => "bool", "default" => "0", "object_access" => true]
    );
    $schema->addTable("permits",
      ["role", "type" => "string", "length" => "30"],
      ["who", "type" => "int", "default" => "0"],
      ["action", "type" => "string", "length" => "100"],
      ["priv_type", "type" => "string", "length" => "30", "default" => "table"],
      ["related_table", "type" => "string", "length" => "100"],
      ["related_id", "type" => "int", "default" => "0"],
      ["groups", "type" => "terms", "taxonomy" => "groups", "user_access" => true, "optional" => true]
    );
    $schema->addTable("pages",
      ["owner", "type" => "int", "references" => "users id"],
      ["category", "type" => "int", "references" => "terms id"],
      ["comments", "type" => "comments"],
      ["images", "type" => "files"],
      ["deleted", "type" => "bool", "default" => "0", "object_access" => true]
    );
    $schema->addTable("comments",
      ["comment", "type" => "text"],
      ["deleted", "type" => "bool", "default" => "0", "object_access" => true]
    );
    // This schema instance has no hooks so we have to manually
    // define the join tables to use all schema aware features.
    $schema->addTable("users_groups",
      ["users_id", "type" => "int", "references" => "users id"],
      ["groups_id", "type" => "int", "references" => "terms id"]
    );
    $schema->addTable("pages_images",
      ["pages_id", "type" => "int", "references" => "pages id"],
      ["images_id" ,"type" => "int", "references" => "files id"]
    );
    return $schema;
  }
}
