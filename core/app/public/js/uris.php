<script type="text/javascript">
function new_uri() {
	dojo.xhrGet({
		url: '<?php echo uri("ajax/uris/new"); ?>',
		load: function (data) {
			dojo.byId('uris_table').innerHTML += data;
		}
	});
}
function save_new() {
	dojo.xhrPost({
		url: '<?php echo uri("ajax/uris/get"); ?>',
		form: 'new_uri_form',
		load: function(data) {
			cancel_new();
			dojo.byId('uris_table').innerHTML += data;
		}
	});
}
function cancel_new() {
	var newrow = dojo.byId('new_uri');
	newrow.parentNode.removeChild(newrow);
}
function edit_uri(id) {
	dojo.xhrGet({
		url: '<?php echo uri("ajax/uris/edit/"); ?>'+id,
		load: function(data) {
			dojo.byId('uri_'+id).innerHTML = data;
		}
	});
}
function save_edit(id) {
	dojo.xhrPost({
		url: '<?php echo uri("ajax/uris/get/"); ?>'+id,
		form: 'edit_uri_form',
		load: function(data) {
			dojo.byId('uri_'+id).innerHTML = data;
		}
	});
}
function cancel_edit(id) {
	dojo.xhrGet({
		url: '<?php echo uri("ajax/uris/get/"); ?>'+id,
		load: function(data) {
			dojo.byId('uri_'+id).innerHTML = data;
		}
	});
}
</script>
