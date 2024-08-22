<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    version="1.0">

    
    <xsl:template match="@rend">
        <xsl:variable name="attrName">
            <xsl:choose>
                <xsl:when test="starts-with(., 'CSS')">style</xsl:when>
                <xsl:when test="contains(.,':')">style</xsl:when>
                <xsl:otherwise>class</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        
        <xsl:variable name="contents">
            <xsl:choose>
                <xsl:when test="contains(., 'CSS(')">
                    <xsl:value-of select="translate(substring-after(., 'CSS('), ')', ' ')"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="."/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        
        <xsl:attribute name="{$attrName}">
            <xsl:value-of select="$contents"/>
        </xsl:attribute>
    </xsl:template>
    
    
    
    
    <xsl:template match="@xml:id">
        <xsl:attribute name="id">
            <xsl:value-of select="."/>
        </xsl:attribute>
    </xsl:template>
 
    
</xsl:stylesheet>