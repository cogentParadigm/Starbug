<div class="field">
	<? render("form/label"); ?>

	<?
		if (!empty($errors[$field])) {
			foreach ($errors[$field] as $error => $message) {
				assign("error", $message);
				render("form/error");
			}
		}
	?>
<? render("form/$control"); ?>
</div>
