<?php
echo $id;
$_POST["block-$region-$position"] = $sb->query("uris,blocks", "select:blocks.*  where:uris.id='$id' && region='$region' && position='$position'  limit:1");
$form = new form("model:block-$region-$position");
echo $form->textarea("content  nolabel:true  id:block-$region-$position  style:width:100%");
?>
