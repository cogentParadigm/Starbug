<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/LoggerInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * validation logger
 */
class Validation implements ValidationInterface {
	public $errors = array();
	public $scope = "global";
	protected $logger;
	protected $request;
	function __construct(LoggerFactoryInterface $loggers, RequestInterface $request) {
		$this->logger = $loggers->get(get_class($this));
		$this->request = $request;
	}
	/**
	 * @copydoc ValidationInterface::errors
	 */
	function errors($key = "", $values = false) {
		if (is_bool($key)) {
			$values = $key;
			$key = "";
		}
		$parts = explode("[", $key);
		$errors = $this->errors;
		if (!empty($key)) foreach ($parts as $p) $errors = $errors[rtrim($p, ']')];
		if ($values) return $errors;
		else return (!empty($errors));
	}
	/**
	 * @copydoc ValidationInterface::error
	 */
	function error($error, $field = "global", $model = "") {
		if (empty($model)) $model = $this->scope;
		$this->errors[$model][$field][] = $error;
		$this->logger->info("{model}::{action} - {field}:{message}", array("model" => $model, "action" => $this->request->data['action'][$model], "field" => $field, "message" => $error));
	}
	/**
	 * @copydoc ValidationInterface::error_scope
	 */
	function error_scope($value = null) {
		$scope = $this->scope;
		if ($value != null) $this->scope = $value;
		return $scope;
	}
	/**
	 * @copydoc ValidationInterface::success
	 */
	function success($model, $action) {
		return (($this->request->data['action'][$model] == $action) && (empty($this->errors[$model])));
	}
	/**
	 * @copydoc ValidationInterface::failure
	 */
	function failure($model, $action) {
		return (($this->request->data['action'][$model] == $action) && (!empty($this->errors[$model])));
	}
}
