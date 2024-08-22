<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns:mhs="http://www.masshist.org/ns/1.0"
    version="1.0">


    <!-- DATE FORMATTING UTILITY FUNCTIONS -->

    
    <xsl:template name="monthFromDate">
        <xsl:param name="isoDate"/>
        <xsl:variable name="mo"><xsl:value-of select="substring($isoDate,6, 2)"/></xsl:variable>
        <xsl:choose>
            <xsl:when test="$mo = '01'">January</xsl:when>
            <xsl:when test="$mo = '02'">February</xsl:when>
            <xsl:when test="$mo = '03'">March</xsl:when>
            <xsl:when test="$mo = '04'">April</xsl:when>
            <xsl:when test="$mo = '05'">May</xsl:when>
            <xsl:when test="$mo = '06'">June</xsl:when>
            <xsl:when test="$mo = '07'">July</xsl:when>
            <xsl:when test="$mo = '08'">August</xsl:when>
            <xsl:when test="$mo = '09'">September</xsl:when>
            <xsl:when test="$mo = '10'">October</xsl:when>
            <xsl:when test="$mo = '11'">November</xsl:when>
            <xsl:when test="$mo = '12'">December</xsl:when>
        </xsl:choose>
    </xsl:template>
    
    
    
    
    <xsl:template name="dayFromDate">
        <xsl:param name="isoDate"/>
        <xsl:choose>
            <!-- first is unknown days-->
            <xsl:when test="substring($isoDate, 9,2) = '00' or substring($isoDate, 9,2) = '99'"></xsl:when> 
            <!-- handle leading zero -->
            <xsl:when test="substring($isoDate, 9,1) = '0'"><xsl:value-of select="substring($isoDate, 10, 1)"/></xsl:when>
            <xsl:otherwise><xsl:value-of select="substring($isoDate, 9, 2)"/></xsl:otherwise>
        </xsl:choose>     
    </xsl:template>
    
</xsl:stylesheet>