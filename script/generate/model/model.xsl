<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
/**
 * <xsl:value-of select="@name"/> model
 * @ingroup models
 */
class <xsl:value-of select="@label"/> extends <xsl:value-of select="@label"/>Model {

	function create() {
		$<xsl:value-of select="@name"/> = $_POST['<xsl:value-of select="@name"/>'];
		return $this->store($<xsl:value-of select="@name"/>);
	}

	function delete() {
		return $this->remove('id='.$_POST['<xsl:value-of select="@name"/>']['id']);
	}

}
?>
</xsl:template>

</xsl:stylesheet>
