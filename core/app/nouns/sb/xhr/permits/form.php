<div class="permit">
<?php
	$sb->import("util/form");
	$name = next($this->uri);
	$privs = array("global" => "global", "table" => "table", "object" => "object"); $options = array("read" => "read"); $roles = array(); $rels = array();
	if ($sb->has($name)) {
		$sb->get($name);
		$methods = get_class_methods(ucwords($name));
		foreach($methods as $method) if (!(($method == "Table") || ($method == "query") || ($method == ucwords($name)) || ($method == "_call"))) $options[$method] = $method;
	}
	$info = unserialize(file_get_contents("var/schema/.info/".$name));
	foreach($sb->query($name) as $item) {
		$label = $info['label'];
		foreach($item as $field => $value) $label = str_replace("%".$field."%", $value, $label);
		$rels[$label] = $item['id'];
	}
	foreach(array("everone", "user", "group", "owner", "collective") as $role) $roles[$role] = $role;
	$fields = array(
		
	);
	$fields = array();
	for ($k=0;$k<=5;$k++) $fields[] = "div	class:field	fields:$$k";
	$fields["save"] = "submit	class:inline_button save_permit";
	$fields[] = "a	class:inline_button cancel_permit	content:cancel";
	$extras = array(
		array("related_table" => "hidden	value:".P($name)),
		array("priv_type" => "select	options:$6"),
		array("action" => "select	options:$7"),
		array("role" => "select	options:$8"),
		array("who" => "select"),
		array("related_id" => "select	options:$9"),
		$privs, $options, $roles, $rels);
	echo form::build("permits", "action:grant	url:$submit_to", $fields, $extras);
?>
</div>
