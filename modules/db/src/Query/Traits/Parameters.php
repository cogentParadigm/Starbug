<?php
namespace Starbug\Db\Query\Traits;

trait Parameters {
	protected $parameters = [];

	public function setParameter($name, $value = null) {
		if (!is_array($name)) $name = array($name => $value);
		foreach ($name as $k => $v) $this->parameters[":".$k] = $v;
	}

	public function getParameter($name) {
		return $this->parameters[":".$name];
	}

	public function getParameters() {
		return $this->parameters;
	}
}
