<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
class <xsl:value-of select="@label"/> extends Table {

	var $defaults = array(<xsl:value-of select="defaults"/>);
	var $uniques = array(<xsl:value-of select="uniques"/>);
	var $lengths = array(<xsl:value-of select="lengths"/>);
	
	function create() {
		$<xsl:value-of select="@name"/> = $_POST['<xsl:value-of select="@name"/>'];
		<xsl:apply-templates select="field"/>
		/* more */
		return $this->store($<xsl:value-of select="/model/@name"/>);
	} 

	function delete() {
		return $this->remove("id='".$_POST['<xsl:value-of select="/model/@name"/>']['id']."'");
	}	
}
?>
</xsl:template>

<xsl:template match="field">
<xsl:choose>
	<xsl:when test="@type='bool'">		if (empty($<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'])) $<xsl:value-of select="/model/@name" />['<xsl:value-of select="@name"/>'] = 0;</xsl:when>
	<xsl:when test="@type='timestamp'">		$<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'] = date("Y-m-d H:i:s");</xsl:when>
	<xsl:when test="@type='datetime'">$<xsl:value-of select="@name"/>d = $<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'];
		<xsl:choose><xsl:when test="@time_select">$<xsl:value-of select="@name"/>t = $<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>_time'];
		unset($<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>_time']);
		if ($<xsl:value-of select="@name"/>t['ampm'] == 'pm') $<xsl:value-of select="@name"/>t['hour'] += 12;
		$<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'] = "$<xsl:value-of select="@name"/>d[year]-$<xsl:value-of select="@name"/>d[month]-$<xsl:value-of select="@name"/>d[day] $<xsl:value-of select="@name"/>t[hour]:$<xsl:value-of select="@name"/>t[minutes]:00";
		if (($<xsl:value-of select="@name"/>t['hour'] == -1) || ($<xsl:value-of select="@name"/>d['minutes'] == -1)) $<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'] = "";</xsl:when>
		<xsl:otherwise>$<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'] = "$<xsl:value-of select="@name"/>d[year]-$<xsl:value-of select="@name"/>d[month]-$<xsl:value-of select="@name"/>d[day] 00:00:00";</xsl:otherwise></xsl:choose>
		if (($<xsl:value-of select="@name"/>d['year'] == -1) || ($<xsl:value-of select="@name"/>d['month'] == -1) || ($<xsl:value-of select="@name"/>d['day'] == -1)) $<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'] = "";
		$_POST['<xsl:value-of select="/model/@name"/>']['<xsl:value-of select="@name"/>'] = $<xsl:value-of select="/model/@name"/>['<xsl:value-of select="@name"/>'];</xsl:when></xsl:choose></xsl:template>

</xsl:stylesheet>
