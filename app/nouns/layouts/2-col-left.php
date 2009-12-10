<?php 
/* containers: content,sidebar
 */
include("app/nouns/header.php"); ?>
			<div id="sidebar">
				<?php $sb->get("pages")->render("sidebar"); ?>
			</div>
			<div id="content">
				<?php $sb->get("pages")->render("content"); ?>
			</div>
<?php include("app/nouns/footer.php"); ?>
