<?php
namespace Starbug\Core;
class ApiController extends Controller implements CollectionFilterInterface {
	function init() {
		$this->api->setModel($this->model);
	}
	function setApi(ApiRequest $api) {
		$this->api = $api;
		$api->addFilter($this);
	}
	function getApi() {
		return $this->api;
	}
	function filterQuery($collection, $query, &$ops) {
		return $query;
	}
	function filterRows($collection, $rows) {
		foreach ($rows as $idx => $row) {
			$rows[$idx] = $this->filterRow($collection, $row);
		}
		return $rows;
	}
	function filterRow($collection, $row) {
		return $row;
	}
}
