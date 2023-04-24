<?php
namespace Starbug\Db;

use Starbug\Core\Pager;

class Collection implements CollectionInterface {
  protected $model;
  protected $search_fields = false;
  public $results = [];
  protected $filters = [];
  protected $pager;
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function getModel() {
    return $this->model;
  }
  public function setModel($model) {
    $this->model = $model;
  }
  public function getPager() : ?Pager {
    return $this->pager;
  }
  public function addFilter(CollectionFilterInterface $filter) {
    $this->filters[] = $filter;
  }
  public function prepare($query, $ops) {
    return $query;
  }
  public function build($query, $ops) {
    return $query;
  }
  public function filterRows($rows) {
    return $rows;
  }
  public function filterQuery($query, $ops) {
    if (isset($ops['id'])) {
      $query->condition($this->model.".id", explode(",", $ops['id']));
    }
    if (!empty($ops['keywords']) && $this->search_fields) {
      $query->search($ops['keywords'], $this->search_fields);
    }
    return $query;
  }
  public function query($ops = []) {
    $query = $this->db->query($this->model);
    if (false === $this->search_fields) {
      $this->search_fields = $query->getSearchFields($this->model);
    }
    // prepare
    $query = $this->prepare($query, $ops);
    // filter query
    $query = $this->filterQuery($query, $ops);
    foreach ($this->filters as $filter) {
      $query = $filter->filterQuery($this, $query, $ops);
    }
    // build
    $query = $this->build($query, $ops);
    // paginate
    if (!empty($ops['limit'])) {
      $query->limit($ops['limit']);
      $page = empty($ops['page']) ? 1 : $ops['page'];
      $this->pager = $query->pager($ops['page']);
    }

    // execute query
    $data = (is_array($query) && isset($query['data'])) ? $query['data'] : $query->all();

    // filter result records
    foreach ($this->filters as $filter) {
      $data = $filter->filterRows($this, $data);
    }
    $data = $this->filterRows($data);
    $this->results = $data;
    return $this->results;
  }
  public function one($ops = []) {
    $this->query($ops);
    return reset($this->results);
  }
}
