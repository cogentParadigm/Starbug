<?php
$sb->provide("util/uris");
// register_uri("path:admin title:Admin  template:templates/View  prefix:app/plugins/admin  collective:2");
// register_uri("title:Admin  prefix:app/plugins/admin  collective:2");
function register_uri($args) {
	global $statuses;
	global $sb;
	$args = starr::star($args);
	efault($args['path'], str_replace(" ", "-", strtolower($args['title'])));
	efault($args['template'], "templates/View");
	efault($args['status'], $statuses['public']);
	$errors = store_once("uris", $args);
	return $errors;
}
?>
