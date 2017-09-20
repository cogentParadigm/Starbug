<div class="row">
<div class="col-md-6">
  <h2><?php echo $product["name"]; ?></h2>
  <div><?php echo $product["content"]; ?></div>
</div>
<div class="col-md-6">
  <?php $this->displays->render("ProductConfigurationForm", ["id" => $product["id"], "type" => $product["type"]]); ?>
</div>
</div>
