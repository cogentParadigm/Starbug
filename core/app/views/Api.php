<?php
include("core/ApiRequest.php");
if ($this->uri[1] == "call") { // Make multiple calls in one HTTP request
	$result = array();
	$output = false;
	efault($_POST['calls'], array());
	foreach ($_POST['calls'] as $idx => $call) {
		list($models, $query) = explode("  ", $call, 2);
		$request = new ApiRequest($models.".".$this->format, $query);
		if (empty($request->result)) $result[] = "$idx:[]";
		else {
			$result[] = $idx.": ".$request->result;
			$output = true;
		}
	}
	if ($output) echo "{ ".implode(", ", $result)." }";
} else { // Make a single call
	$query = "";
	foreach ($_GET as $k => $v) $query .= "$k:$v  ";
	$request = new ApiRequest($this->uri[1].".".$this->format, rtrim($query, ' '));
	if (!empty($request->result)) echo $request->result;
}
?>
