<?php
$sb->provide("util/permits");
// register_permit("type:table  model:users  action:login  role:everyone");
// register_permit("type:table,global  model:albums  action:create  role:group  who:2");
function register_permit($args) {
	global $statuses;
	global $sb;
	$args = starr::star($args);
	efault($args['type'], "table");
	efault($args['role'], "everyone");
	$types = explode(",", $args['type']);
	$status = 0;
	foreach($statuses as $c => $v) $status += $v;
	efault($args['status'], $status);
	if ((isset($args['status'])) && (!is_numeric($args['status']))) {
		$args['status'] = $statuses[$args['status']];
	}
	$permit = array("action" => $args['action'], "role" => $args['role'], "status" => $args['status']);
	if (isset($args['who'])) $permit['who'] = $args['who'];
	if (isset($args['id'])) $permit['related_id'] = $args['who'];
	$errors = array();
	foreach($types as $type) {
		$permit['priv_type'] = $type;
		$models = explode(",", $args['model']);
		foreach($models as $model) {
			$permit["related_table"] = P($model);
			if (false !== ($return = store_once("permits", $permit))) $errors = array_merge($errors, $return);
		}
	}
}
?>
