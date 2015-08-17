<?php
if (!empty($this->request->parameters['to'])) $this->request->parameters['to'] = uri($this->request->parameters['to']);
?>
<div id="content">
<?php
	if (logged_in()) redirect(uri());
	else {
		$this->assign("url", $this->request->parameters['to']);
		$this->displays->render("LoginForm");
	}
?>
</div>
