<?
	list($controller, $action) = $request->uri;
	$object = controller($controller);
	$object->action($action, array_splice($request->uri, 2));
?>
