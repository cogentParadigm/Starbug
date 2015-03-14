<h1>New Page</h1>
<?php
	$this->assign("action", "create");
	$this->assign("uri", "admin/uris/update");
	$this->render_form("uris");
?>
