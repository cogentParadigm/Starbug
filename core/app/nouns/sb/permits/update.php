<?php $id = next($this->uri); $_POST['permits'] = $sb->get("permits")->find("*", "id='$id'")->fields(); ?>
<h2>Update permits</h2>
<?php $formid = "edit_permits_form"; $action = "create"; $submit_to = uri("sb/permits/show/").$id; include("core/app/nouns/sb/permits/form.php"); ?>