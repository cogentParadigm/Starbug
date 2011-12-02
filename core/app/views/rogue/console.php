<?php
	if (!empty($_GET['time'])) $errors = query("errors", "where:created >= '".$_GET['time']."'");
	else if (!empty($_GET['last'])) $errors = query("errors", "orderby:created DESC  limit:".$_GET['last']);
	$html = '';
	foreach ($errors as $error) {
		$html .= '<div class="error '.$error['type'].'"><strong>'.$error['type'];
		if (!empty($error['action'])) $html .= '::'.$error['action'];
		$html .= ' '.$error['field'].':</strong> '.$error['message'].'</div>';
	}
	$output = array("time" => date("Y-m-d H:i:s"), "html" => $html);
	echo json_encode($output);
?>
