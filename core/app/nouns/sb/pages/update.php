<?php $id = next($this->uri); $_POST['pages'] = $sb->query("pages", "where:id='$id'	limit:1"); ?>
<h2>Update Page</h2>
<?php $action = "update"; $submit_to = uri("pages/update"); include("core/app/nouns/sb/pages/form.php"); ?>
