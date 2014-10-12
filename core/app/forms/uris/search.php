<?php open_form("method:get", "class:form-inline"); ?>
		<?php text("keywords  nolabel:  class:round-left  onkeyup:uris_grid.filterChange(this)"); ?>
		<?php select("type  onchange:uris_grid.filterChange(this)  nolabel:  select:*  from:entities  caption:%name%  value:type", array("All" => "")); ?>
		<?php category_select("status  onchange:uris_grid.filterChange(this)  nolabel:  taxonomy:statuses  optional:Any Status"); ?>
		<?php button("Search", "class:btn-default"); ?>
<?php close_form(); ?>
