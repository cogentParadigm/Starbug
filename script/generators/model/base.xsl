<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
/**
 * <xsl:value-of select="@name"/> model
 * 
 * @package <xsl:value-of select="@package"/>
 * @subpackage models
 */
class <xsl:value-of select="@label"/>Model extends Table {

	var $filters = array(<xsl:for-each select="field[filter]">
		"<xsl:value-of select="@name"/>" => "<xsl:for-each select="filter"><xsl:value-of select="@name"/>:<xsl:value-of select="@value"/><xsl:if test="position()!=last()"><xsl:text>  </xsl:text></xsl:if></xsl:for-each>"<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>
	);

	function onload() {<xsl:for-each select="field[references]">
		$this->has_one("<xsl:value-of select="references/@model"/>", "<xsl:value-of select="@name"/>");</xsl:for-each><xsl:for-each select="relation">
		$this->has_many("<xsl:value-of select="@model"/>", "<xsl:value-of select="@field"/>"<xsl:if test="@lookup">, "<xsl:value-of select="@lookup"/>", "<xsl:value-of select="@ref_field"/>"</xsl:if>);</xsl:for-each>
	}

}
?>
</xsl:template>

</xsl:stylesheet>
