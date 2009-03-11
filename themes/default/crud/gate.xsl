<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php $page=next($this->uri); if (file_exists("app/nouns/<xsl:value-of select="@name"/>/$page.php")) include("app/nouns/<xsl:value-of select="@name"/>/$page.php"); else include("app/nouns/<xsl:value-of select="@name"/>/list.php"); ?&gt;
</xsl:template>
</xsl:stylesheet>
