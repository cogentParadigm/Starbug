<?php
namespace Starbug\Payment;
use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;
class ProductLinesCollection extends Collection {
	public function __construct(ModelFactoryInterface $models, Cart $cart) {
		$this->models = $models;
		$this->cart = $cart;
	}
	public function build($query, &$ops) {
		$query->condition("product_lines.orders_id", $this->cart->get("id"));
		$query->select("product_lines.product.sku");
		$query->select("product_lines.product.type.slug as product_type");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as $idx => $item) {
			$item['description'] = (string) '<strong>'.$item['description'].'</strong>';
			$item['total'] = $item['price'] * $item['qty'];
			$item['total_formatted'] = money_format('%.2n', $item['total']/100);
			$item['price_formatted'] = money_format('%.2n', $item['price']/100);
			$rows[$idx] = $item;
		}
		return $rows;
	}
}
?>
