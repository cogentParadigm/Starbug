<?php
	$view = empty($request->file) ? $request->uri[0] : $request->file;
	$this->render_view($view);
?>
