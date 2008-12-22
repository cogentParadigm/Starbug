<?php
$file = fopen(dirname(__FILE__)."/../../db/migrations/".ucwords($argv[2])."Migration.php", "w");
fwrite($file, "<?php\nclass ".ucwords($argv[2])."Migration extends Migration {\n\n\tfunction describe() {\n\t\t\n\t}\n\n\tfunction up() {\n\t\t\n\t}\n\n\tfunction down() {\n\t\t\n\t}\n\n}\n?>");
fclose($file);
$file = fopen(dirname(__FILE__)."/../../db/migrations/list", "a");
fwrite($file, ucwords($argv[2])."\r\n");
fclose($file);
?>