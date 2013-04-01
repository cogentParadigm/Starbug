<?
	list($base, $controller, $action) = $request->uri;
	$object = controller($controller, $base);
	$object->action($action, array_splice($request->uri, 3));
?>
