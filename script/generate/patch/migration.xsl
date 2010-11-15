<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>
<xsl:template match="/models">&lt;?php
/**
 * core migration
 * 
 * @file app/migrations/CoreMigration.php
 * @ingroup migrations
 */
class CoreMigration extends Migration {

	function up() {<xsl:for-each select="model">
		$this->table("<xsl:value-of select="@name"/>",<xsl:for-each select="field">
			"<xsl:value-of select="@name"/><xsl:for-each select="@*"><xsl:if test="(name() != 'name') and (name() != 'input_type') and (name() != 'display')"><xsl:text>  </xsl:text><xsl:value-of select="name()"/>:<xsl:value-of select="."/></xsl:if></xsl:for-each><xsl:for-each select="filter"><xsl:text>  </xsl:text><xsl:value-of select="@name"/>:<xsl:value-of select="@value"/></xsl:for-each>"<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>
		);<xsl:for-each select="action">
		$this->permit("<xsl:value-of select="parent::model/@name"/>::<xsl:value-of select="@name"/>", "<xsl:for-each select="@*"><xsl:if test="name()!='name'"><xsl:value-of select="name()"/>:<xsl:value-of select="."/></xsl:if><xsl:if test="position()!=last()"><xsl:text>  </xsl:text></xsl:if></xsl:for-each>");</xsl:for-each><xsl:for-each select="uri">
		$this->uri("<xsl:value-of select="@path"/>", "<xsl:for-each select="@*"><xsl:value-of select="name()"/>:<xsl:value-of select="."/><xsl:if test="position()!=last()"><xsl:text>  </xsl:text></xsl:if></xsl:for-each>");</xsl:for-each></xsl:for-each>
	}

	function down() {<xsl:for-each select="model">
		$this->drop("<xsl:value-of select="@name"/>");</xsl:for-each>
	}

}
?>
</xsl:template>
</xsl:stylesheet>
