<?php
namespace Starbug\Db;

use Starbug\Core\Pager;

interface CollectionInterface {
  public function getPager() : ?Pager;
  public function addFilter(CollectionFilterInterface $filter);
  public function query($ops = []);
  public function one($ops = []);
}
