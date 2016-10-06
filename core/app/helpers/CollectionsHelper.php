<?php
namespace Starbug\Core;
class CollectionsHelper {
	public function __construct(CollectionFactoryInterface $collections) {
		$this->target = $collections;
	}
	public function helper() {
		return $this->target;
	}
}
?>
