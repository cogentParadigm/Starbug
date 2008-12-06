<?php if (!file_exists("app/elements/".$page.".php")) $page = "Missing";
if (file_exists("app/elements/".$page.".php")) include("app/elements/".$page.".php"); ?>