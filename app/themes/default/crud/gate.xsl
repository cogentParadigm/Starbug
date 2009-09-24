<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text"/>

<xsl:template match="/model">&lt;?php
include("app/nouns/header.php");
include($this-&gt;file);
include("app/nouns/footer.php");
?&gt;
</xsl:template>
</xsl:stylesheet>
