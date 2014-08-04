<div class="panel panel-default">
        <div class="panel-heading"><strong>Add Content</strong></div>
        <div class="panel-body">
<?php $types = query("content_types")->condition("base", "uris")->sort("name")->all(); foreach($types as $type) { ?>
					<div class="content_type">
						<p style="margin-bottom:0"><strong><a href="<?php echo uri("admin/uris/create?type=".$type['type']); ?>"><?php echo $type['name']; ?></a></strong></p>
						<p><?php echo $type['description']; ?></p>
					</div>
<?php } ?>
        </div>
</div>

