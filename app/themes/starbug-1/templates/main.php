<?php if (request()->path != "home") { ?>
<div class="pg-opt">
  <div class="container">
      <div class="row">
          <div class="col-md-6">
              <h2><?php echo request("title"); ?></h2>
          </div>
          <div class="col-md-6">
              <ol class="breadcrumb">
                  <li><a href="<?php echo uri(); ?>">Home</a></li>
                  <li class="active"><?php echo request()->title; ?></li>
              </ol>
          </div>
      </div>
  </div>
</div>
<?php } ?>
<?php $this->render("layout"); ?>
