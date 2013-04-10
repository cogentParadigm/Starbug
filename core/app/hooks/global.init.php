<?php
//autoload settings
$settings = query("settings", "select:name,value  where:autoload=1");
foreach ($settings as $s) cache("settings-".$s['name'], $s['value']);
?>
