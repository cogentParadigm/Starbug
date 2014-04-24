<?php open_form("method:get", "class:form-inline"); ?>
		<?php text("keywords  nolabel:  onkeyup:".$model."_grid.filterChange(this)"); ?>
		<?php button("Search", "class:btn-default"); ?>
<?php close_form(); ?>
