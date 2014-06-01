<?php if (success("menus", "create")) { ?>
        <div class="success">Menu <?= (empty($_POST['menus']['id'])) ? "created" : "updated"; ?> successfully</div>
<?php } ?>
<div class="panel panel-default">
        <div class="panel-heading"><strong> <span data-i18n="Update Menu">Update Menu '<?php echo $menu; ?>'</span></strong></div>
        <div class="panel-body">
        <div class="clearfix">
                <p class="pull-right"><?php link_to("Add Menu Item <b class=\"fa fa-plus\"></b>", "admin/menus/create?menu=".$menu, "class:btn btn-default");?></p>
        </div>
        <?php render_display("grid", "menus", "tree",  array("menu" => $menu, "dnd" => true, "attributes" => array("base_url" => "admin/menus"))); ?>
        </div>
</div>
