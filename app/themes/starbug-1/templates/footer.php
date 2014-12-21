<div class="container">
    <div class="row">

				<div class="col-md-4">
						<div class="col">
							<h4>Browse</h4>
							<ul>
										<li><a href="<?php echo uri(); ?>">Blog</a></li>
										<li><a href="<?php echo uri(); ?>">Contact Us</a></li>
										<?php if (logged_in()) { ?>
											<li><a href="<?php echo uri("logout"); ?>">Log Out</a></li>
										<?php } else { ?>
											<li><a href="<?php echo uri("login"); ?>">Log In</a></li>
										<?php } ?>
								</ul>
						</div>
				</div>

        <div class="col-md-4">
            <div class="col">
                <h4>Mailing list</h4>
                <p>Sign up if you would like to receive occasional treats from us.</p>
                <form class="form-horizontal form-light">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Your email address...">
                        <span class="input-group-btn">
                            <button class="btn btn-base" type="button">Go!</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="col col-social-icons">
                <h4>Follow us</h4>
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-google-plus"></i></a>
                <a href="#"><i class="fa fa-linkedin"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-skype"></i></a>
                <a href="#"><i class="fa fa-pinterest"></i></a>
                <a href="#"><i class="fa fa-youtube-play"></i></a>
                <a href="#"><i class="fa fa-flickr"></i></a>
            </div>
        </div>

    </div>

    <hr>

    <div class="row">
        <div class="col-lg-9 copyright">
            2014 © Neon Rain Interactive. All rights reserved.
            <a href="#">Terms and conditions</a>
        </div>
        <div class="col-lg-3">
            <a href="http://starbugphp.com" title="Powered by StarbugPHP" target="_blank" class="">
                <img src="<?php echo uri("app/themes/starbug-1/public/images/logo-gray.png"); ?>" alt="StarbugPHP" class="pull-right">
            </a>
        </div>
    </div>
</div>
