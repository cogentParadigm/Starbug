<?php
namespace Starbug\Payment;
use Starbug\Core\GridDisplay;
class Product_typesGrid extends GridDisplay {
	public $model = "product_types";
	public $action = "admin";
	function build_display($options) {
		$this->add("name");
	}
}
