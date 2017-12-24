<form <?php echo $this->filter->attributes($display->attributes); ?>>
  <?php if (!empty($display->model) && !empty($display->default_action) && $display->success($display->default_action)) { ?>
    <p class="alert alert-success">Saved</p>
  <?php } ?>
  <?php if ($display->errors("global")) { ?>
    <?php foreach ($display->errors("global", true) as $key => $value) { ?>
      <p class="alert alert-danger"><?php echo $value; ?></p>
    <?php } ?>
  <?php } ?>
<?php if ($display->method == "post") { ?>
  <input name="oid" type="hidden" value="<?php echo $this->filter->string($request->getCookie('oid')); ?>"/>
<?php } ?>
<?php $this->render("display/fields"); ?>
<?php if ($display->actions->template != "inline") { ?>
  <div class="form-actions">
<?php } ?>
        <?php
          foreach ($display->actions->fields as $name => $field) {
            $field['value'] = $name;
            $field['name'] = 'action';
            if (!empty($display->model)) $field['name'] .= '['.$display->model.']';
            $label = $field['label'];
            $ops = $field;
            if (empty($ops['type'])) $ops['type'] = "submit";
            $ops['class'] = ((empty($ops['class'])) ? "" : $ops['class']." ")."btn";
            echo '<button '.$this->filter->attributes($ops).'>'.$label.'</button>';
          }
        ?>
        <?php if (!empty($display->cancel_url)) { ?>
          <button type="button" class="cancel btn btn-danger" onclick="window.location='<?php echo $this->url->build($display->cancel_url); ?>'">Cancel</button>
        <?php } ?>
<?php if ($display->actions->template != "inline") { ?>
  </div>
<?php } ?>
</form>
