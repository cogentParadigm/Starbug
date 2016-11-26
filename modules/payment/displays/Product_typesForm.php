<?php
namespace Starbug\Payment;
use Starbug\Core\FormDisplay;
class Product_typesForm extends FormDisplay {
	public $model = "product_types";
	public $cancel_url = "admin/product_types";
	function build_display($options) {
		$this->add("name");
		$this->add("slug");
		$this->add("description");
		$this->add("content");
	}
}
