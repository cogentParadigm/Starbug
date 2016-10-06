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
	public function post($action, $data = array()) {
		$this->action = $action;
		if ($this->user->loggedIn("admin") || $this->user->loggedIn("root")) return true;
		else if (isset($data['id'])) {
			$order = $this->cart->get("id");
			$permits = $this->db->query($this->type)
										->condition($this->type.".id", $data['id'])
										->condition($this->type.".orders_id", $order)->one();
		} else {
			$permits = $this->db->query("permits")->action($action, $this->type)->one();
		}
		if ($permits) {
			$this->$action($data);
			return true;
		} else {
			$this->error("Access Denied");
			return false;
		}
	}
}
?>
