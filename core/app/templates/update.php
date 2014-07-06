<?
	efault($action, "create");
	$options = schema($model);
	/*
	$record = (is_numeric($id)) ? query($model, "select:$model.*  action:$action  where:$model.id=?", array($id)) : query($model, "select:$model.*  action:$action  sort:$model.created DESC");	
	
	$refs = array();
	foreach ($options['fields'] as $name => $field) {
		if (sb()->db->has($field['type']) || $field['type'] == "category") {
			if (empty($field['column'])) $field['column'] = "id";
			$record->select($model.".".$name.".".$field['column']." as ".$name);
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
	*/
	efault($form_header, 'Update '.$options['singular_label']);
	
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
<?php
	render_display("form", $model, "form", array("action" => $action, "id" => $id, "cancel_url" => $cancel_url));
	//if (!empty($form)) render_form($form);
	//else render("form");
?>
	</div>
</div>
