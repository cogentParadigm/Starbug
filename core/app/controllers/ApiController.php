<?php
namespace Starbug\Core;

use Starbug\Db\CollectionFilterInterface;
use Starbug\Routing\Controller;

class ApiController extends Controller implements CollectionFilterInterface {
  protected $model;
  public function __construct(
    protected ApiRequest $api
  ) {
    $api->setModel($this->model);
    $api->addFilter($this);
  }
  public function getApi() {
    return $this->api;
  }
  public function filterQuery($collection, $query, $ops) {
    return $query;
  }
  public function filterRows($collection, $rows) {
    foreach ($rows as $idx => $row) {
      $rows[$idx] = $this->filterRow($collection, $row);
    }
    return $rows;
  }
  public function filterRow($collection, $row) {
    return $row;
  }
}
