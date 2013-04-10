<?php
	$options = schema($model);
	assign("action", "create");
	assign("url", (empty($uri) ? "" : uri($uri)));
	assign("fields", $options['fields']);
	efault($form_header, '<h1>New '.$options['singular_label'].'</h1>');
	
	echo $form_header;
	if (!empty($form)) render_form($form);
	else render("form");
?>
