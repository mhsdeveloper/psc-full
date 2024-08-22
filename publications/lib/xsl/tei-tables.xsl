<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:tei="http://www.tei-c.org/ns/1.0"
    version="1.0">
    
    
    <!-- TABLES -->
    <xsl:template match="tei:table">
        <table>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates select="tei:head" mode="tablesOnly"></xsl:apply-templates>
            <xsl:apply-templates/>
        </table>
    </xsl:template>
    
    
    <xsl:template match="tei:head" mode="tablesOnly">
        <tr>
            <th>
                <xsl:copy-of select="@*"/>
                <xsl:apply-templates />
            </th>
        </tr>
    </xsl:template>
    
    
    
    <!-- TABLE ROW -->
    <xsl:template match="tei:row">
        <tr>
            <!--Handle row when label-->
            <xsl:if test="@role = 'label'">
                <xsl:attribute name="class">label</xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </tr>
    </xsl:template>
    
    
    
    
    
    <!-- TABLE CELL -->
    <xsl:template match="tei:cell">
        <xsl:choose>
            <!--When cell is label or is a part of a row which is a label use <TH> tags -->
            <xsl:when test="@role = 'label' or parent::tei:row[@role = 'label']">
                <th>
                    <xsl:if test="@cols">
                        <xsl:attribute name="colspan"><xsl:value-of select="@cols"/></xsl:attribute>
                    </xsl:if>
                    <xsl:if test="@rows">
                        <xsl:attribute name="rowspan"><xsl:value-of select="@rows"/></xsl:attribute>
                    </xsl:if>
                    <!-- Handle currency-->
                    <xsl:if test="@role = 'curr'">
                        <xsl:attribute name="class">currency</xsl:attribute>
                    </xsl:if>
                    <xsl:apply-templates select="@*|node()"/><xsl:text> </xsl:text></th>
            </xsl:when>
            <!--   Otherwise use regular <TD> tags-->
            <xsl:otherwise>
                <td>
                    <xsl:if test="@cols">
                        <xsl:attribute name="colspan"><xsl:value-of select="@cols"/></xsl:attribute>
                    </xsl:if>
                    <xsl:if test="@rows">
                        <xsl:attribute name="rowspan"><xsl:value-of select="@rows"/></xsl:attribute>
                    </xsl:if>
                    <!-- Handle currency-->
                    <xsl:if test="@role = 'curr'">
                        <xsl:attribute name="class">currency</xsl:attribute>
                    </xsl:if>
                    <xsl:apply-templates select="@*|node()"/>
                    <!-- 
			      Handle page break inside table
			      following rule needed because <pb> often occurs following a table row, which breaks the HTML 
			      (putting a <span> between <tr>, no good); so put the page break inside the cell content-->
                    <!-- If following is not a cell, and first following sibling is a pb
			         create pb in table mode within the cell-->
                    <xsl:if
                        test="( not(following-sibling::tei:cell) ) and (parent::tei:row/following-sibling::*)[1][self::tei:pb]">
                        <xsl:apply-templates mode="table"
                            select="(parent::tei:row/following-sibling::*)[1][self::tei:pb]"/>
                    </xsl:if><xsl:text> </xsl:text></td>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!-- end TABLE CELL -->
</xsl:stylesheet>