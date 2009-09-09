<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>

<xsl:template match="/form">&lt;form&lt;?php if (!empty($formid)) echo " id=\"$formid\""; ?&gt; class="<xsl:value-of select="@name"/>_form" action="&lt;?php echo (empty($submit_to) ? $_SERVER['REQUEST_URI'] : $submit_to); ?&gt;" method="<xsl:value-of select="@method"/>"<xsl:if test="@multipart='true'"> enctype="multipart/form-data"</xsl:if>&gt;
	&lt;input class="action" name="action[<xsl:value-of select="@name"/>]" type="hidden" value="&lt;?php echo $action; ?&gt;" /&gt;
	&lt;?php if (!empty($_POST['<xsl:value-of select="@name"/>']['id'])) { ?&gt;&lt;input id="id" name="<xsl:value-of select="@name"/>[id]" type="hidden" value="&lt;?php echo $_POST['<xsl:value-of select="@name"/>']['id']; ?&gt;" /&gt;&lt;?php } ?&gt;
<xsl:apply-templates select="/form/field"/>	&lt;div&gt;&lt;input class="button" type="submit" value="Save" /&gt;&lt;a class="button" href="&lt;?php echo uri("<xsl:value-of select="@name"/>"); ?&gt;">Cancel&lt;/a&gt;&lt;/div&gt;
&lt;/form&gt;
</xsl:template>

<xsl:template match="field">	&lt;div class="field"&gt;<xsl:if test="@type='checkbox'"><xsl:call-template name="input"/></xsl:if><xsl:if test="@label"><xsl:call-template name="label"/></xsl:if><xsl:apply-templates select="error"/><xsl:if test="@type!='checkbox'"><xsl:call-template name="input"/></xsl:if>
	&lt;/div&gt;
</xsl:template>

<xsl:template name="input">
<xsl:choose>
	<xsl:when test="@type='text' or @type='password' or @type='hidden' or @type='submit' or @type='file' or @type='image'">
		&lt;input class="text" id="<xsl:value-of select="@id"/>" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>]" type="<xsl:value-of select="@type"/>" &lt;?php if (!empty($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>'])) { ?&gt; value="&lt;?php echo $_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']; ?&gt;"&lt;?php } <xsl:choose>
			<xsl:when test="@default">else { ?&gt; value="<xsl:value-of select="@default"/>"&lt;?php } ?&gt;<xsl:if test="@onfocus"> onfocus="if(this.value=="<xsl:value-of select="@default"/>"){this.value="";}else{this.select();this.focus();}"</xsl:if></xsl:when>
			<xsl:otherwise>?&gt;</xsl:otherwise></xsl:choose>/&gt;</xsl:when>
	<xsl:when test="@type='checkbox'">
		&lt;input class="checkbox" id="<xsl:value-of select="@id"/>" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>]" type="<xsl:value-of select="@type"/>"&lt;?php if (isset($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>'])) { ?&gt; value="&lt;?php echo $_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']; ?>"&lt;?php } <xsl:choose>
			<xsl:when test="@default">else { ?&gt; value="<xsl:value-of select="@default"/>"&lt;?php } ?&gt;</xsl:when>
			<xsl:otherwise>?&gt;</xsl:otherwise></xsl:choose>/&gt;</xsl:when>
	<xsl:when test="@type='select'">
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>'], "<xsl:value-of select="@default"/>"); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>]"&gt;<xsl:apply-templates select="option"/>
		&lt;/select&gt;</xsl:when>
	<xsl:when test="@type='textarea'">
		&lt;textarea id="<xsl:value-of select="@name"/>" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>]" cols="<xsl:value-of select="if (@cols) then @cols else '30'"/>" rows="<xsl:value-of select="if (@rows) then @rows else '10'"/>"&gt;&lt;?php if (!empty($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>'])) echo $_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']; <xsl:choose>
			<xsl:when test="@default">else echo "<xsl:value-of select="@default"/>"; ?&gt;</xsl:when>
			<xsl:otherwise> ?&gt;</xsl:otherwise></xsl:choose>&lt;/textarea&gt;</xsl:when>
	<xsl:when test="@type='date_select'"><xsl:call-template name="date_select"/></xsl:when>
	<xsl:when test="@type='time_select'"><xsl:call-template name="time_select"/></xsl:when></xsl:choose></xsl:template>

<xsl:template name="label">
		&lt;label for="<xsl:value-of select="@id"/>"&gt;<xsl:value-of select="@label"/>&lt;/label&gt;</xsl:template>

<xsl:template match="error">
		&lt;?php if (!empty($this->errors['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>Error'])) { ?&gt;&lt;span class="error"&gt;<xsl:value-of select="."/>&lt;/span&gt;&lt;?php } ?&gt;</xsl:template>

<xsl:template match="option">
		&lt;option value="<xsl:value-of select="@name"/>"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="parent::field/@name"/>'] == "<xsl:value-of select="@name"/>") { ?&gt; selected="true"&lt;?php } ?&gt;&gt;<xsl:value-of select="."/>&lt;/option&gt;</xsl:template>

