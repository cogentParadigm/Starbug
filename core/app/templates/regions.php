<?php foreach($this->config->get("info.regions", "themes/".$response->theme) as $region) $this->render("region", array("region" => $region)); ?>
