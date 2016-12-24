<?php
namespace Starbug\Core;
class AdminTermsCollection extends TermsCollection {
	public function build($query, &$ops) {
		$query->undo("select");
		$query->select("DISTINCT terms.taxonomy");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as &$row) {
			$row['id'] = $row['taxonomy'];
		}
		return $rows;
	}
}
