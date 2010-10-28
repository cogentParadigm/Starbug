<style type="text/css">
  @import "http://o.aolcdn.com/dojo/1.3/dojox/grid/resources/Grid.css";
  @import "http://o.aolcdn.com/dojo/1.3/dojox/grid/resources/tundraGrid.css";

  .dojoxGrid table {
    margin: 0;
  }
</style>
<script type="text/javascript">
	dojo.require("dojo.data.ItemFileReadStore");
	dojo.require("dojox.grid.DataGrid");
	function groupName(data, rowIndex) {
		console.log(data);
		switch (data) {
		<?php foreach ($this->groups as $name => $number) { ?>
			case "<?php echo $number; ?>":
				return "<?php echo $name; ?>";
		<?php } ?>
		}
	}
</script>
<div jsId="usersStore" dojoType="dojo.data.ItemFileReadStore"
	url="<?php echo uri("api/users/get.json?keywords=".urlencode($_GET['keywords'])."&search=".urlencode("username,email")); ?>"></div>
<h2><a id="add_user" class="right round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>Users</h2>
	<?php
		$sb->import("util/form");
		echo form("method:get", "text  keywords  class:left round-left", "submit  class:round-right button  value:Search")."<br/>";
	?>
	<table id="usergrid" style="width:100%" autoheight="true" dojoType="dojox.grid.DataGrid" store="usersStore">
		<thead>
			<tr>
				<th field="username" width="auto">Username</th>
				<th field="memberships" width="auto" formatter="groupName">Memberships</th>
			</tr>
		</thead>
	</table>
	<br class="clear" />
<a id="add_user" class="big left round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>