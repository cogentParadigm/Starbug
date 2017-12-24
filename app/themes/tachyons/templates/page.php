<div class="page layout--<?php echo $response->layout; ?>">
  <header class="pa4 flex-ns justify-between-ns items-center-ns">
    <h1 class="mt0 mb0-ns tc"><a class="link green dim" href="<?php echo $this->url->build(""); ?>"><?php echo $this->settings->get("site_name"); ?></a></h1>
    <nav id="navigation">
      <ul class="list pl0 ma0 flex justify-center items-center">
        <li><a class="active btn white bg-green bn link" href="<?php echo $this->url->build(""); ?>">Home</a></li>
        <?php if ($this->user->loggedIn()) { ?>
          <li class="ml2"><a class="btn green bn hover-bg-green hover-white link" href="<?php echo $this->url->build("logout"); ?>">Log Out</a></li>
        <?php } else { ?>
          <li class="ml2"><a class="btn green bn hover-bg-green hover-white link" href="<?php echo $this->url->build("login"); ?>">Log In</a></li>
        <?php } ?>
      </ul>
    </nav>
  </header>
  <div id="content" class="pa4">
    <?php $this->render("layout"); ?>
  </div>
</div>