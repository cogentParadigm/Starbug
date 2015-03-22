<?php
	list($base, $controller, $action) = $request->uri;
	$object = $sb->locator->get_module_class("controllers/".ucwords($base).ucwords($controller)."Controller", "lib/Controller", "core");
	$instance = new $object($this);
	$instance->action($action, array_slice($request->uri, 3));
?>
