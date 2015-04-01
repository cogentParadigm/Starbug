<?php
	foreach ($tokens as $name => $token) {
		if ($name === "token") {
			$entity = query("entities")->condition("name", $data['uris']['type'])->one();
			$pattern = (empty($entity['url_pattern'])) ? "[uris:path]" : $entity['url_pattern'];
			$replacements[$token] = $this->replace($pattern, $data);
		}
	}
?>
