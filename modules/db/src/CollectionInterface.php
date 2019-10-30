<?php
namespace Starbug\Core;

interface CollectionInterface {
  public function getPager() : ?Pager;
  public function addFilter(CollectionFilterInterface $filter);
  public function query($ops = []);
  public function one($ops = []);
}
