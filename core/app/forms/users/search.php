<?php open_form("method:get"); ?>
	<div class="inline">
		<?php text("keywords  nolabel:  div:text left  class:round-left"); ?>
		<?php select("group  onchange:form.submit()  nolabel:  div:select left", array_merge(array("Any Group" => ""), config("groups"))); ?>
		<?php select("status  options:Any Status,Enabled,Disabled  values:,4,1  onchange:form.submit()  nolabel:  div:select left"); ?>
		<?php button("Search", "class:left round-right"); ?>
	</div>
<?php close_form(); ?>
<br/>
