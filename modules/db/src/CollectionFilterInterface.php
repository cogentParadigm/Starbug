<?php
namespace Starbug\Db;

interface CollectionFilterInterface {
  public function filterQuery($collection, $query, $ops);
  public function filterRows($collection, $rows);
}
