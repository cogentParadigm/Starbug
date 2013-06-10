<?php open_form("method:get", "class:form-inline"); ?>
		<?php text("keywords  nolabel:  class:round-left  onkeyup:users_grid.filterChange(this)"); ?>
		<?php select("group  onchange:users_grid.filterChange(this)  nolabel:", array_merge(array("Any Group" => ""), config("groups"))); ?>
		<?php select("status  options:Any Status,Enabled,Disabled  values:,4,1  onchange:users_grid.filterChange(this)  nolabel:"); ?>
		<?php button("Search"); ?>
<?php close_form(); ?>
