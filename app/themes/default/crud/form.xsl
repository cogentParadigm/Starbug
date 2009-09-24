<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>
<xsl:template match="/form">&lt;?php
	$sb-&gt;import("util/form");
	$fields = array();
<xsl:apply-templates select="/form/field"/>	$fields["Save"] = array("type" => "submit", "class" => "big left button");
	echo form::render("<xsl:value-of select="@name"/>", "post", $action, $submit_to, $fields);
?&gt;</xsl:template>

<xsl:template match="field">	$<xsl:value-of select="@name"/>_errors = array(<xsl:apply-templates select="error"/>);
	$fields["<xsl:value-of select="@name"/>"] = array(<xsl:apply-templates select="@*"/>"errors" => $<xsl:value-of select="@name"/>_errors);</xsl:template>

<xsl:template match="@*">"<xsl:value-of select="name()"/>" => "<xsl:value-of select="."/>", </xsl:template>

<xsl:template match="error">"<xsl:value-of select="@name"/>" => "<xsl:value-of select="."/>"<xsl:if test="position() != last()">, </xsl:if></xsl:template>

</xsl:stylesheet>
