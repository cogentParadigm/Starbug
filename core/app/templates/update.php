<?
	if (is_numeric($id)) efault($_POST[$model], query($model, "select:$model.*  action:create  where:$model.id='$id'  limit:1"));
	else {
		$_POST[$model] = query($model, "select:$model.*  action:create  limit:1  orderby:$model.created DESC");
		$id = $_POST[$model]['id'];
	}
	$options = schema($model);
	assign("model", $model);
	assign("action", "create");
	assign("url", uri($uri));
	assign("fields", $options['fields']);
?>
	<h1>New <?= $options['singular_label']; ?></h1>
	<? render("form"); ?>
