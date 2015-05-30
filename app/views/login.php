<?php
if (!empty($_GET['to'])) $_GET['to'] = uri($_GET['to']);
?>
<div id="content">
<?php
	if (logged_in()) redirect(uri());
	else {
		$this->assign("url", $_GET['to']);
		$this->displays->render("LoginForm");
	}
?>
</div>
