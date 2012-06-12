<?
	$sb->import("core/lib/Controller");
	list($controller, $action) = $request->uri;
	$c = ucwords($controller);
	
	$failure = false;
	$name = ucwords($c)."Controller";
	$found = locate("$name.php", "controllers");
	if (!empty($found)) include(end($found));
	else {
		$failure = true;
		$request->missing();
		render("html");
	}
	
	if (!$failure) {
		$object = new $name();
		if (empty($action)) $action = "default_action";
		call_user_func_array(array($object, $action), array_splice($request->uri, 2));
	}
?>
