<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?= strip_tags($request->payload['title']).' - '.Etc::WEBSITE_NAME; ?></title>
		<?php $sb->publish("head"); ?>
	</head>
	<body class="claro">
		<?php render("page"); ?>
		<?php $sb->publish("footer"); ?>
	</body>
</html>
