<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns="http://www.tei-c.org/ns/1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:FGEA="http://rotunda.upress.virginia.edu/FGEA" version="2.0"
    xmlns:mhs="http://www.masshist.org/ns/1.0">
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="no"
        exclude-result-prefixes="tei FGEA mhs"/>

    <xsl:strip-space elements="none"/>

    <xsl:param name="index"/>
    

    <xsl:template match="*"></xsl:template>
    
    <xsl:template match="/">
        <delete>
            <xsl:apply-templates select="//tei:TEI"/>
            <xsl:apply-templates select="//tei:div[@type='doc']"/>
        </delete>
    </xsl:template>

    <xsl:template match="tei:TEI | tei:div[@type='doc']">
        <query>id:(<xsl:value-of select="$index"/>-<xsl:value-of select="./@xml:id"/>)</query>

    </xsl:template>


</xsl:stylesheet>
