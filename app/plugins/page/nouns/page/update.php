<?php $id = next($this->uri); $_POST['page'] = $this->get("page")->get("*", "id='$id'")->fields(); ?>
<h2>Update page</h2>
<?php $formid = "edit_page_form"; $action = "create"; $submit_to = uri("page"); include("app/nouns/page/form.php"); ?>
