<?php
class MainController {
	public $validators = array(
		'response' => '{model}/{action}.{format}'
	);
	function response($model, $action) {
		$request = new ApiRequest($model."/".$action.".".$this->request->format, $this->request->get);
		$this->response->content = $request->execute();
	}
}
?>
