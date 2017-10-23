<?php
namespace Starbug\Db\Query;

interface CompilerHookInterface {
  public function beforeCompileQuery(QueryInterface $query, CompilerInterface $compiler);
}
