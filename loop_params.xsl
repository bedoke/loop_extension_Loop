<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:functx="http://www.functx.com" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema">

    <xsl:param name="config_file">
        <xsl:value-of select="'loop_params.xml'"/>
    </xsl:param>
    
    <xsl:param name="pageheight">
		<xsl:value-of select="document($config_file)/config/pageheight"/>
    </xsl:param>
    
    <xsl:param name="pagewidth">
		<xsl:value-of select="document($config_file)/config/pagewidth"/>
    </xsl:param>
    

</xsl:stylesheet>