<?php
	list($controller, $action) = $request->uri;
	$object = $sb->locator->get_module_class("controllers/".ucwords($controller)."Controller", "lib/Controller", "core");
	$instance = new $object($this);
	$instance->action(str_replace("-", "_", $action), array_slice($request->uri, 2));
?>
