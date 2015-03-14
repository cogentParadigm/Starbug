<!DOCTYPE html>
<html lang="en">
	<head>
		<?php sb()->publish("head"); ?>
	</head>
	<body class="dbootstrap">
		<?php $this->render("page"); ?>
		<?php sb()->publish("footer"); ?>
	</body>
</html>
