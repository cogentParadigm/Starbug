<?php
	function cat_select($categories, $match=0, $parent=0, $iteration=0) {
		$cats = $categories->get("*", "parent='".$parent."'")->GetRows();
		foreach($cats as $row) { ?>
			<option value="<?php echo $row["id"]; ?>" <?php if(!empty($match) && $match == $row["id"]) { ?>selected="selected" <?php } ?>><?php for($i=0;$i<$iteration+1;$i++) { ?>-<?php } ?> <?php echo $row["name"]; ?></option>
			<?php cat_select($categories, $match, $row["id"], $iteration+1);
		}
	}
	function cat_query($categories, $cid, $prefix="") {
		$prefix .= $cid;
		$children = $categories->get("*", "parent=".$cid)->GetRows();
		if (!empty($children)) foreach($children as $kid) $prefix .= ", ".cat_query($categories, $kid['id'], "");
		return $prefix;
	}
?>
