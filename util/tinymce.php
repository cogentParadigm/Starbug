<?php $sb->provide("util/tinymce"); ?>
<!-- TinyMCE -->
<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<!--<script type="text/javascript" src="<?php echo uri("core/app/public/js/tiny_mce/tiny_mce.js"); ?>"></script>-->
<script type="text/javascript">
	function tiny_mce_browser_callback(field_name, url, type, win){
					window.SetUrl=function(url,width,height,caption){
					 var input_field = dojo.byId(field_name, win.document);
					 dojo.attr(input_field, 'value', url);
					 if(caption){
									dojo.attr(input_field, 'alt', caption);
					 }
					}
					window.open('<?php echo uri("admin/media?modal=true"); ?>','media','modal,width=800,height=600');
	}
	tinyMCE.init({
		// General options
		selector : "textarea.rich-text",
		theme : "modern",
    plugins: [
        "advlist autolink autoresize textcolor lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen charmap",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste"
    ],

    toolbar1: "undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | print preview",
    image_advtab: true,
		file_browser_callback: tiny_mce_browser_callback,
		relative_urls : false,
		remove_script_host : false,
		document_base_url : "<?php echo uri("", 'u'); ?>"

		// Example content CSS (should be your site CSS)
		//content_css : "<?php echo uri("var/public/stylesheets/screen.css"); ?>"
	});
</script>
<!-- /TinyMCE -->
