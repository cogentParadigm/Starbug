<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ModelTest.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup ModelTest
 */
namespace Starbug\Core;
/**
 * @defgroup ModelTest
 * the base test class for models
 * @ingroup test
 */
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup Fixture
 */
class ModelTest extends \PHPUnit_Framework_TestCase {

	public $model;
	protected $db;
	protected $models;

	function setUp() {
		global $container;
		$this->db = $container->get("Starbug\Core\DatabaseInterface");
		$this->models = $container->get("Starbug\Core\ModelFactoryInterface");
	}

	function get() {
		$args = array_merge(array($this->model), func_get_args());
		return call_user_func_array(array($this->db, "get"), $args);
	}

	function query() {
		$args = array_merge(array($this->model), func_get_args());
		return call_user_func_array(array($this->db, "query"), $args);
	}

	function action() {
		$args = func_get_args();
		$method = array_shift($args);
		return call_user_func_array(array($this->models->get($this->model), $method), $args);
	}

	function __get($name) {
		return $this->models->get($this->model)->$name;
	}

}
?>
