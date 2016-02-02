<?php
namespace Starbug\Core;
class ApiController extends Controller {
	public $routes = array(
		'response' => '{model}/{action}'
	);
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function response($model, $action) {
		$this->response->template = "xhr";
		$request = new ApiRequest($this->models, $model."/".$action.".".$this->request->getFormat(), $this->request->getParameters());
		$this->assign("api_request", $request);
		$this->response->content = $request->result;
		if (empty($this->response->content)) $this->response->content = '[]';
		if (!empty($request->template)) $this->response->template = $request->template;
	}
}
?>
