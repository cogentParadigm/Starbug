<?php
  $this->render_view("admin/update");
?>
<div class="panel panel-default">
  <div class="panel-heading"><strong> <span>Product Options</span></strong></div>
  <div class="panel-body">
    <?php
      $this->assign("model", "product_options");
      $this->assign("dialog", true);
      $this->render(["product_options/admin-toolbar", "admin-toolbar"]);
      $this->displays->render("ProductOptionsGrid", ["product_types_id" => $id, "attributes" => ["dialog" => "options_form"]]);
    ?>
  </div>
</div>
<div id="product_options_dialog" data-dojo-type="starbug/form/Dialog" data-dojo-id="product_options_form" data-dojo-props="url:'<?php echo $this->url->build("admin/product_options/"); ?>', callback:function(){product_options_grid.refresh();}, get_data:{'product_types_id':'<?php echo $id; ?>'}, post_data:{'product_options[product_types_id]':'<?php echo $id; ?>'}"></div>