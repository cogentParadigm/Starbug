<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/MenusTreeCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class MenusTreeCollection extends Collection {
	public function __construct(ModelFactoryInterface $models, DatabaseInterface $db) {
		$this->models = $models;
		$this->db = $db;
	}
	public function build($query, &$ops) {
		$query->select("menus.uris_id.title,(SELECT COUNT(*) FROM ".$this->db->prefix("menus")." as t WHERE t.parent=menus.id) as children");
		if (!empty($ops['parent'])) $query->condition("menus.parent", $ops['parent']);
		else {
			$query->condition("menus.parent", 0);
			$query->condition("menus.menu", $ops['menu']);
		}
		$query->sort("menus.menu_path ASC, menus.position ASC");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as $idx => $item) {
			if (empty($item['content']) && !empty($item['title'])) $item['content'] = $item['title'];
			if ($item['template'] === "divider") $item['content'] = "(divider)";
			$depth = 0;
			if (!empty($item['menu_path'])) {
				$tree = $item['menu_path'];
				$depth = substr_count($tree, "-")-1;
			}
			if ($depth > 0) $item['content'] = str_pad(" ".$item['content'], strlen(" ".$item['content'])+$depth, "-", STR_PAD_LEFT);
			$rows[$idx] = $item;
		}
		return $rows;
	}
}
?>
