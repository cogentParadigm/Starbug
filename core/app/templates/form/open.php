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
<form <?php echo $attributes; ?>action="<?php echo $url; ?>" method="<?php echo $method; ?>" accept-charset="UTF-8">
<?php if ($method == "post") { ?>
	<input class="postback" name="postback" type="hidden" value="<?php echo $postback; ?>" />
<?php } ?>
<?php if (!empty($action)) { ?>
	<input class="action" name="action[<?php echo $model; ?>]" type="hidden" value="<?php echo $action; ?>" />
<?php } ?>
<?php if (!empty($fields['id'])) { ?>
	<input id="id" name="<?php echo $model; ?>[id]" type="hidden" value="<?php echo $fields['id']; ?>" />
<?php } ?>
