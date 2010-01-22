<?php 
/* containers: content,sidebar
 */
include("app/views/header.php"); ?>
			<div id="sidebar">
				<?php $sb->get("uris")->render("sidebar"); ?>
			</div>
			<div id="content">
				<?php $sb->get("uris")->render("content"); ?>
			</div>
<?php include("app/views/footer.php"); ?>
