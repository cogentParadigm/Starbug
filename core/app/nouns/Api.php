<?php
include("core/db/ApiFunctions.php");
$model = next($this->uri);
$action = next($this->uri);
$action = explode(".", $action);
$format = $action[1];
$action = $action[0];
$query = "";
if ($action == "get") {
	if ((!empty($_POST['action'])) && (empty($sb->errors))) $_GET['id'] = (!empty($_POST[$model]['id'])) ? $_POST[$model]['id'] : $sb->insert_id;
	if (!empty($_GET['id'])) $query = "where:id='$_GET[id]'";
	echo $query;
	if ($format == "xml") {
		header("Content-Type: text/xml");
		echo ApiFunctions::getXML($model, $query);
	} else if ($format == "json") {
		header("Content-Type: application/json");
		echo ApiFunctions::getJSON($model, $query);
	} else if ($format == "jsonp") {
		header("Content-Type: application/x-javascript");
		echo ApiFunctions::getJSONP($model, $query);
	}
}
