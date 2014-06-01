<?php open_form("method:get", "class:form-inline"); ?>
		<?php text("keywords  nolabel:  class:round-left  onkeyup:users_grid.filterChange(this)"); ?>
		<?php category_select("group  onchange:users_grid.filterChange(this)  taxonomy:groups  nolabel:  optional:Any Group"); ?>
		<?php category_select("status  taxonomy:statuses  optional:Any Status  onchange:users_grid.filterChange(this)  nolabel:"); ?>
		<?php button("Search", "class:btn-default"); ?>
<?php close_form(); ?>
