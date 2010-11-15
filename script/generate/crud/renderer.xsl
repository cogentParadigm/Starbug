<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>
<xsl:template match="/model">&lt;tr id="<xsl:value-of select="@name"/>_&lt;?php echo $item[&apos;id&apos;]; ?&gt;"&gt;<xsl:apply-templates select="/model/field[display=true]"/>
&lt;/tr&gt;</xsl:template>

<xsl:template match="field">
	&lt;td&gt;
		<xsl:choose>
			<xsl:when test="position() = 1">&lt;a href="&lt;?php echo uri("<xsl:value-of select="/model/@name"/>/update/$item[id]"); ?&gt;"&gt;&lt;?php echo $item[&apos;<xsl:value-of select="@name"/>&apos;]; ?&gt;&lt;/a&gt;
			&lt;ul class="row-actions"&gt;
				&lt;li class="first"&gt;&lt;a href="&lt;?php echo uri("<xsl:value-of select="/model/@name"/>/update/$item[id]"); ?&gt;"&gt;edit&lt;/a&gt;&lt;/li&gt;
				&lt;li&gt;&lt;?php $_POST['<xsl:value-of select="/model/@name"/>'] = $item; echo form("model:<xsl:value-of select="/model/@name"/><xsl:text>  action:delete", "submit  class:link  value:delete"); </xsl:text>?&gt;&lt;/li&gt;
			&lt;/ul&gt;</xsl:when>
			<xsl:otherwise>&lt;?php echo $item[&apos;<xsl:value-of select="@name"/>&apos;]; ?&gt;</xsl:otherwise>
		</xsl:choose>
	&lt;/td&gt;
</xsl:template>
</xsl:stylesheet>