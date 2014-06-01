<?
/**
 * Forms are renderered with the following assigned variables
 * $attributes - html attribute string
 * $model - the model (if this form is associated with a model)
 * $action - the function of the model
 * $url - the URL to submit to
 * $method - the HTTP method (get or post)
 * $postback - the path to post back to if there is an error
 * $fields - field values
 * $errors - errors if there are any
 */
?>
<form <?= $attributes; ?>action="<?= $url; ?>" method="<?= $method; ?>" accept-charset="UTF-8">
<? if ($method == "post") { ?>
	<input class="postback" name="postback" type="hidden" value="<?= $postback; ?>" />
<? } ?>
<? if (!empty($action)) { ?>
	<input class="action" name="action[<?= $model; ?>]" type="hidden" value="<?= $action; ?>" />
<? } ?>
<? if (!empty($fields['id'])) { ?>
	<input id="id" name="<?= $model; ?>[id]" type="hidden" value="<?= $fields['id']; ?>" />
<? } ?>
