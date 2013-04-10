<?
	efault($action, "create");
	$record = (is_numeric($id)) ? query($model, "select:$model.*  action:$action  where:$model.id=?  limit:1", array($id)) : query($model, "select:$model.*  action:$action  limit:1  orderby:$model.created DESC");	
	efault($_POST[$model], array());
	foreach ($record as $k => $v) dfault($_POST[$model][$k], $v);
	if (!empty($_POST[$model]['id'])) $id = $_POST[$model]['id'];
		
	$options = schema($model);
	assign("model", $model);
	assign("action", $action);
	assign("url", (empty($uri) ? "" : uri($uri)));
	assign("fields", $options['fields']);
	efault($form_header, '<h1>Update '.$options['singular_label'].'</h1>');
	
	echo $form_header;
	if (!empty($form)) render_form($form);
	else render("form");
?>
