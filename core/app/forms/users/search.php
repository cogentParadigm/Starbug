<?php open_form("method:get", "class:form-inline"); ?>
		<?php text("keywords  nolabel:  class:round-left"); ?>
		<?php select("group  onchange:form.submit()  nolabel:", array_merge(array("Any Group" => ""), config("groups"))); ?>
		<?php select("status  options:Any Status,Enabled,Disabled  values:,4,1  onchange:form.submit()  nolabel:"); ?>
		<?php button("Search"); ?>
<?php close_form(); ?>
