<?php
namespace Starbug\Core;
class ImagesHelper {
	public function __construct(ImagesInterface $url) {
		$this->target = $url;
	}
	public function helper() {
		return $this->target;
	}
}
?>
