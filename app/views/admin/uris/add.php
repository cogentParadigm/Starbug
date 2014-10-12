<div class="panel panel-default">
        <div class="panel-heading"><strong>Add Content</strong></div>
        <div class="panel-body">
<?php $types = query("entities")->condition("base", "uris")->sort("name")->all(); foreach($types as $type) { ?>
					<div class="content_type">
						<p style="margin-bottom:0"><strong><a href="<?php echo uri("admin/uris/create?type=".$type['name']); ?>"><?php echo $type['singular_label']; ?></a></strong></p>
						<p><?php echo $type['description']; ?></p>
					</div>
<?php } ?>
        </div>
</div>
