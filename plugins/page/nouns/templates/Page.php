<?php
$pages = $this->get("page");
$name = current($this->uri);
$content = $pages->get("*", "name='$name'")->fields();
$description = substr(strip_tags($content['content']), 0, 200)."...";
include("app/nouns/header.php");
echo "<h2>$content[title]</h2>";
echo $content['content'];
include("app/nouns/footer.php");
?>
