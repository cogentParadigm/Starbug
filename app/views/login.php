<?php
if ($this->request->hasParameter('to')) $this->request->setParameter('to', $this->url->build($this->request->getParameter('to')));
?>
<div id="content">
<?php
	$this->assign("url", $this->request->getParameter('to'));
	$this->displays->render("LoginForm");
?>
</div>
