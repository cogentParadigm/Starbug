<?php
class RequestHelper {
	public function __construct(Request $request) {
		$this->target = $request;
	}
	public function helper() {
		return $this->target;
	}
}
?>
