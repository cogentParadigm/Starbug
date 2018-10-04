<?php
namespace Starbug\Core;
class BreadcrumbsHelper {
	public function __construct(Breadcrumbs $target) {
		$this->target = $target;
	}
	public function helper() {
		return $this->target;
	}
}
