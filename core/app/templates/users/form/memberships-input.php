<?php
	$groups = config("groups");
	$memberships = intval($form->get("memberships"));
	foreach ($groups as $name => $number) {
		if ($memberships & $number) $number .= "  checked:checked";
		checkbox("groups[]  label:$name  style:clear:left  value:".$number);
		echo "<br/>";
	}
?>
<br/>