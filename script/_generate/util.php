<?php
$file = fopen(dirname(__FILE__)."/../../util/".$argv[2].".php", "w");
fwrite($file, "<?php\nclass ".$argv[2]." {\n\n\t\n\n}\n?>");
fclose($file);
?>