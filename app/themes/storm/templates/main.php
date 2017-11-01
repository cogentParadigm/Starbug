<!-- MAIN PANEL -->
<div id="main" role="main">

	<!-- RIBBON -->
	<div id="ribbon">

		<!-- collapse menu button -->
		<span class="ribbon-button-alignment">
			<span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu" style="margin:5px 10px 0 0"><i class="fa fa-reorder" style="color:#fff"></i></a> </span>
		</span>
		<!-- end collapse menu -->

		<!-- breadcrumb -->
		<ol class="breadcrumb">
			<?php foreach ($this->breadcrumbs as $crumb) { ?>
				<li>
					<?php if (!isset($crumb["href"])) { ?>
						<?php echo $crumb["title"]; ?>
					<?php } else { ?>
						<a href="<?php echo $this->url->build($crumb["href"]); ?>"><?php echo $crumb["title"]; ?></a>
					<?php } ?>
				</li>
			<?php } ?>
		</ol>
		<!-- end breadcrumb -->

	</div>
	<!-- END RIBBON -->

	<!-- MAIN CONTENT -->
	<div id="content">
		<?php $this->render("layout"); ?>
	</div>
	<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->
