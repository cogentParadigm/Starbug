<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text"/>

<xsl:include href="crud/gate.xsl"/>
<xsl:include href="crud/create.xsl"/>
<xsl:include href="crud/show.xsl"/>
<xsl:include href="crud/update.xsl"/>
<xsl:include href="crud/list.xsl"/>

<xsl:template match="/model">
<xsl:result-document method="text" href="gate.php">
<xsl:apply-templates select="." mode="gate"/>
</xsl:result-document>
<xsl:result-document method="html" href="create.php">
<xsl:apply-templates select="." mode="create"/>
</xsl:result-document>
<xsl:result-document method="text" href="show.php">
<xsl:apply-templates select="." mode="show"/>
</xsl:result-document>
<xsl:result-document method="text" href="update.php">
<xsl:apply-templates select="." mode="update"/>
</xsl:result-document>
<xsl:result-document method="text" href="list.php">
<xsl:apply-templates select="." mode="list"/>
</xsl:result-document>
</xsl:template>
</xsl:stylesheet>
