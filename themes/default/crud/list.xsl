<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
$<xsl:value-of select="@name"/> = $this->get("<xsl:value-of select="@name"/>");
$all = $<xsl:value-of select="@name"/>->get("*");
$total = $<xsl:value-of select="@name"/>->recordCount;
if (!empty($this->errors['<xsl:value-of select="@name"/>'])) {
	$id = next($this->uri);
	include("app/nouns/<xsl:value-of select="@name"/>/".(($id)?"update":"create").".php");
} else if ($total == 0) { ?&gt;
	&lt;h2&gt;<xsl:value-of select="@name"/> list&lt;/h2&gt;
	&lt;p&gt;Nothing to display&lt;/p&gt;
	&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;"&gt;new&lt;/a&gt;
&lt;?php } else {
	$page = next($this->uri);
	empty_nan($page, 0);
	$list = $<xsl:value-of select="@name"/>->get("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25")->GetRows();
	$shown = $<xsl:value-of select="@name"/>->recordCount;
	?&gt;
	&lt;h2&gt;<xsl:value-of select="@name"/> list&lt;/h2&gt;
	&lt;?php if ($total > 25) { ?&gt;
	&lt;ul class="pages"&gt;
		&lt;?php if ($page > 0) { ?&gt;
		&lt;li class="back"&gt;&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/").($page-1); ?&gt;"&gt;Back&lt;/a&gt;&lt;/li&gt;
		&lt;?php } for($i=0;$i&lt;ceil($total/25);$i++) { ?&gt;
		&lt;li&gt;&lt;a class="button&lt;?php if($page == $i) { ?&gt; active&lt;?php } ?&gt;" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/").$i; ?&gt;"&gt;&lt;?php echo $i+1; ?&gt;&lt;/a&gt;&lt;/li&gt;
		&lt;?php } if($page &lt; ceil($total/25)-1) { ?&gt;
		&lt;li class="next"&gt;&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/").($page+1); ?&gt;"&gt;Next&lt;/a&gt;&lt;/li&gt;
		&lt;?php } ?&gt;
		&lt;/ul&gt;
	&lt;?php } ?&gt;
	&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;"&gt;new&lt;/a&gt;
	&lt;table class="clear" id="<xsl:value-of select="@name"/>_list"&gt;
	&lt;tr&gt;
	<xsl:for-each select="field">&lt;th&gt;<xsl:value-of select="@name"/>&lt;/th&gt;</xsl:for-each>&lt;th&gt;options&lt;/th&gt;
	&lt;/tr&gt;
	&lt;?php foreach($list as $entry) { ?&gt;
		&lt;tr id ="<xsl:value-of select="@name"/>_&lt;?php echo $entry['id']; ?&gt;"&gt;
			<xsl:apply-templates select="field[@display]"/>
			&lt;td&gt;
				&lt;form class="left" id="del_form" action="&lt;?php echo htmlentities($_SERVER['REQUEST_URI']); ?&gt;" method="post"&gt;
					&lt;input id="action[<xsl:value-of select="@name"/>]" name="action[<xsl:value-of select="@name"/>]" type="hidden" value="delete"/&gt;
					&lt;input type="hidden" name="<xsl:value-of select="@name"/>[id]" value="&lt;?php echo $entry['id']; ?&gt;"/&gt;
					&lt;input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="delete"/&gt;
				&lt;/form&gt;
				&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/update/$entry[id]"); ?&gt;"&gt;edit&lt;/a&gt;<xsl:apply-templates select="field[@label]"/>
			&lt;/td&gt;
		&lt;/tr&gt;
	&lt;?php } ?&gt;
	&lt;/table&gt;
&lt;?php } ?&gt;
</xsl:template>
<xsl:template match="/model/field">
<xsl:choose>
<xsl:when test="@display='true'">
		&lt;td&gt;&lt;?php echo $entry['<xsl:value-of select="@name"/>']; ?&gt;&lt;/td&gt;</xsl:when>
<xsl:when test="@display='pass'">
		&lt;td&gt;******&lt;/td&gt;</xsl:when>
</xsl:choose>
</xsl:template>
</xsl:stylesheet>
