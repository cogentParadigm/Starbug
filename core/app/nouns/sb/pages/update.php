<?php $id = next($this->uri); $_POST['pages'] = $sb->query("pages", "where:id='$id'	limit:1"); ?>
<h2>Update Page</h2>
<?php $formid = "edit_page_form"; $action = "create"; $submit_to = uri("pages"); include("core/app/nouns/sb/pages/form.php"); ?>
