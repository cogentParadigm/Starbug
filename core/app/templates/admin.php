<?
	$sb->import("core/lib/Controller");
	list($admin, $controller, $action) = $request->uri;
	$c = ucwords($controller);
	
	$failure = false;
	$name = "Admin".ucwords($c)."Controller";
	$found = locate("$name.php", "controllers");
	if (!empty($found)) include(end($found));
	else {
		$admin_theme = $request->theme;
		$failure = true;
		$request->missing();
		$request->theme = $admin_theme;
		render("html");
	}
	
	if (!$failure) {
		$object = new $name();
		if (empty($action)) $action = "default_action";
		call_user_func_array(array($object, $action), array_splice($request->uri, 3));
		if ($object->auto_render) render($object->template);
	}
?>