<xsl:template name="date_select">
		&lt;?php
		$year = date("Y");
		$year_options = array("Year" => "-1", $year => $year, (((int) $year)+1) => (((int) $year)+1));
		$month_options = array("Month" => "-1", "January" => "1", "February" => "2", "March" => "3", "April" => "4", "May" => "5", "June" => "6", "July" => "7", "August" => "8", "September" => "9", "October" => "10", "November" => "11", "December" => "12");
		$day_options = array("Day" => "-1");
		for($i=1;$i&lt;32;$i++) $day_options["$i"] = $i;
		if (!empty($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>'])) {
			$<xsl:value-of select="@name"/>_dt = strtotime($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']);
			$_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>'] = array("year" => date("Y", $<xsl:value-of select="@name"/>_dt), "month" => date("m", $<xsl:value-of select="@name"/>_dt), "day" => date("d", $<xsl:value-of select="@name"/>_dt));
			<xsl:if test="@time_select">			$_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>_time'] = array("hour" => date("h", $<xsl:value-of select="@name"/>_dt), "minutes" => date("i", $<xsl:value-of select="@name"/>_dt), "ampm" => date("a", $<xsl:value-of select="@name"/>_dt));</xsl:if>
		}
		?&gt;
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['month'], date("m", strtotime(<xsl:value-of select="@default"/>))); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>-mm" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>][month]"&gt;
		&lt;?php foreach ($month_options as $caption => $val) { ?&gt;
		&lt;option value="&lt;?php echo $val ?&gt;"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['month'] == $val) { ?&gt; selected="true"&lt;?php } ?&gt;&gt;&lt;?php echo $caption; ?&gt;&lt;/option&gt;
		&lt;?php } ?&gt;
		&lt;/select&gt;
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['day'], date("d", strtotime(<xsl:value-of select="@default"/>))); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>-dd" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>][day]"&gt;
		&lt;?php foreach ($day_options as $caption => $val) { ?&gt;
		&lt;option value="&lt;?php echo $val; ?&gt;"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['day'] == $val) { ?&gt; selected="true"&lt;?php } ?&gt;&gt;&lt;?php echo $caption; ?&gt;&lt;/option&gt;
		&lt;?php } ?&gt;
		&lt;/select&gt;
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['month'], date("m", strtotime(<xsl:value-of select="@default"/>))); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>" class="split-date range-low-&lt;?php echo date("Y-m-d"); ?&gt; no-transparency" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>][year]"&gt;
		&lt;?php foreach ($year_options as $caption => $val) { ?&gt;
		&lt;option value="&lt;?php echo $val; ?&gt;"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['year'] == $val) { ?&gt; selected="true"&lt;?php } ?&gt;&gt;&lt;?php echo $caption; ?&gt;&lt;/option&gt;
		&lt;?php } ?&gt;
		&lt;/select&gt;<xsl:if test="@time_select"><xsl:call-template name="time_select"/></xsl:if></xsl:template>
		
<xsl:template name="time_select">
		&lt;?php
		$hour_options = array("Hour" => "-1");
		for($i=1;$i&lt;13;$i++) $hour_options[$i] = $i;
		$minutes_options = array("Minutes" => "-1", "00" => "00", "15" => "15", "30" => "30", "45" => "45");
		$ampm_options = array("AM" => "am", "PM" => "pm");
		?&gt;
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['hour'], date("H", strtotime(<xsl:value-of select="@default"/>))); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>-hour" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>][hour]"&gt;
		&lt;?php foreach ($hour_options as $caption => $val) { ?&gt;
		&lt;option value="&lt;?php echo $val; ?&gt;"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['hour'] == $val) { ?&gt; selected="true"&lt;?php } ?&gt;&gt;&lt;?php echo $caption; ?&gt;&lt;/option&gt;
		&lt;?php } ?&gt;
		&lt;/select&gt;
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['minutes'], date("i", strtotime(<xsl:value-of select="@default"/>))); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>-minutes" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>][minutes]"&gt;
		&lt;?php foreach ($minutes_options as $caption => $val) { ?&gt;
		&lt;option value="&lt;?php echo $val; ?&gt;"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['minutes'] == "$val") { ?&gt; selected="true"&lt;?php } ?&gt;&gt;&lt;?php echo $caption; ?&gt;&lt;/option&gt;
		&lt;?php } ?&gt;
		&lt;/select&gt;
		<xsl:if test="@default">		&lt;?php dfault($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['ampm'], date("a", strtotime(<xsl:value-of select="@default"/>))); ?&gt;</xsl:if>
		&lt;select id="<xsl:value-of select="@id"/>" name="<xsl:value-of select="/form/@name"/>[<xsl:value-of select="@name"/>][ampm]"&gt;
		&lt;?php foreach ($ampm_options as $caption => $val) { ?&gt;
		&lt;option value="&lt;?php echo $val; ?&gt;"&lt;?php if ($_POST['<xsl:value-of select="/form/@name"/>']['<xsl:value-of select="@name"/>']['ampm'] == "$val") { ?&gt; selected="true"&lt;?php } ?&gt;&gt;&lt;?php echo $caption; ?&gt;&lt;/option&gt;
		&lt;?php } ?&gt;
		&lt;/select&gt;</xsl:template>

</xsl:stylesheet>
