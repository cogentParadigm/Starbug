<?
	list($controller, $action) = $request->uri;
	$object = controller($controller);
	if (empty($action)) $action = "default_action";
	call_user_func_array(array($object, $action), array_splice($request->uri, 2));
	if ($object->auto_render) render(($object->template == "auto") ? (empty($request->template) ? $request->format : $request->template) : $object->template);
?>
