<h2>Complete Order</h2>
<div data-dojo-type="payment/Cart" data-dojo-props="mode:'payment', order:<?php echo str_replace('"', "'", json_encode($cart->getOrder())); ?>"></div>
<div style="clear:both"></div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Shipping Address</h3>
			</div>
			<div class="panel-body">
				<?php
					echo $this->models->get("address")->format($cart->get('shipping_address'));
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Billing Address</h3>
			</div>
			<div class="panel-body">
				<?php
					if ($cart->get("billing_same")) {
						echo "Same as shipping address";
					} else {
						echo $this->models->get("address")->format($cart->get('billing_address'));
					}
				?>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
<?php $this->displays->render("PaymentForm", array("id" => $cart->get("id"))); ?>
	</div>
</div>
<?php if ($this->url->getScheme() == "https") { ?>
<div style="clear:both">
	<table width=130 border=0 cellspacing=0 cellpadding=0 title="CLICK TO VERIFY: This site uses a GlobalSign SSL Certificate to secure your personal information." ><tr><td><span id="ss_img_wrapper_130-65_flash_en"><a href="http://www.globalsign.com/" target=_blank title="SSL"><img alt="SSL" border=0 id="ss_img" src="//seal.globalsign.com/SiteSeal/images/gs_noscript_130-65_en.gif"></a></span><script type="text/javascript" src="//seal.globalsign.com/SiteSeal/gs_flash_130-65_en.js" ></script><br /><a href="http://www.globalsign.com/" target=_blank  style="color:#000000; text-decoration:none; font:bold 8px arial; margin:0px; padding:0px;">SSL</a></td></tr></table>
</div>
<?php } ?>
