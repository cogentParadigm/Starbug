<?php
namespace Starbug\EventDispatcher\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SaveEvent extends Event {
  public function __construct(
    protected $model,
    protected $record
  ) {
  }
  public function getModel() {
    return $this->model;
  }
  public function getRecord() {
    return $this->record;
  }
}
