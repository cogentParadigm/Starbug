<?php
namespace Starbug\Payment;
use Starbug\Core\ModelFactoryInterface;
/**
 * The Authnet class. A simple wrapper for the Authorize.Net XML APIs
 * API documentation available at http://developer.authorize.net/api/
 * @ingroup payment
 */
class Cart implements \IteratorAggregate, \ArrayAccess, \Countable {

	protected $order = false;
	protected $lines = array(
		"product" => array(),
		"shipping" => array()
	);
	protected $hooks = array();

	protected $conditions = array();

	/**
	 * constructor.
	 */
	public function __construct(ModelFactoryInterface $models, $conditions) {
		$this->models = $models;
		$this->conditions = $conditions;
	}

	public function init($create = true) {
		if (!empty($this->order)) return;
		$this->load();
		if (empty($this->order) && $create) {
			$this->models->get("orders")->create(array());
			$this->load();
		}
	}

	public function load($conditions=array()) {
		if (empty($conditions)) $conditions = $this->conditions;
		if (empty($conditions['order_status'])) $conditions['order_status'] = 'cart';
		$this->order = $this->models->get("orders")->query()->conditions($conditions)->one();
		if (!empty($this->order)) {
			foreach ($this->lines as $k => $v) {
				$this->lines[$k] = $this->models->get($k."_lines")->query()->condition($k."_lines.orders_id", $this->order['id']);
				if ($k == "product") $this->lines[$k]->select("product_lines.product.type.slug as product_type");
				$this->lines[$k] = $this->lines[$k]->all();
			}
		}
	}

	public function getOrder() {
		$this->init(false);
		return $this->order;
	}

	public function get($property) {
		$this->init(false);
		return empty($this->order) ? null : $this->order[$property];
	}

	public function add($type, $options=array()) {
		$this->init(false);
		$options['orders_id'] = $this->order['id'];
		$this->models->get($type)->create($options);
	}

	public function offsetSet($offset, $value) {
		$this->init(false);
		if (is_null($offset)) {
			$this->lines['product'][] = $value;
		} else {
			$this->lines['product'][$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		$this->init(false);
		return isset($this->lines['product'][$offset]);
	}

	public function offsetUnset($offset) {
		$this->init(false);
		unset($this->lines['product'][$offset]);
	}

	public function offsetGet($offset) {
		$this->init(false);
		return isset($this->lines['product'][$offset]) ? $this->lines['product'][$offset] : null;
	}

	public function getIterator() {
		$this->init(false);
		return new \ArrayIterator($this->lines['product']);
	}

	public function count() {
		$this->init(false);
		$count = 0;
		foreach ($this->lines['product'] as $line) $count += intval($line['qty']);
		return $count;
	}

	function addProduct($options) {
		$product = $this->models->get("products")->query()->condition("products.id", $options['id'])->one();
		$line = array(
			"product" => $product['id'],
			"description" => $product['name'],
			"price" => $product['price']
		);
		$this->init();
		//pass id and qty
		$line['qty'] = 1;
		$this->add("product_lines", $line);
		$line['id'] = $this->models->get("product_lines")->insert_id;
		return $line;
  }
}

?>
