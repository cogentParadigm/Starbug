<?php
	$command = $_REQUEST['command'];
	$output = array();
	exec("git $command", $output);
	$search = array("#");
	$replace = array("");
	$data = array("staged" => array(), "modified" => array(), "untracked" => array());
	foreach ($output as $i => $o) $output[$i] = str_replace($search, $replace, $o);
	foreach ($output as $i => $o) if (false !== strpos($o, "(")) unset($output[$i]);
	$set = "";
	foreach ($output as $i => $o) {
		if (false !== strpos($o, "On branch")) $data['branch'] = str_replace("On branch ", "", $o);
		if (false !== strpos($o, "Changes to be committed:")) $set = "staged";
		else if (false !== strpos($o, "Changed but not updated:")) $set = "modified";
		else if (false !== strpos($o, "Untracked files:")) $set = "untracked";
		else if ((!empty($o)) && (!empty($set))) $data[$set][] = str_replace("modified: ", "", $o);
	}
	//if (!empty($output)) echo implode("<br/>", $output);
	header("Content-Type: application/json");
	echo json_encode($data);
?>