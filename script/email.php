<?php
import("mailer");
$mailer = new mailer();
$next = array_shift($argv);
include(BASE_DIR."/script/email/$next/$next.php");
$result = $mailer->send();
echo $result;
?>
