<?php $id = next($this->uri); $_POST['uris'] = (!empty($id)) ? $sb->query("uris", "action:read  where:id='$id'  limit:1") : $sb->query("uris", "where:1 ORDER BY id DESC  limit:1"); ?>
<h2>Update URI</h2>
<?php $action = "update"; $submit_to = uri("sb/uris/update"); include("core/app/views/sb/uris/uris_form.php"); ?>
