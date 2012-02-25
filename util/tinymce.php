<?php $sb->provide("util/tinymce"); ?>
<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo uri("core/app/public/js/tiny_mce/tiny_mce.js"); ?>"></script>
<script type="text/javascript">
	function kfm_for_tiny_mce(field_name, url, type, win){
					window.SetUrl=function(url,width,height,caption){
					 var input_field = dojo.byId(field_name, win.document);
					 dojo.attr(input_field, 'value', '//'+url);
					 if(caption){
									dojo.attr(input_field, 'alt', caption);
					 }
					}
					window.open('<?php echo uri("core/app/public/js/tiny_mce/plugins/"); ?>kfm/index.php?mode=selector&type='+type,'kfm','modal,width=800,height=600');
	}
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,spellchecker,pagebreak,style,table,advlink,iespell,inlinepopups,insertdatetime,paste,fullscreen,xhtmlxtras",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,|,cut,copy,paste,pastetext,pasteword,|,forecolor,backcolor,|,cleanup,code,|,fullscreen",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,|,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,|,styleprops,spellchecker,attribs,|,blockquote,pagebreak",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		file_browser_callback: "kfm_for_tiny_mce",
		editor_deselector: "plain"

		// Example content CSS (should be your site CSS)
		//content_css : "<?php echo uri("var/public/stylesheets/screen.css"); ?>"
	});
</script>
<!-- /TinyMCE -->
