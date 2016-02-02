<?php
if ($this->request->hasParameter('to')) $this->request->setParameter('to', uri($this->request->getParameter('to')));
?>
<div id="content">
<?php
	if ($this->user->loggedIn()) redirect(uri());
	else {
		$this->assign("url", $this->request->getParameter('to'));
		$this->displays->render("LoginForm");
	}
?>
</div>
