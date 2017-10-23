<?php
namespace Starbug\Db\Tests;
use Starbug\Db\Query\Query;
use Starbug\Db\Query\Builder;
use Starbug\Db\Query\Compiler;
use Starbug\Db\Schema\Schema;
use Starbug\Db\Schema\QueryCompilerHook;
use PHPUnit_Framework_TestCase;
class QueryBuilderTestBase extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->compiler = $this->createCompiler();
  }

  function createCompiler() {
    $compiler = new Compiler("test_");
    $compiler->addHook(new QueryCompilerHook($this->createSchema()));
    return $compiler;
  }

  function createQuery() {
    $this->builder = new Builder();
    $this->builder->setSchema($this->createSchema());
    return $this->builder;
  }

  function compile(Builder $builder = null) {
    if (is_null($builder)) $builder = $this->builder;
    $query = $builder->getQuery();
    return $this->compiler->build($query)->getSql();
  }

  function createSchema() {
    $schema = new Schema();
    $schema->addTable("files",
      ["filename", "type" => "string"],
      ["mime_type", "type" => "string"]
    );
    $schema->addTable("terms",
      ["name", "type" => "string"],
      ["slug", "type" => "string"]
    );
    $schema->addTable("users",
      ["first_name", "type" => "string"],
      ["groups", "type" => "terms", "taxonomy" => "groups"]
    );
    $schema->addTable("pages",
      ["owner", "type" => "int", "references" => "users id"],
      ["category", "type" => "int", "references" => "terms id"],
      ["comments", "type" => "comments"],
      ["images", "type" => "files"]
    );
    $schema->addTable("comments",
      ["comment", "type" => "text"]
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
