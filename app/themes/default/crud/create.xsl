<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html"/>

<xsl:template match="/model">
<h2>Create <xsl:value-of select="@name"/></h2>
<xsl:text disable-output-escaping="yes">
&lt;?php $action = "create"; $submit_to = uri("</xsl:text>
<xsl:value-of select="@name"/>
<xsl:text disable-output-escaping="yes">"); include("app/views/</xsl:text>
<xsl:value-of select="@name"/>
<xsl:text disable-output-escaping="yes">/form.php"); ?&gt;</xsl:text>
</xsl:template>
</xsl:stylesheet>
