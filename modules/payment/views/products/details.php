<h2><?php echo $product["name"]; ?></h2>
<div><?php echo $product["content"]; ?></div>
<a class="btn btn-primary" href="javascript:cart_dialog.add(<?php echo $product['id']; ?>);">Add To Cart</a>
<div data-dojo-type="payment/Dialog" data-dojo-id="cart_dialog"></div>
