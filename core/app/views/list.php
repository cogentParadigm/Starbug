<?php
	assign("model", $request->uri[1]);
	assign("uri", "[action]/[model]");
	render("list");
?>
