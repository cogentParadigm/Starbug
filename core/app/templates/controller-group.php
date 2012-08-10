<?
	list($base, $controller, $action) = $request->uri;
	$object = controller($controller, $base);
	if (empty($action)) $action = "default_action";
	call_user_func_array(array($object, $action), array_splice($request->uri, 3));
	if ($object->auto_render) render($object->template);
?>
