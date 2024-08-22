<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns="http://www.tei-c.org/ns/1.0"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    exclude-result-prefixes="#default xsl tei w"
    version="1.0">
    <!--     xpath-default-namespace="http://www.tei-c.org/ns/1.0" -->    
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="no"/>
    
    <xsl:strip-space elements="*"/>
    
    <xsl:param name="xmlid">fakeid</xsl:param>

    <xsl:template match="w:document">
        <TEI>
            <xsl:attribute name="xml:id"><xsl:value-of select="$xmlid"/></xsl:attribute>
            <text>    
            <xsl:apply-templates select="w:body"/>
            </text>
        </TEI>
    </xsl:template>
    
    <xsl:template match="w:body">
        <body>
            <xsl:apply-templates />
        </body>
    </xsl:template>
    
    <xsl:template match="w:p">
        <xsl:if test="string-length(.) > 0">
            <p>
                <xsl:apply-templates />
            </p>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="w:r">
        <!-- this variable holds all the non-whitespace characters -->
        <xsl:variable name="trimmedText"><xsl:value-of select="translate(normalize-space(.), ' ', '')"/></xsl:variable>
        <xsl:choose>
            <xsl:when test="string-length(.) > 0">
                
                <xsl:choose>
                    <!-- if there are only whitespace characters, than skip any wrapping element -->
                    <xsl:when test="string-length($trimmedText) = 0"><xsl:value-of select="."/></xsl:when>
                    
                    <!-- superscript -->
                    <xsl:when test=".//w:vertAlign[@w:val='superscript']"><hi rend="superscript"><xsl:apply-templates/></hi></xsl:when>
                    
                    <!-- italic -->
                    <xsl:when test=".//w:i"><hi rend="italic"><xsl:apply-templates/></hi></xsl:when>
                    
                    <!-- underline -->
                    <xsl:when test=".//w:u[@w:val='single']"><hi rend="underline"><xsl:apply-templates/></hi></xsl:when>
                    
                    <!-- strikethrough -->
                    <xsl:when test=".//w:strike[not(@w:val) or @w:val='true']"><del><xsl:apply-templates/></del></xsl:when>
                    
                    <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:when test="//w:t[@xml:space='preserve']">
                <xsl:text> </xsl:text>
            </xsl:when>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="w:t">
        <xsl:value-of select="."/>
    </xsl:template>
    
    <xsl:template match="w:br">
        <lb/>
    </xsl:template>
   
</xsl:stylesheet>