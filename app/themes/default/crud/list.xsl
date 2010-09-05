<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;h2&gt;&lt;a id="add_<xsl:value-of select="@name"/>" class="right round button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;"&gt;Create <xsl:value-of select="@name"/>&lt;/a&gt;<xsl:value-of select="@name"/>&lt;/h2&gt;
	&lt;?php
		$sb-&gt;import("util/form", "util/lister");
		efault($_GET['orderby'], "<xsl:value-of select="@name"/>.created");
		efault($_GET['direction'], "desc");
		echo form("method:get",
			"hidden  orderby", "hidden  direction", "text  keywords  class:left round-left", "submit  class:round-right button  value:Search"
		)."<br/>";
		$lister = new lister(
			"orderby:$_GET[orderby] $_GET[direction]  renderer:<xsl:value-of select="@name"/>_row  show:25  page:".end($this->uri),
			uri("<xsl:value-of select="@name"/>?page=[page]?keywords=$_GET[keywords]&amp;orderby=[orderby]&amp;direction=[direction]")
		);
<xsl:apply-templates select="field[@display]"/>
		$lister->query("<xsl:value-of select="@name"/>", "action:read  keywords:$_GET[keywords]  search:status<xsl:for-each select="field">,<xsl:value-of select="@name"/></xsl:for-each>");
		$lister->render("id:<xsl:value-of select="@name"/>_table  class:clear lister");
	?&gt;
&lt;a id="add_<xsl:value-of select="@name"/>" class="big left round button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;"&gt;Create <xsl:value-of select="@name"/>&lt;/a&gt;
</xsl:template>

<xsl:template match="/model/field">		$lister->add_column("<xsl:value-of select="@name"/>  sortable:");
</xsl:template>
</xsl:stylesheet>
