	<?php
		$sb->import("util/form");
		$fields = array();
		$path_errors = array("path" => "Please enter a path name.");
		$fields["path"] = array("type" => "text", "errors" => $path_errors);
		$template_errors = array("template" => "Please enter a template.");
		$fields["template"] = array("type" => "text", "errors" => $template_errors);
		$visibility_errors = array("visibility" => "Please select a visibility.");
		$fields["visibility"] = array("type" => "select", "range" => "0:1", "default" => "1", "errors" => $visibility_errors);
		$importance_errors = array("importance" => "Please select an importance.");
		$fields["importance"] = array("type" => "select", "range" => "0:10", "errors" => $importance_errors);
		$collective_errors = array("collective" => "Please select a collective.");
		$fields["collective"] = array("type" => "select", "options" => array_merge(array("everybody" => 0), $this->groups), "errors" => $collective_errors);
		$fields["Save"] = array("type" => "submit", "class" => "big left button");
		echo Form::render("uris", "post", $action, $submit_to, $fields);
	?>
