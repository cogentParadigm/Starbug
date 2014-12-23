<div class="pg-opt">
  <div class="container">
      <div class="row">
          <div class="col-md-6">
              <h2><?php echo request("title"); ?></h2>
          </div>
          <div class="col-md-6">
              <ol class="breadcrumb">
                  <li><a href="#">Home</a></li>
                  <li><a href="#">Pages</a></li>
                  <li class="active">About us</li>
              </ol>
          </div>
      </div>
  </div>
</div>
<div class="container">
	<div class="section">
		<? render_view(); ?>
	</div>
</div>
