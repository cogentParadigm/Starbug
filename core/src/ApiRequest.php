<?php
namespace Starbug\Core;
class ApiRequest {

	protected $types = array(
		"xml" => "text/xml",
		"json" => "application/json",
		"jsonp" => "application/x-javascript",
		"csv" => "text/csv"
	);

	protected $results = [];
	protected $time = '0000-00-00 00:00:00';
	protected $options = [];
	protected $ranges = [];
	protected $filters = [];
	protected $model;

	public function __construct(ModelFactoryInterface $models, CollectionFactoryInterface $collections, RequestInterface $request, ResponseInterface $response) {
		$this->models = $models;
		$this->collections = $collections;
		$this->request = $request;
		$this->response = $response;
	}
	public function setModel($model) {
		$this->model = $model;
	}
	public function getModel() {
		return $this->model;
	}
	public function setOptions($ops) {
		foreach ($ops as $k => $v) {
			$this->options[$k] = $v;
		}
	}
	public function setOption($key, $value) {
		$this->options[$key] = $value;
	}
	public function addFilter(CollectionFilterInterface $filter) {
		$this->filters[] = $filter;
	}
	public function add($collection, $options = [], $name = false) {
		if (!$name) $name = $this->model;
		$options['time'] = $this->time;

		//instantiate the model and collection
		$instance = $this->models->get($this->model);
		if ($instance->errors()) {
			$this->results[$name] = $this->errors($this->model);
			return;
		}
		$collection = $this->collections->get($collection);
		$collection->setModel($this->model);

		//register filters
		foreach ($this->filters as $filter) {
			$collection->addFilter($filter);
		}

		//populate default options
		$options = $options + $this->options + $this->request->getParameters();
		$range = $this->request->getHeader("HTTP_RANGE");
		if (!empty($range)) {
			list($start, $finish) = explode("-", end(explode("=", $range)));
			$options['limit'] = 1 + (int) $finish - (int) $start;
			$options['page'] = 1 + (int) $start/$options['limit'];
		}
		if ($this->request->hasPost('action', $this->model) && !$instance->errors()) {
			$id = ($this->request->hasPost($this->model, 'id')) ? $this->request->getPost($this->model, 'id') : $instance->insert_id;
			$options['id'] = $id;
		}

		$this->results[$name] = $collection->query($options);

		if ($pager = $collection->getPager()) {
			$this->response->setHeader("Content-Range", "items ".$pager->start.'-'.$pager->finish.'/'.$pager->count);
		} else {
			$count = count($this->results[$name]);
			$this->response->setHeader("Content-Range", "items 0-$count/$count");
		}
	}

	public function capture($key = false) {
		$format = $this->request->getFormat();
		$this->response->setContentType($this->types[$format]);
		$this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
		$this->response->setHeader('Sync-Time', date('Y-m-d H:i:s'));
		$results = $key ? $this->results[$key] : $this->results;
		$this->response->content = $results;
	}

	public function render($collection, $options = [], $name = false) {
		$this->add($collection, $options, $name);
		if (!$name) $name = $this->model;
		$this->capture($name);
	}

	protected function errors($model) {
		$instance = $this->models->get($model);
		$schema = $instance->column_info();
		if (empty($schema)) $schema = array();
		$json = ["errors" => []];
		$e = $instance->errors("", true);
		foreach ($instance->errors("", true) as $k => $v) {
			if (!empty($schema[$k]) && !empty($schema[$k]['label'])) $k = $schema[$k]['label'];
			$json['errors'][] = ["field" => $k, "errors" => $v];
		}
		return $json;
	}
}
