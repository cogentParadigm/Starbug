<?php
namespace Starbug\Core;

interface ModelFactoryInterface {
  public function has($collection);
  public function get($collection);
}
