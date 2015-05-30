<?php
	$this->displays->render("CsvDisplay", array_merge($api_request->options, array("model" => $api_request->model, "action" => $api_request->action, "data" => $api_request->data, "template" => "csv")));
?>
