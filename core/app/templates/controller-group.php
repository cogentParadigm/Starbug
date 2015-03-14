<?php
	list($base, $controller, $action) = $request->uri;
	$object = controller($controller, $base);
	$instance = new $object($this);
	$instance->action($action, array_slice($request->uri, 3));
?>
