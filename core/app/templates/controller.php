<?
	$sb->import("core/Controller");
	if ($request->uri[0] == "c") array_shift($request->uri);
	list($controller, $action) = $request->uri;
	$c = ucwords($controller);
	
	$failure = false;
	if (file_exists(BASE_DIR."/app/controllers/$c.php")) include(BASE_DIR."/app/controllers/$c.php");
	else if (file_exists(BASE_DIR."/app/themes/".request("theme")."/controllers/$c.php")) include(BASE_DIR."/app/themes/".request("theme")."/controllers/$c.php");
	else if (file_exists(BASE_DIR."/core/app/controllers/$c.php")) include(BASE_DIR."/core/app/controllers/$c.php");
	else {
		$failure = true;
		$request->missing();
		render("html");
	}
	
	if (!$failure) {
		$object = new $c();
		if (!method_exists($object, $action)) $action = "default";
		call_user_func_array(array($object, $action), array_splice($request->uri, 3));
	}
?>
