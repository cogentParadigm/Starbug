<?
	efault($action, "create");
	$record = (is_numeric($id)) ? query($model, "select:$model.*  action:$action  where:$model.id=?", array($id)) : query($model, "select:$model.*  action:$action  orderby:$model.created DESC");	
	
	$options = schema($model);
	$refs = array();
	foreach ($options['fields'] as $name => $field) {
		if ($field['type'] == "terms" || $field['type'] == "category") {
			$record->select($model.".".$name.".id as ".$name);
			$refs[] = $name;
		}
	}
	$record = $record->one();
	foreach ($refs as $name) if (!is_array($record[$name])) $record[$name] = explode(",", $record[$name]);
	
	efault($_POST[$model], array());
	foreach ($record as $k => $v) dfault($_POST[$model][$k], $v);
	if (!empty($_POST[$model]['id'])) $id = $_POST[$model]['id'];
		
	assign("model", $model);
	assign("action", $action);
	assign("url", (empty($uri) ? "" : uri($uri)));
	assign("fields", $options['fields']);
	efault($form_header, '<h1>Update '.$options['singular_label'].'</h1>');
	
	echo $form_header;
	if (!empty($form)) render_form($form);
	else render("form");
?>
