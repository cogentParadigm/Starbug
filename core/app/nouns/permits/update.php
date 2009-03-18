<?php $id = next($this->uri); $_POST['permits'] = $this->get("permits")->find("*", "id='$id'")->fields(); ?>
<h2>Update permits</h2>
<?php $formid = "edit_permits_form"; $action = "create"; $submit_to = uri("permits/show/").$id; include("app/nouns/permits/permits_form.php"); ?>
