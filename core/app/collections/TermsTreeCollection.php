<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/collections/TermsTreeCollection.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
namespace Starbug\Core;

class TermsTreeCollection extends TermsCollection {
	public function __construct(ModelFactoryInterface $models, DatabaseInterface $db) {
		$this->models = $models;
		$this->db = $db;
	}
	public function build($query, &$ops) {
		$query->select("terms.*,(SELECT COUNT(*) FROM ".$this->db->prefix("terms")." as t WHERE t.parent=terms.id) as children");
		if (!empty($ops['parent'])) $query->condition("parent", $ops['parent']);
		else $query->condition("terms.parent", 0);
		$query->sort("terms.position");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as &$row) {
			$depth = 0;
			if (!empty($row['term_path'])) {
				$tree = $row['term_path'];
				$depth = substr_count($tree, "-")-1;
			}
			if ($depth > 0) $row['term'] = str_pad(" ".$row['term'], strlen(" ".$row['term'])+$depth, "-", STR_PAD_LEFT);
		}
		return $rows;
	}
}
?>
