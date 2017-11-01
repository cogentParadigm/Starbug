<?php
namespace Starbug\Core;
class Collection {
	protected $model;
	protected $search_fields = false;
	public $results = array();
	protected $filters = array();
	protected $pager;
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	public function getModel() {
		return $this->model;
	}
	public function setModel($model) {
		$this->model = $model;
	}
	public function getPager() {
		return $this->pager;
	}
	public function addFilter(CollectionFilterInterface $filter) {
		$this->filters[] = $filter;
	}
	public function prepare($query, &$ops) {
		return $query;
	}
	public function build($query, &$ops) {
		return $query;
	}
	public function filterRows($rows) {
		return $rows;
	}
	public function filterQuery($query, &$ops) {
		if (!empty($ops['id'])) {
			$query->condition($this->model.".id", explode(",", $ops['id']));
		}
		if (!empty($ops['keywords']) && $this->search_fields) $query->search($ops['keywords'], $this->search_fields);
		return $query;
	}
	public function query($ops = array()) {
		if (false === $this->search_fields) {
			$this->search_fields = $this->models->get($this->model)->search_fields;
		}
		//create
		$query = $this->models->get($this->model)->query();
		//prepare
		$query = $this->prepare($query, $ops);
		//filter query
		$query = $this->filterQuery($query, $ops);
		foreach ($this->filters as $filter) {
			$query = $filter->filterQuery($this, $query, $ops);
		}
		$query = $this->models->get($this->model)->filterQuery($this, $query, $ops);
		//build
		$query = $this->build($query, $ops);
		//paginate
		if (!empty($ops['limit'])) {
			$query->limit($ops['limit']);
			$page = empty($ops['page']) ? 1 : $ops['page'];
			$this->pager = $query->pager($ops['page']);
		}

		//execute query
		$data = (is_array($query) && isset($query['data'])) ? $query['data'] : $query->all();

		//filter result records
		foreach ($this->filters as $filter) {
			$data = $filter->filterRows($this, $data);
		}
		$data = $this->filterRows($data);
		$data = $this->models->get($this->model)->filterRows($this, $data);
		$this->results = $data;
		return $this->results;
	}
	public function one($ops = array()) {
		$this->query($ops);
		return reset($this->results);
	}
}
