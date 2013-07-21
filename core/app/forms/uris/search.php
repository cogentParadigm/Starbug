<?php open_form("method:get", "class:form-inline"); ?>
		<?php text("keywords  nolabel:  class:round-left  onkeyup:uris_grid.filterChange(this)"); ?>
		<?php select("type  onchange:uris_grid.filterChange(this)  nolabel:  options:All,Pages,Views  values:,Page,View"); ?>
		<?php button("Search"); ?>
<?php close_form(); ?>
