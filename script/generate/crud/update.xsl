<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php content_top(); ?&gt;
	&lt;?php
		$id = end($this->uri);
		if (is_numeric($id)) efault($_POST['<xsl:value-of select="@name"/>'], query("<xsl:value-of select="@name"/>", "select:<xsl:value-of select="@name"/>.*  action:create  where:<xsl:value-of select="@name"/>.id='$id'  limit:1"));
		else {
			$_POST['<xsl:value-of select="@name"/>'] = query("<xsl:value-of select="@name"/>", "select:<xsl:value-of select="@name"/>.*  action:create  limit:1  orderby:<xsl:value-of select="@name"/>.created DESC");
			$id = $_POST['<xsl:value-of select="@name"/>']['id'];
		}
	?>
	&lt;h2&gt;Update <xsl:value-of select="@name"/>&lt;/h2&gt;
	&lt;?php $action = "create"; $submit_to = uri("<xsl:value-of select="@name"/>"); include("app/views/<xsl:value-of select="@name"/>/form.php"); ?>
&lt;?php content_bottom(); ?&gt;
</xsl:template>

</xsl:stylesheet>
