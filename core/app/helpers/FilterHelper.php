<?php
namespace Starbug\Core;
class FilterHelper {
	public function __construct(InputFilterInterface $filter) {
		$this->target = $filter;
	}
	public function helper() {
		return $this->target;
	}
}
