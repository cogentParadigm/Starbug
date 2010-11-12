<?php $sb->provide("util/tinymce"); ?>
<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo uri("app/public/js/tiny_mce/tiny_mce.js"); ?>"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "pagebreak,style,table,save,advhr,advimage,advlink,iespell,insertdatetime,media,paste,fullscreen,xhtmlxtras,wordcount",

		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "<?php echo uri("var/public/stylesheets/screen.css"); ?>"
	});
</script>
<!-- /TinyMCE -->