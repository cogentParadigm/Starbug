<?php
	assign("model", $request->uri[1]);
	assign("id", $request->uri[2]);
	assign("uri", "list/".$request->uri[1]);
	render("update");
?>
	
