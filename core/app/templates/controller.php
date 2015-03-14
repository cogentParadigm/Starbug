<?php
	list($controller, $action) = $request->uri;
	$object = controller($controller);
	$instance = new $object($this);
	$instance->action($action, array_slice($request->uri, 2));
?>
