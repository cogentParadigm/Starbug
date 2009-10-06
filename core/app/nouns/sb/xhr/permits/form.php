<div class="permit">
<?php
	$sb->import("util/form");
	$name = next($this->uri);
	$privs = array("global" => "global", "table" => "table", "object" => "object"); $options = array("read" => "read"); $roles = array(); $rels = array();
	if ($sb->has($name)) {
		$sb->get($name);
		$methods = get_class_methods(ucwords($name));
		foreach($methods as $method) if (!(($method == "Table") || ($method == "query") || ($method == ucwords($name)))) $options[$method] = $method;
	}
	$info = unserialize(file_get_contents("var/schema/.info/".$name));
	foreach($sb->query($name) as $item) {
		$label = $info['label'];
		foreach($item as $field => $value) $label = str_replace("%".$field."%", $value, $label);
		$rels[$label] = $item['id'];
	}
	foreach(array("everone", "user", "group", "owner", "collective") as $role) $roles[$role] = $role;
	$fields = array(
		"related_table" => "type:hidden	value:$name",
		"priv_type" => "type:select	options:$0",
		"action" => "type:select	options:$1",
		"role" => "type:select	options:$2",
		"who" => "type:select",
		"related_id" => "type:select	options:$3",
		"save" => "type:submit	class:inline_button save_permit",
		"cancel" => "type:a	class:inline_button cancel_permit"
	);
	echo form::build($name, "action:grant	url:$submit_to", $fields, $privs, $options, $roles, $rels);
?>
</div>
