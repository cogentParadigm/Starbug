<?php $schema = schema($model); ?>
<a class="big round create button" href="<?php echo uri(str_replace("[action]", "create", $path)); ?>">New <?php echo $schema['singular_label']; ?></a>
