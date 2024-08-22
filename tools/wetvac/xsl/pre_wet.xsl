<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns="http://www.tei-c.org/ns/1.0"
    exclude-result-prefixes="xs tei xsl #default"
   version="1.0">
    <xsl:strip-space elements="*"/>
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="no"/>

    <!-- This template includes EVERYTHING by default -->
    <xsl:template match="node()|@*">
        <xsl:copy>
            <xsl:apply-templates select="node()|@*"/>
        </xsl:copy>
    </xsl:template>


    <!-- remove dates from body -->
    <xsl:template match="tei:date">
        <xsl:if test="ancestor::tei:teiHeader">
            <xsl:copy><xsl:apply-templates/></xsl:copy>
        </xsl:if>
    </xsl:template>

    <xsl:template match="tei:p">
        <xsl:element name="p">
            <xsl:apply-templates />
        </xsl:element>
    </xsl:template>
    

    <xsl:template match="tei:hi">
        <xsl:choose>
            <xsl:when test="./@rend='strikethrough'">
                <xsl:element name="del">
                    <xsl:apply-templates />
                </xsl:element>
            </xsl:when>

            <xsl:when test="./@rend='superscript'">
                <xsl:element name="hi">
                    <xsl:attribute name="rend">superscript</xsl:attribute>
                    <xsl:apply-templates />
                </xsl:element>
            </xsl:when>

            <xsl:when test="./@rend='underline'">
                <xsl:element name="hi">
                    <xsl:attribute name="rend">underline</xsl:attribute>
                    <xsl:apply-templates />
                </xsl:element>
            </xsl:when>
            
            <xsl:otherwise>
                <xsl:apply-templates />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    
    <xsl:template match="tei:anchor"/>
    
    <!-- setup notes that are inline from comments -->
    <xsl:template match="tei:note">
        <xsl:if test="@place">
            <xsl:element name="ptr"><xsl:attribute name="type">noteRef</xsl:attribute></xsl:element>
        </xsl:if>
        <xsl:copy>
            <xsl:apply-templates select="node()|@*"/>
        </xsl:copy>
        
    </xsl:template>
    
</xsl:stylesheet>