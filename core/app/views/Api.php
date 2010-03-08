<?php
include("core/db/ApiFunctions.php");
$model = next($this->uri);
if (false !== strpos($model, ".")) {
	$models = explode(".", $model);
	$model = $models[0];
} else $models = array($model);
$action = next($this->uri);
$action = explode(".", $action);
$format = $action[1];
$action = $action[0];
$query = "";
if ($action == "get") {
	if ((!empty($_POST['action'][$model])) && (empty($sb->errors[$model]))) $_GET['id'] = (!empty($_POST[$model]['id'])) ? $_POST[$model]['id'] : $sb->insert_id;
	if ($model == "permits") $_GET['id'] = $sb->insert_id;
	if (!empty($_GET['id'])) $query = "where:$model.id='$_GET[id]'";
	if ($format == "xml") {
		header("Content-Type: text/xml");
		echo ApiFunctions::getXML($models, $query);
	} else if ($format == "json") {
		header("Content-Type: application/json");
		echo ApiFunctions::getJSON($models, $query);
	} else if ($format == "jsonp") {
		header("Content-Type: application/x-javascript");
		echo ApiFunctions::getJSONP($models, $query);
	}
}
