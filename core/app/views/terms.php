<?php
	$taxonomy = urldecode(end($request->uri));
	$terms = terms($taxonomy);
	echo json_encode($terms);
?>
