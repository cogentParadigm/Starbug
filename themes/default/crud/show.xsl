<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
	$id = next($this->uri);
	if (!empty($this->errors['<xsl:value-of select="@name"/>'])) include("app/nouns/<xsl:value-of select="@name"/>/".(($id)?"update":"create").".php");
		else {
		if (!$id) $entry = $this->get("<xsl:value-of select="@name"/>")->find("*", "", "LIMIT 1")->fields();
		else $entry = $this->get("<xsl:value-of select="@name"/>")->find("*", "id='".$id."'")->fields();
?&gt;<xsl:apply-templates select="field[@label]"/>
&lt;dl&gt;
<xsl:apply-templates select="field[@display]" />
	&lt;dt&gt;Options&lt;/dt&gt;
	&lt;dd&gt;
		&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/update/$entry[id]"); ?&gt;" style="float:left">Edit&lt;/a&gt;
		&lt;form id="del_form" action="&lt;?php echo uri("<xsl:value-of select="@name"/>/list"); ?&gt;" method="post"&gt;
			&lt;input name="action[<xsl:value-of select="@name"/>]" type="hidden" value="delete"/&gt;
			&lt;input type="hidden" name="<xsl:value-of select="@name"/>[id]" value="&lt;?php echo $entry['id']; ?&gt;"/&gt;
			&lt;input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?')" value="Delete"/&gt;
		&lt;/form&gt;
	&lt;/dd&gt;
&lt;/dl&gt;
&lt;?php } ?&gt;
</xsl:template>

<xsl:template match="/model/field">
<xsl:choose>
<xsl:when test="@label">
		&lt;h2&gt;&lt;?php echo $entry['<xsl:value-of select="@name"/>']; ?&gt;&lt;/h2&gt;</xsl:when>
<xsl:when test="@display='true'">
		&lt;dt&gt;<xsl:value-of select="@name"/>&lt;/dt&gt;&lt;dd&gt;&lt;?php echo $entry['<xsl:value-of select="@name"/>']; ?&gt;&lt;/dd&gt;</xsl:when>
<xsl:when test="@display='pass'">
		&lt;dt&gt;<xsl:value-of select="@name"/>&lt;/dt&gt;&lt;dd&gt;******&lt;/dd&gt;</xsl:when>
</xsl:choose>
</xsl:template>
</xsl:stylesheet>
