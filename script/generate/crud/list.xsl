<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;div id="content"&gt;
	&lt;h2&gt;&lt;a class="add_<xsl:value-of select="@name"/> big right round button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;" title="New <xsl:value-of select="@name"/>"&gt;New <xsl:value-of select="@name"/>&lt;/a&gt;<xsl:value-of select="@name"/>&lt;/h2&gt;
	&lt;?php open_form("method:get"); ?&gt;
		&lt;?php text("keywords  nolabel:  class:left round-left"); ?&gt;
		&lt;button class="round-right" style="padding-top:6px;padding-bottom:5px;margin-top:0.5em"&gt;Search&lt;/button&gt;
	&lt;?php close_form(); ?&gt;
	&lt;br/&gt;
	&lt;?php
		$sb->import("util/grid");
		$grid = new grid(
			"model:<xsl:value-of select="@name"/>",
			"keywords:$_GET[keywords]  search:status<xsl:for-each select="field">,<xsl:value-of select="@name"/></xsl:for-each>  select:<xsl:value-of select="@name"/>.*"
		);
		<xsl:apply-templates select="/model/field[@display='true']"/>
		$grid->add_column("id  width:84  formatter:row_options", "Options");
		$grid->render();
		$action = uri("api/<xsl:value-of select="@name"/>/get.json");
		remote_form(".add_<xsl:value-of select="@name"/>", "action:'$action'  callback:dojo.hitch(<xsl:value-of select="@name"/>-grid, 'reloadStore')");
		remote_form(".edit_<xsl:value-of select="@name"/>", "action:'$action'  callback:dojo.hitch(<xsl:value-of select="@name"/>-grid, 'reloadStore')");
	?&gt;
	&lt;a class="add_<xsl:value-of select="@name"/> big left round button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;" title="New <xsl:value-of select="@name"/>"&gt;New <xsl:value-of select="@name"/>&lt;/a&gt;
&lt;/div&gt;
&lt;script type="text/javascript"&gt;
		function row_options(data, rowIndex) {
			var text = '&lt;a class="edit_<xsl:value-of select="@name"/> button" title="Update <xsl:value-of select="@name"/>" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/update/"); ?&gt;'+data+'"&gt;&lt;img src="&lt;?php echo uri("core/app/public/icons/file-edit.png"); ?&gt;"/&gt;&lt;/a&gt;';
			text += '&lt;form method="post" onsubmit="return confirm(\'are you sure you want to delete this item?\');"&gt;&lt;input type="hidden" name="action[<xsl:value-of select="@name"/>]" value="delete"/&gt;&lt;input type="hidden" name="<xsl:value-of select="@name"/>[id]" value="'+data+'"/&gt;&lt;button class="negative" title="delete"&gt;&lt;img src="&lt;?php echo uri("core/app/public/icons/cross.png"); ?&gt;"/&gt;&lt;/button&gt;&lt;/form&gt;';
			return text;
		}
		dojo.addOnLoad(function() {
			setTimeout(dojo.hitch(dojo.behavior, 'apply'), 1000);
		});
&lt;/script&gt;
</xsl:template>

<xsl:template match="field">		$grid->add_column("<xsl:value-of select="@name"/>  width:auto");
</xsl:template>
</xsl:stylesheet>
