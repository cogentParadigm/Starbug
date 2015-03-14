<?php
	$view = empty($request->file) ? locate_view($request->uri) : $request->file;
	$this->render_view($view);
?>
