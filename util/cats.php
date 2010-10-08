<?php
/**
 * This file is part of StarbugPHP <br/>
 * @file util/cats.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup cats
 */
/**
 * @defgroup cats
 * functions for working with categories
 * @ingroup util
 */
$sb->provide("util/cats");
	/**
	 * generates html option tags in a heirarchy. requires parent column
	 * @param string $categories the model name
	 * @param int $match the id of the record to make selected
	 * @param int $parent the top level to start form
	 * @return string the html output of the option tags
	 * @ingroup cats
	 */
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
	/**
	 * returns a comma delimited list of id's and it's decendents using a parent column
	 * @param string $categories the model name
	 * @param int $cid the id of the top ancestor
	 * @return string a comma delimted list of id's that can be used in a sql query IN(...)
	 * @ingroup cats
	 */
	function cat_query($categories, $cid, $prefix="") {
		global $sb;
		$prefix .= $cid;
		$children = $sb->query($categories, "where:parent=".$cid);
		if (!empty($children)) foreach($children as $kid) $prefix .= ", ".cat_query($categories, $kid['id'], "");
		return $prefix;
	}
?>
