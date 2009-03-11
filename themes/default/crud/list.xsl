<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
$<xsl:value-of select="@name"/> = $this->get("<xsl:value-of select="@name"/>");
$page = next($this->uri);
empty_nan($page, 0);
$all = $<xsl:value-of select="@name"/>->afind("*");
$total = $<xsl:value-of select="@name"/>->recordCount;
$list = $<xsl:value-of select="@name"/>->afind("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25");
$shown = $<xsl:value-of select="@name"/>->recordCount;
?&gt;
&lt;h2&gt;<xsl:value-of select="@name"/> list&lt;/h2&gt;
&lt;?php if ($total > 25) { ?&gt;
&lt;ul class="pages"&gt;
	&lt;?php if ($page > 0) { ?&gt;
	&lt;li class="back"&gt;&lt;a href="<xsl:value-of select="@name"/>/list/&lt;?php echo $page-1; ?&gt;"&gt;Back&lt;/a&gt;&lt;/li&gt;
	&lt;?php } for($i=0;$i&lt;ceil($total/25);$i++) { ?&gt;
	&lt;li&gt;&lt;a&lt;?php if($page == $i) { ?&gt; class="active"&lt;?php } ?&gt; href="<xsl:value-of select="@name"/>/list/&lt;?php echo $i; ?&gt;"&gt;&lt;?php echo $i+1; ?&gt;&lt;/a&gt;&lt;/li&gt;
	&lt;?php } if($page &lt; ceil($total/25)-1) { ?&gt;
	&lt;li class="next"&gt;&lt;a href="<xsl:value-of select="@name"/>/list/&lt;?php echo $page+1; ?&gt;"&gt;Next&lt;/a&gt;&lt;/li&gt;
	&lt;?php } ?&gt;
	&lt;/ul&gt;
&lt;?php } ?&gt;
&lt;ul id="<xsl:value-of select="@name"/>_list" class="lidls"&gt;
&lt;?php foreach($list as $entry) { ?&gt;
	&lt;li id ="<xsl:value-of select="@name"/>_&lt;?php echo $entry['id']; ?&gt;"&gt;
		&lt;h3&gt;
			&lt;form id="del_form" action="&lt;?php echo htmlentities($_SERVER['REQUEST_URI']); ?&gt;" method="post"&gt;
				&lt;input id="action[<xsl:value-of select="@name"/>]" name="action[<xsl:value-of select="@name"/>]" type="hidden" value="delete"/&gt;
				&lt;input type="hidden" name="<xsl:value-of select="@name"/>[id]" value="&lt;?php echo $entry['id']; ?&gt;"/&gt;
				&lt;input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="[X]"/&gt;
			&lt;/form&gt;
			&lt;a href="&lt;?php echo uri("<xsl:value-of select="@name"/>/update/$entry[id]"); ?&gt;"&gt;[edit]&lt;/a&gt;<xsl:apply-templates select="field[@label]"/>
		&lt;/h3&gt;
		&lt;dl id="<xsl:value-of select="@name"/>_&lt;?php echo $entry['id']; ?&gt;_list" style="padding:5px" class="hidden"&gt;
			<xsl:apply-templates select="field[@display]"/>
		&lt;/dl&gt;
	&lt;/li&gt;
&lt;?php } ?&gt;
&lt;/ul&gt;
&lt;a id="add_<xsl:value-of select="@name"/>" class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>/create"); ?&gt;"&gt;new <xsl:value-of select="@name"/>&lt;/a&gt;
</xsl:template>
<xsl:template match="/model/field">
<xsl:choose>
<xsl:when test="@label">
			&lt;a class="title" href="#"&gt;&lt;?php echo $entry['<xsl:value-of select="@name"/>']; ?&gt;&lt;/a&gt;</xsl:when>
<xsl:when test="@display='true'">
			&lt;dt&gt;<xsl:value-of select="@name"/>&lt;/dt&gt;&lt;dd&gt;&lt;?php echo $entry['<xsl:value-of select="@name"/>']; ?&gt;&lt;/dd&gt;</xsl:when>
<xsl:when test="@display='pass'">
			&lt;dt&gt;<xsl:value-of select="@name"/>&lt;/dt&gt;&lt;dd&gt;******&lt;/dd&gt;</xsl:when>
</xsl:choose>
</xsl:template>
</xsl:stylesheet>
