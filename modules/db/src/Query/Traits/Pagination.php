<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Core\Pager;

trait Pagination {
  protected ?Pager $pager = null;
  /**
   * Page the results.
   *
   * @param integer $page The page number you want the results from.
   * @param boolean $force Pass true to force re-querying the count.
   *
   * @return Pager
   */
  public function pager($page, $force = false) {
    if ($force || is_null($this->pager)) {
      $this->pager = new Pager($this->count(), $this->query->getLimit(), $page);
      $this->skip($this->pager->start);
    }
    return $this->pager;
  }
}
