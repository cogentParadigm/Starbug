<?php
	sb::load("core/app/plugins/jsforms");
	$sb->import("util/form");
	$collectives = array_merge(array("everybody" => 0), $this->groups);
	$parents = $sb->query("uris", "action:read");
	$kids = array();
	foreach($parents as $u) $kids[$u['parent']][] = $u;
	function parent_options($u, $k, $l=0) {
		$arr = array();
		$key = $u['path'];
		for($i=0;$i<$l;$i++) $key = "-".$key;
		$arr[$key] = $u['id'];
		foreach ($k[$u['id']] as $kid) $arr = array_merge_recursive($arr, parent_options($kid, $k, $l+1));
		return $arr;
	}
	$parent_ops = array("root (no parent)" => 0);
	foreach($kids[0] as $child) $parent_ops = array_merge_recursive($parent_ops, parent_options($child, $kids));
	$fu = new form("uris", "action:$action	url:$submit_to");
	echo $fu->open();
?>
	<div class="field"><?php echo $fu->text("path"); ?></div>
	<div class="field"><?php echo $fu->text("template"); ?></div>
	<div class="field"><?php echo $fu->text("prefix	default:app/nouns/"); ?></div>
	<div class="field"><?php echo $fu->select("parent", $parent_ops); ?></div>
	<div class="field"><?php echo $fu->select("collective", $collectives); ?></div>
	<?php echo $fu->submit("class:big round button	value:Save"); ?>
</form>
