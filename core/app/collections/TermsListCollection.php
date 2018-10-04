<?php
namespace Starbug\Core;
class TermsListCollection extends TermsCollection {
	public function build($query, &$ops) {
		$query->sort("terms.term_path ASC, terms.position ASC");
		return $query;
	}
}
