<?php
class TermsGrid extends GridDisplay {
	public $model = "terms";
	public $action = "admin";
	public function build_display($options) {
		$this->add("taxonomy", "row_options  plugin:starbug.grid.columns.taxonomy_options");
	}
}
?>
