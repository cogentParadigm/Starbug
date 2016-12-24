<?php
namespace Starbug\Core;
class UrlHelper {
	public function __construct(URLInterface $url) {
		$this->target = $url;
	}
	public function helper() {
		return $this->target;
	}
}
