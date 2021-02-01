<?php
namespace Starbug\Core;

class ApiController extends Controller implements CollectionFilterInterface {
  public function setApi(ApiRequest $api) {
    $this->api = $api;
    $this->api->setModel($this->model);
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
