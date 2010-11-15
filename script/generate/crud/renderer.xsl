<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>
<xsl:template match="/model">&lt;tr id="<xsl:value-of select="@name"/>_&lt;?php echo $item[&apos;id&apos;]; ?&gt;"&gt;<xsl:apply-templates select="/model/field"/>
&lt;/tr&gt;</xsl:template>

<xsl:template match="field"><xsl:if test="@display = 'true'"><xsl:choose><xsl:when test="position() = 1">
	&lt;td&gt;
		&lt;a href="&lt;?php echo uri("<xsl:value-of select="/model/@name"/>/update/$item[id]"); ?&gt;"&gt;&lt;?php echo $item[&apos;<xsl:value-of select="@name"/>&apos;]; ?&gt;&lt;/a&gt;
		&lt;ul class="row-actions"&gt;
			&lt;li class="first"&gt;&lt;a href="&lt;?php echo uri("<xsl:value-of select="/model/@name"/>/update/$item[id]"); ?&gt;"&gt;edit&lt;/a&gt;&lt;/li&gt;
			&lt;li&gt;&lt;?php $_POST['<xsl:value-of select="/model/@name"/>'] = $item; echo form("model:<xsl:value-of select="/model/@name"/><xsl:text>  action:delete", "submit  class:link  value:delete"); </xsl:text>?&gt;&lt;/li&gt;
		&lt;/ul&gt;
	&lt;/td&gt;</xsl:when><xsl:otherwise>
	&lt;td&gt;&lt;?php echo $item[&apos;<xsl:value-of select="@name"/>&apos;]; ?&gt;&lt;/td&gt;</xsl:otherwise></xsl:choose></xsl:if></xsl:template>
</xsl:stylesheet>