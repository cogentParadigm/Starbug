<?php
namespace Starbug\Core;

class Pager {
  public $count; // total number of items
  public $per; // items per page
  public $last; // last page
  public $start; // showing $start to $finish of $count items
  public $finish; // showing $start to $finish of $count items
  public $current_page; // current page
  public $range; // number of page links to show
  public function __construct($total, $items_per_page, $current_pg = 1, $range = 6) {
    $this->count = $total;
    $this->per = $items_per_page;
    $this->range = $range;
    if (empty($current_pg) || !is_numeric($current_pg)) $current_pg = 1;
    $this->current_page = $current_pg;
    $this->last = ceil($total/$items_per_page);
    $this->start = (($current_pg-1)*$items_per_page);
    if ($this->current_page < 1) $this->current_page = 1;
    elseif ($this->current_page > $this->last) $this->current_page = $this->last;
    $remaining = $total - $this->start;
    $shown = ($remaining<$items_per_page) ? ($remaining % $items_per_page) : $items_per_page;
    $this->finish = $this->start + $shown;
  }
}
