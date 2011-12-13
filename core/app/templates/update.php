<?
	$record = (is_numeric($id)) ? query($model, "select:$model.*  action:create  where:$model.id=?  limit:1", array($id)) : query($model, "select:$model.*  action:create  limit:1  orderby:$model.created DESC");	
	efault($_POST[$model], array());
	foreach ($record as $k => $v) dfault($_POST[$model][$k], $v);
	if (!empty($_POST[$model]['id'])) $id = $_POST[$model]['id'];
		
	$options = schema($model);
	assign("model", $model);
	assign("action", "create");
	assign("url", uri($uri));
	assign("fields", $options['fields']);
?>
	<h1>Update <?= $options['singular_label']; ?></h1>
	<? render("form"); ?>
