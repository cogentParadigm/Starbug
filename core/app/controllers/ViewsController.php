<?php
class ViewsController {
	function show() {
		$this->render($this->response->path);
	}
}
?>
