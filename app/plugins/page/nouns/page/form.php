<?php
$templates = array();
if ($handle = opendir("app/nouns/templates/")) {
	while (false !== ($file = readdir($handle))) if ((strpos($file, ".") !== 0)) $templates[] = substr($file, 0, strpos($file, "."));
	closedir($handle);
}
?>
		<?php sb::load("core/jsforms"); ?>

<form<?php if (!empty($formid)) echo " id=\"$formid\""; ?> class="page_form" action="<?php echo (empty($submit_to) ? $_SERVER['REQUEST_URI'] : $submit_to); ?>" method="post">
	<input class="action" name="action[page]" type="hidden" value="<?php echo $action; ?>" />
	<?php if (!empty($_POST['page']['id'])) { ?><input id="id" name="page[id]" type="hidden" value="<?php echo $_POST['page']['id']; ?>" /><?php } ?>
	<div class="field">
		<label for="name">Unique Name</label>
		<?php if (!empty($this->errors['page']['nameError'])) { ?><span class="error">Please enter a Name</span><?php } ?>
		<?php if (!empty($this->errors['page']['nameExistsError'])) { ?><span class="error">That Name already exists</span><?php } ?>
		<input class="text" id="name" name="page[name]" type="text" <?php if (!empty($_POST['page']['name'])) { ?> value="<?php echo $_POST['page']['name']; ?>"<?php } ?>/>
	</div>
	<div class="field">
		<label for="title">Title</label>
		<?php if (!empty($this->errors['page']['titleError'])) { ?><span class="error">Please enter a Title</span><?php } ?>
		<input class="text" id="title" name="page[title]" type="text" <?php if (!empty($_POST['page']['title'])) { ?> value="<?php echo $_POST['page']['title']; ?>"<?php } ?>/>
	</div>
	<div class="field" id="contentBox">
		<label for="content">Content</label>
		<?php if (!empty($this->errors['page']['contentError'])) { ?><span class="error">Please enter a Content</span><?php } ?>
		<textarea name="page[content]" id="page[content]" cols="100"><?php if (!empty($_POST['page']['content'])) echo $_POST['page']['content']; ?></textarea>
	</div>
	<div class="field">
		<label for="template">Template</label>
		<?php dfault($_POST['page']['template'], "Page"); ?>
		<select id="template" name="page[template]">
		<?php foreach ($templates as $t) { ?>
			<option value="<?php echo $t; ?>"<?php if ($_POST['page']['template'] == $t) { ?> selected="selected"<?php } ?>><?php echo $t; ?></option>
		<?php } ?>
		</select>
	</div>
	<div class="field">
		<label for="sort_order">Sort order</label>
		<?php if (!empty($this->errors['page']['sort_orderError'])) { ?><span class="error">Please enter a Sort order</span><?php } ?>
		<input class="text" id="sort_order" name="page[sort_order]" type="text" <?php if (!empty($_POST['page']['sort_order'])) { ?> value="<?php echo $_POST['page']['sort_order']; ?>"<?php } ?>/>
	</div>
	<div class="field">
		<label for="collective">Access</label>
		<select id="collective" name="page[collective]">
			<option value="0">everyone</option>
			<?php foreach ($this->groups as $name => $number) { ?>
			<option value="<?php echo $number; ?>"<?php if ($_POST['page']['collective'] == $number) { ?> selected="selected"<?php } ?>><?php echo $name; ?></option>
			<?php } ?>
		</select>
	</div>
	<div><input class="big button" type="submit" value="Save" /><a class="big button" href="<?php echo uri("page"); ?>">Cancel</a></div>
</form>
