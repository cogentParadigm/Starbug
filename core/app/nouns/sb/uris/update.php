<?php $id = next($this->uri); $_POST['uris'] = $sb->query("uris", "action:read	where:id='$id'	limit:1"); ?>
<h2>Update URI</h2>
<?php $action = "create"; $submit_to = uri("sb/uris"); include("core/app/nouns/sb/uris/uris_form.php"); ?>
