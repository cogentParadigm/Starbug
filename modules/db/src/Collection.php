<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/db/src/Collection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

/**
 * usage:
 * $queue = new QueryQueue();
 * $queue->push($query);
 * $queue->push($query2);
 * $queue->execute();
 * @ingroup db
 */

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
		$query = $this->models->get($this->model)->query();
		$query = $this->filterQuery($query, $ops);

		foreach ($this->filters as $filter) {
			$query = $filter->filterQuery($this, $query, $ops);
		}

		$query = $this->build($query, $ops);

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
		$this->results = $this->filterRows($data);

		return $this->results;
	}
}
?>
