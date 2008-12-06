<?php
$file = fopen(dirname(__FILE__)."/../../app/results/".$argv[2].".php", "w");
fwrite($file, "<?php\nclass ".$argv[2]." extends Result {\n\tfunction cause(\$key, \$data) {\n\t\t\n\t}\n}\n?>");
fclose($file);
?>