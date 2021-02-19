<?php
namespace Starbug\Core;

use Exception;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiRequest {

  // @var ModelFactoryInterface
  protected $models;
  // @var CollectionFactoryInterface
  protected $collections;
  // @var ServerRequestInterface
  protected $request;
  // @var ResponsefactoryInterface
  protected $response;
  protected $format = "json";
  protected $types = [
    "json" => "application/json",
    "csv" => "text/csv"
  ];

  protected $results = [];
  protected $headers = [];
  protected $options = [];
  protected $filters = [];
  protected $model;

  public function __construct(ModelFactoryInterface $models, CollectionFactoryInterface $collections, ServerRequestInterface $request, ResponseFactoryInterface $response) {
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
  public function setFormat($format) {
    if (!array_key_exists($format, $this->types)) {
      throw new Exception("Invalid format");
    }
    $this->format = $format;
  }
  public function getFormat() {
    return $this->format;
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

    // Instantiate the model and collection.
    if (!is_null($this->model)) {
      $instance = $this->models->get($this->model);
      if ($instance->errors()) {
        $this->results[$name] = $this->errors($this->model);
        return;
      }
    }
    $collection = $this->collections->get($collection);
    if (method_exists($collection, "setModel")) {
      $collection->setModel($this->model);
    }

    // Register filters.
    foreach ($this->filters as $filter) {
      $collection->addFilter($filter);
    }

    // Populate default options.
    $options = $options + $this->options + $this->request->getQueryParams();
    $range = $this->request->getHeader("HTTP_RANGE");
    if (!empty($range)) {
      list($start, $finish) = explode("-", end(explode("=", $range)));
      $options['limit'] = 1 + (int) $finish - (int) $start;
      $options['page'] = 1 + (int) $start/$options['limit'];
    }
    $bodyParams = $this->request->getParsedBody();
    if (!empty($bodyParams["action"][$this->model]) && !$instance->errors()) {
      $id = $bodyParams[$this->model]["id"] ?? $instance->insert_id;
      $options['id'] = $id;
    }

    $results = $collection->query($options);
    if ($this->getFormat() == "csv" && !empty($results)) {
      $headers = $results[0];
      foreach ($headers as $key => $value) {
        $headers[$key] = ucwords(str_replace("_", " ", $key));
      }
      array_unshift($results, $headers);
    }
    $this->results[$name] = $results;

    if ($pager = $collection->getPager()) {
      $this->headers["Content-Range"] = "items ".$pager->start.'-'.$pager->finish.'/'.$pager->count;
    } else {
      $count = count($this->results[$name]);
      $this->headers["Content-Range"] = "items 0-$count/$count";
    }
  }

  public function capture($key = false) {
    $format = $this->getFormat();
    $headers = $this->headers + [
      "Content-Type" => $this->types[$format],
      "Sync-Time" => date('Y-m-d H:i:s')
    ];
    $response = $this->response->createResponse();
    foreach ($headers as $name => $value) {
      $response = $response->withHeader($name, $value);
    }
    $results = $key ? $this->results[$key] : $this->results;
    if ($format == "json") {
      $response = $response->withBody(Utils::streamFor(json_encode($results)));
    } elseif ($format == "csv") {
      $csv = fopen('php://temp', 'r+');
      foreach ($results as $row) {
        fputcsv($csv, $row);
      }
      $response = $response->withBody(Utils::streamFor($csv));
    }
    return $response;
  }

  public function render($collection, $options = [], $name = false) {
    $this->add($collection, $options, $name);
    if (!$name) $name = $this->model;
    return $this->capture($name);
  }

  protected function errors($model) {
    $instance = $this->models->get($model);
    $json = ["errors" => []];
    foreach ($instance->errors("", true) as $k => $v) {
      $json['errors'][] = ["field" => $k, "errors" => $v];
    }
    return $json;
  }
}
