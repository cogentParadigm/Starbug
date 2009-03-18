<?php $page=next($this->uri); if (file_exists("core/app/nouns/permits/$page.php")) include("core/app/nouns/permits/$page.php"); else include("core/app/nouns/permits/list.php"); ?>
