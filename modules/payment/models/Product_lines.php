<?php
namespace Starbug\Payment;
use Starbug\Core\Product_linesModel;
class Product_lines extends Product_linesModel {
	function update($lines) {
		if (count($this->cart)) {
			foreach ($lines as $id => $qty) {
				$line = $this->query()->condition("product_lines.id", $id)
				->condition("product_lines.orders_id", $this->cart->get('id'))->one();
				if ($line) {
					$this->store(["id" => $id, "qty" => $qty]);
				}
			}
		} else {
			$this->error("You have no items in your cart", "global");
		}
	}
	function delete($line) {
		if (count($this->cart)) {
			$line = $this->query()->condition("product_lines.id", $line['id'])
				->condition("product_lines.orders_id", $this->cart->get('id'))->one();
			if ($line) {
				$this->remove($line['id']);
			}
		} else {
			$this->error("You have no items in your cart", "global");
		}
	}
}
?>
