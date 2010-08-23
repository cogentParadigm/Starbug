<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php $id = next($this->uri); $_POST['<xsl:value-of select="@name"/>'] = $sb->query("<xsl:value-of select="@name"/>", "action:read  where:id='$id'  limit:1"); ?&gt;
&lt;h2&gt;Update <xsl:value-of select="@name"/>&lt;/h2&gt;
&lt;?php $action = "create"; $submit_to = uri("<xsl:value-of select="@name"/>"); include("app/views/<xsl:value-of select="@name"/>/<xsl:value-of select="@name"/>_form.php"); ?&gt;
</xsl:template>

</xsl:stylesheet>
