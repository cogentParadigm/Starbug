<?php
// for absolute path http://www.mysite.com/upload/ or for relative path upload/ 
include("../../../../etc/Etc.php");
define("_path", Etc::WEBSITE_URL."public/uploads/"); // absolute path
define("_folder", "../../../../public/uploads/"); // relative path
?>
