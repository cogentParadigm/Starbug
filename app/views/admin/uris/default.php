<?php
	assign("query", "uris  where:prefix='app/views/' && owner<>1");
	render("list");
?>
