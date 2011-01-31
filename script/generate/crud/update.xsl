<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php if ($this->format != "xhr") { ?&gt;&lt;div id="content"&gt;&lt;?php } ?&gt;
	&lt;?php $id = end($this->uri); efault($_POST['<xsl:value-of select="@name"/>'], query("<xsl:value-of select="@name"/>", "select:<xsl:value-of select="@name"/>.*  action:create  where:<xsl:value-of select="@name"/>.id='$id'  limit:1")); ?>
	&lt;h2&gt;Update <xsl:value-of select="@name"/>&lt;/h2&gt;
	&lt;?php $action = "create"; $submit_to = uri("<xsl:value-of select="@name"/>"); include("app/views/<xsl:value-of select="@name"/>/form.php"); ?>
&lt;?php if ($this->format != "xhr") { ?&gt;&lt;/div&gt;&lt;?php } ?&gt;
</xsl:template>

</xsl:stylesheet>
