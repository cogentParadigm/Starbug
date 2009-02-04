function new_element() {
	dojo.xhrGet({
		url: '<?php echo uri("elements/new"); ?>',
		load: function (data) {
			dojo.byId('elements_table').innerHTML += data;
		}
	});
}
function save_new() {
	dojo.xhrPost({
		url: '<?php echo uri("elements/get"); ?>',
		form: 'new_element_form',
		load: function(data) {
			cancel_new();
			dojo.byId('elements_table').innerHTML += data;
		}
	});
}
function cancel_new() {
	var newrow = dojo.byId('new_element');
	newrow.parentNode.removeChild(newrow);
}
function edit_element(id) {
	dojo.xhrGet({
		url: '<?php echo uri("elements/edit/"); ?>'+id,
		load: function(data) {
			dojo.byId('element_'+id).innerHTML = data;
		}
	});
}
function save_edit(id) {
	dojo.xhrPost({
		url: '<?php echo uri("elements/get/"); ?>'+id,
		form: 'edit_element_form',
		load: function(data) {
			dojo.byId('element_'+id).innerHTML = data;
		}
	});
}
function cancel_edit(id) {
	dojo.xhrGet({
		url: '<?php echo uri("elements/get/"); ?>'+id,
		load: function(data) {
			dojo.byId('element_'+id).innerHTML = data;
		}
	});
}