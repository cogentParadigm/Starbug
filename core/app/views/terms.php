<?php
	$taxonomy = urldecode(end($request->getComponents()));
	$terms = terms($taxonomy);
	echo json_encode($terms);
?>
