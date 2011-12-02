<?php
	assign("model", $request->uri[1]);
	assign("uri", "list/".$request->uri[1]);
	render("create");
?>
