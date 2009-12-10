<?php
$sb->provide("util/cats");
	function cat_select($categories, $match=0, $parent=0, $iteration=0) {
		global $sb;
		$cats = $sb->query($categories, "where:parent='".$parent."'");
		$select_str = "";
		foreach($cats as $row) {
			$select_str .= '<option value="'.$row["id"].'" '.((!empty($match) && $match == $row["id"]) ? 'selected="selected"' : '').'>\n';
			for($i=0;$i<$iteration+1;$i++) $select_str .= '-';
			$select_str .= $row["name"].'</option>';
			$select_str .= cat_select($categories, $match, $row["id"], $iteration+1);
		}
		return $select_str;
	}
	function cat_query($categories, $cid, $prefix="") {
		global $sb;
		$prefix .= $cid;
		$children = $sb->query($categories, "where:parent=".$cid);
		if (!empty($children)) foreach($children as $kid) $prefix .= ", ".cat_query($categories, $kid['id'], "");
		return $prefix;
	}
?>
