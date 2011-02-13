<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>
<xsl:template match="/model">&lt;?php
	open_form("model:<xsl:value-of select="@name"/>  action:$action  url:$submit_to", "id:<xsl:value-of select="@name"/>_form<xsl:if test="count(//field[@input_type='file_select']) &gt; 0">  enctype:multipart/form-data</xsl:if>");
?&gt;
<xsl:apply-templates select="/model/field"/>	&lt;div class="field"&gt;&lt;button class="left positive"&gt;Save&lt;/button&gt;&lt;button class="negative cancel button"&gt;Cancel&lt;/button&gt;&lt;/div&gt;
&lt;?php close_form(); ?&gt;</xsl:template>

<xsl:template match="field"><xsl:choose>
	<xsl:when test="@display = 'true'">	&lt;div class="field"&gt;&lt;?php <xsl:value-of select="@input_type"/>("<xsl:value-of select="@name"/><xsl:apply-templates select="@*"/><xsl:if test="@input_type = 'select'"><xsl:if test="filter[@name='alias']">  caption:<xsl:value-of select="filter[@name='alias']/@value"/>  value:<xsl:value-of select="substring-after(filter[@name='references']/@value, ' ')"/>  from:<xsl:value-of select="substring-before(filter[@name='references']/@value, ' ')"/></xsl:if></xsl:if>"); ?&gt;&lt;/div&gt;
</xsl:when>
	<xsl:otherwise>
		<xsl:if test="(@name != 'id') and (@name != 'owner') and (@name != 'collective') and (@name != 'status') and (@name != 'created') and (@name != 'modified')">	&lt;?php hidden("<xsl:value-of select="@name"/><xsl:apply-templates select="@*"/>"); ?&gt;
</xsl:if>
	</xsl:otherwise>
</xsl:choose></xsl:template>

<xsl:template match="@*"><xsl:if test="(name() != 'constraint') and (name() != 'null') and (name() != 'enctype') and (name() != 'name') and (name() != 'type') and (name() != 'input_type') and (name() != 'display') and (name() != 'alias') and (name() != 'references')"><xsl:text>  </xsl:text><xsl:value-of select="name()"/>:<xsl:value-of select="."/></xsl:if></xsl:template>

</xsl:stylesheet>
