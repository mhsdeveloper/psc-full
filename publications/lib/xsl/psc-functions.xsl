<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    xmlns:mhs="http://www.masshist.org/ns/1.0">
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes"/>
    
    
    
    
    <xsl:function name="mhs:docDate">
        <xsl:param name="doc"></xsl:param>
        <xsl:if test="$doc/@when">
            <field name="date_when">
                <xsl:value-of select="mhs:dateFix($doc/@when)"/>
            </field>
            <field name="date_to">
                <xsl:value-of select="mhs:dateFix($doc/@when)"/>
            </field>
            <field name="date_year">
                <xsl:value-of select="substring($doc/@when, 1, 4)"/>
            </field>
            <field name="date_month">
                <xsl:value-of select="mhs:getMonth($doc/@when)"/>
            </field>
        </xsl:if>
        <xsl:if test="$doc/@from">
            <field name="date_when">
                <xsl:value-of select="mhs:dateFix($doc/@from)"/>
            </field>
            <field name="date_year">
                <xsl:value-of select="substring($doc/@from, 1, 4)"/>
            </field>
            <field name="date_month">
                <xsl:value-of select="mhs:getMonth($doc/@from)"/>
            </field>
        </xsl:if>
        <xsl:if test="$doc/@to">
            <field name="date_to">
                <xsl:value-of select="mhs:dateFix($doc/@to)"/>
            </field>
            <field name="date_year">
                <xsl:value-of select="substring($doc/@to, 1, 4)"/>
            </field>
            <field name="date_month">
                <xsl:value-of select="mhs:getMonth($doc/@to)"/>
            </field>
            
        </xsl:if>
        <xsl:if test="$doc/@notBefore">
            <field name="date_when">
                <xsl:value-of select="mhs:dateFix($doc/@notBefore)"/>
            </field>
            <field name="date_year">
                <xsl:value-of select="substring($doc/@notBefore, 1, 4)"/>
            </field>
            <field name="date_month">
                <xsl:value-of select="mhs:getMonth($doc/@notBefore)"/>
            </field>
            
            <field name="date_label">notBefore</field>
        </xsl:if>
        <xsl:if test="$doc/@notAfter">
            <field name="date_to">
                <xsl:value-of select="mhs:dateFix($doc/@notAfter)"/>
            </field>
            <field name="date_year">
                <xsl:value-of select="substring($doc/@notAfter, 1, 4)"/>
            </field>
            <field name="date_month">
                <xsl:value-of select="mhs:getMonth($doc/@notAfter)"/>
            </field>
            
        </xsl:if>
        
    </xsl:function>
    
    
    
    <xsl:function name="mhs:persRef">
        <xsl:param name="el"></xsl:param>
        <xsl:for-each select="tokenize($el/@ref, ';')">
            <xsl:copy-of select="mhs:onePersRef(.)"></xsl:copy-of>
        </xsl:for-each>
        <xsl:for-each select="tokenize($el/@key, ';')">
            <xsl:copy-of select="mhs:onePersRef(.)"></xsl:copy-of>
        </xsl:for-each>
        <xsl:for-each select="tokenize($el/@target, ';')">
            <xsl:copy-of select="mhs:onePersRef(.)"></xsl:copy-of>
        </xsl:for-each>
    </xsl:function>
    
    
    
    <xsl:function name="mhs:onePersRef">
        <xsl:param name="val"></xsl:param>
        <xsl:choose>
            <xsl:when test="$val = 'u'"></xsl:when>
            <xsl:when test="$val = 'unknown'"></xsl:when>
            <xsl:otherwise>
                <field name="person_keyword"><xsl:value-of select="translate($val, ' ', '')"/></field>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:function>
    
    
    <xsl:function name="mhs:subjects">
        <xsl:param name="el"></xsl:param>
        <xsl:choose>
            <xsl:when test="string-length($el) gt 0"><field name="subject"><xsl:value-of select="normalize-space($el)"/></field></xsl:when>
            <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
    </xsl:function>
    
    
    
    <xsl:function name="mhs:dateFix">
        <xsl:param name="date"></xsl:param>
        <xsl:variable name="date2"><xsl:value-of select="translate($date, '-', '')"/></xsl:variable>
        <xsl:choose>
            <xsl:when test="string-length($date2) eq 4"><xsl:value-of select="$date2"/>9999</xsl:when>
            <xsl:when test="string-length($date2) eq 6"><xsl:value-of select="$date2"/>99</xsl:when>
            <xsl:when test="string-length($date2) eq 8"><xsl:value-of select="$date2"/></xsl:when>
        </xsl:choose>
    </xsl:function>
    
    
    <xsl:function name="mhs:getMonth">
        <xsl:param name="date"></xsl:param>
        <xsl:variable name="date2"><xsl:value-of select="translate($date, '-', '')"/></xsl:variable>
        <xsl:choose>
            <xsl:when test="string-length($date2) eq 4"><xsl:value-of select="$date2"/>99</xsl:when>
            <xsl:otherwise><xsl:value-of select="substring($date2, 5, 2)"/></xsl:otherwise>
        </xsl:choose>
    </xsl:function>
    
    
    
    
    <xsl:function name="mhs:doc_beginning">
        <xsl:param name="doc"></xsl:param>
        <xsl:param name="size"/>

        <xsl:variable name="text"><xsl:value-of select='replace($doc, "\s+" , " ")'/></xsl:variable>
        
        <xsl:value-of select="mhs:trim-text(normalize-space($text), number($size))"/>
        
    </xsl:function>
    
    
 
    <xsl:function name="mhs:trim-text">
        <xsl:param name="text"/>
        <xsl:param name="pos"/>
        <xsl:choose>
            <xsl:when test="$pos != 0 and substring($text,$pos,1) != ' '">
                <xsl:value-of select="mhs:trim-text(substring($text, 1, ($pos - 1)), ($pos -1))"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="substring($text, 1, $pos)"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:function>
    
    
    
    
</xsl:stylesheet>