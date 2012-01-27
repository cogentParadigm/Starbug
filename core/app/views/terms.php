<?php
	$taxonomy = urldecode(end($this->uri));
	$terms = terms($taxonomy);
	echo json_encode($terms);
?>
