<?php if ($product) { ?>
	<div class="alert alert-success">The following item has been added to your cart</div>
	<div style="padding:15px">
		<?php echo $product['description']; ?>
		<br/>
		<a class="btn btn-default" href="<?php echo $this->url->build("checkout"); ?>" style="margin:5px 0;width:100%">Proceed to Checkout</a>
		<a class="btn btn-default hide-dialog" href="javascript:;" style="margin:5px 0;width:100%">Continue Shopping</a>
	</div>
<?php } else { ?>
	<div class="alert alert-danger">An unknown error ocurred</div>
<?php } ?>
