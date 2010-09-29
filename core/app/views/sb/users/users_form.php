<?php
	$sb->import("util/form");
	$collectives = array_merge(array("everybody" => 0), $this->groups);
	$fu = new form("model:users  action:$action  url:$submit_to");
	echo $fu->open();
?>
	<div class="field"><?php echo $fu->text("email"); ?></div>
	<div class="field"><?php echo $fu->password("password  class:text"); ?></div>
	<div class="field"><?php echo $fu->text("memberships"); ?></div>
	<?php echo $fu->submit("class:big round button  value:Save"); ?>
</form>
