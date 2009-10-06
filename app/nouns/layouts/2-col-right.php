<?php 
/* containers: content,sidebar
 */
include("app/nouns/header.php"); ?>
			<div id="content">
				<?php $sb->get("pages")->render("content"); ?>
			</div>
			<div id="sidebar">
				<?php $sb->get("pages")->render("sidebar"); ?>
			</div>
<?php include("app/nouns/footer.php"); ?>
