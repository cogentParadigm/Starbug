<?php
	$command = $_REQUEST['command'];
	$result = array();
	$output = array();
	if ($command != "status") exec("git $command", $result);
	exec("git status", $output);
	$search = array("#");
	$replace = array("");
	$data = array("staged" => array(), "modified" => array(), "untracked" => array(), "branches" => array(), "diff" => "0", "output" => implode("<br/>", $result));
	foreach ($output as $i => $o) $output[$i] = str_replace($search, $replace, $o);
	foreach ($output as $i => $o) if (false !== strpos($o, "(")) unset($output[$i]);
	$set = "";
	foreach ($output as $i => $o) {
		if (false !== strpos($o, "On branch")) $data['branch'] = str_replace("On branch ", "", $o);
		else if (false !== strpos($o, "Your branch is ahead of")) {
			$data['diff'] = explode(" ", trim($o));
			$data['diff'] = $data['diff'][7];
		} else if (false !== strpos($o, "Your branch is behind")) {
			$data['diff'] = explode(" ", trim($o));
			$data['diff'] = $data['diff'][6];
		} else if (false !== strpos($o, "Changes to be committed:")) $set = "staged";
		else if (false !== strpos($o, "Changes not staged for commit:")) $set = "modified";
		else if (false !== strpos($o, "Untracked files:")) $set = "untracked";
		else if ((!empty($o)) && (!empty($set))) $data[$set][] = trim(str_replace("modified: ", "", $o));
	}
	$output = array();
	exec("git branch", $output);
	foreach($output as $i => $o) $data["branches"][] = array("name" => end(explode(" ", trim($o))));
	//if (!empty($output)) echo implode("<br/>", $output);
	header("Content-Type: application/json");
	echo json_encode($data);
?>
