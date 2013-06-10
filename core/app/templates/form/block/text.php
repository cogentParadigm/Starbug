<?php
$_POST["block-$region-$position"] = query("uris,blocks", "select:blocks.*  where:uris.id='$id' && region='$region' && position='$position'  limit:1");
$form = new form("model:block-$region-$position");
echo $form->textarea("content  nolabel:true  id:block-$region-$position  class:rich-text  style:width:100%;height:100px");
?>
