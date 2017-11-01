<?php
namespace Starbug\Core;
class RequestHelper {
	public function __construct(RequestInterface $request) {
		$this->target = $request;
	}
	public function helper() {
		return $this->target;
	}
}
