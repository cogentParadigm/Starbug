<?php
namespace Starbug\Css;
class CssHelper {
	public function __construct(CssLoader $target) {
		$this->target = $target;
	}
	public function helper() {
		return $this->target;
	}
}
