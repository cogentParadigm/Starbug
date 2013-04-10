<?php
	$record = get("terms", $id);
	efault($_POST['terms'], array());
	foreach ($record as $k => $v) efault($_POST['terms'][$k], $v);
	$taxonomy = $record['taxonomy'];
	assign("taxonomy", $taxonomy);
?>
<h1>Update Term</h1>
<?php
 render_form("terms");
?>
