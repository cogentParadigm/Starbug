<?php
namespace Starbug\Core;

interface CollectionFilterInterface {
  public function filterQuery($collection, $query, &$ops);
  public function filterRows($collection, $rows);
}
