<script type="text/javascript">
	function switch_icon(text) {
		if (text == '--') return '&crarr;';
		else return '--';
	}
</script>
<h2><a id="add_uri" class="right round button" href="<?php echo uri("sb/uris/create"); ?>">Create URI</a>URIs</h2>
<?php
		$sb->import("util/form", "util/lister", "util/dojo");
		efault($_GET['orderby'], "modified");
		efault($_GET['direction'], "desc");
		efault($_GET['page'], "1");
		echo form("method:get",
			"hidden  orderby", "hidden  direction", "text  keywords  id:uris-keywords  class:left round-left", "submit  class:round-right button  value:Search"
		)."<br/>";
		$lister = new lister(
			"orderby:$_GET[orderby] $_GET[direction]  renderer:uris_row  show:25  page:".end($this->uri),
			uri("sb-admin?page[page]?keywords=$_GET[keywords]&orderby=[orderby]&direction=[direction]")
		);
		$lister->add_column("expand  caption:");
		$lister->add_column("title  sortable:");
		$lister->add_column("modified  caption:Status  sortable:");
		$uris = $sb->query("uris", "action:read  keywords:$_GET[keywords]  search:title,path  orderby:$_GET[orderby] $_GET[direction]");
		global $kids;
		$kids = array();
		foreach($uris as $uri) $kids[$uri['parent']][] = $uri;
		$lister->items($kids[0]);
		$lister->render("id:uris_table  class:clear lister");
?>
<?php if ($this->uri[1] == "uris") { ?>
<a id="add_uri" class="big left round button" href="<?php echo uri("sb/uris/create"); ?>">Create URI</a>
<?php } ?>
