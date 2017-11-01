<?php if ($request->format != "xhr") { ?>
<!DOCTYPE html>
<html lang="en">
	<head>
<?php $this->publish("head"); ?>
	</head>
	<body class="dbootstrap <?php echo $response->layout; ?>">
		<div class="page">
<?php } ?>
			<?php $this->render("layout"); ?>
<?php if ($request->format != "xhr") { ?>
		</div>
<?php $this->publish("footer"); ?>
	</body>
</html>
<?php } ?>
