<?php 
/* containers: content,sidebar
 */
include("app/views/header.php"); ?>
			<div id="content">
				<?php $sb->get("uris")->render("content"); ?>
			</div>
			<div id="sidebar">
				<?php $sb->get("uris")->render("sidebar"); ?>
			</div>
<?php include("app/views/footer.php"); ?>
