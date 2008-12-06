<?php
$file = fopen(dirname(__FILE__)."/../../app/models/".$argv[2].".php", "w");
fwrite($file, "<?php\nclass ".$argv[2]." extends Table {\n\n}\n?>");
fclose($file);
?>