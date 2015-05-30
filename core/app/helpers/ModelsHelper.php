<?php
	class ModelsHelper {
		public function __construct(ModelFactoryInterface $models) {
			$this->target = $models;
		}
		public function helper() {
			return $this->target;
		}
	}
?>
