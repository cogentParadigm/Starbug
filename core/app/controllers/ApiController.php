<?php
namespace Starbug\Core;

class ApiController extends Controller implements CollectionFilterInterface {
  protected $model;
  public function __construct(ApiRequest $api) {
    $this->api = $api;
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
