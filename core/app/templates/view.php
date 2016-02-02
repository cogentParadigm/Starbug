<?php
	$view = empty($request->file) ? $request->getComponent(0) : $request->file;
	$this->render_view($view);
?>
