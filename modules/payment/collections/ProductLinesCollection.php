<?php
namespace Starbug\Payment;
use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;
class ProductLinesCollection extends Collection {
	public function build($query, &$ops) {
		$query->condition("product_lines.orders_id", $ops["id"]);
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
			if ($item["recurring"]) {
				$unit = $item["unit"];
				$interval = $item["interval"];
				$phrase = "";
				if ($unit == "days" && $interval == 365) {
					$phrase = "year";
				} else if ($interval == 1) {
					$phrase = substr($unit, 0, 1);
				} else {
					$phrase = $interval." ".$unit;
				}
				$item["price_formatted"] .= "/".$phrase;
				$item["total_formatted"] .= "/".$phrase;
			}
			$rows[$idx] = $item;
		}
		return $rows;
	}
}
?>
