<?
	list($controller, $action) = $request->uri;
	$object = controller($controller);
	$object->action($action, array_slice($request->uri, 2));
?>
