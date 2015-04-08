<?php
class ApiController {
	public $validators = array(
		'response' => '{model}/{action}'
	);
	function response($model, $action) {
		$this->response->template = "xhr";
		$request = new ApiRequest($model."/".$action.".".$this->request->format, $this->request->parameters);
		$this->response->content = $request->result;
	}
}
?>
