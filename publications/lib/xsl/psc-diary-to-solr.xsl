<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="2.0"
    xmlns:mhs="http://www.masshist.org/ns/1.0">
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes"/>

    <xsl:strip-space elements="none"/>

    <xsl:include href="../../../publications/lib/xsl/psc-functions.xsl"/>
    
    <xsl:include href="../../../publications/lib/xsl/psc-umbrellas.xsl"/>
    

<!-- PARAMETERS TO CUSTOMIZE FOR SPECIFIC COLLECTION -->

    <xsl:param name="index"></xsl:param>
    <xsl:param name="resource_group_name"></xsl:param>
    <xsl:param name="resource_uri_start"></xsl:param>
    <xsl:param name="upload_date"><xsl:value-of select="format-date(current-date(), '[Y0001][M01][D01]')"/></xsl:param>
    <xsl:param name="filename"/> 
 
 
    <xsl:variable name="volume"><xsl:value-of select="/tei:TEI/tei:text/tei:body/tei:div[1]/@volume"/></xsl:variable>


    <xsl:template match="*"> </xsl:template>

    <xsl:template match="/">
        <add>
            <xsl:apply-templates select="/tei:TEI/tei:text/tei:body//tei:div[@type='entry']"/>
        </add>
    </xsl:template>

    <xsl:template match="tei:div[@type='entry']">
        
          <doc>
                <field name="id"><xsl:value-of select="./@xml:id"/></field>
                <field name="index"><xsl:value-of select="$index"/></field>
                <field name="filename"><xsl:value-of select="$filename"/></field>         
                <field name="upload_date"><xsl:value-of select="$upload_date"/></field>
                <field name="resource_uri_start"><xsl:value-of select="$resource_uri_start"/></field>
                <field name="resource_group_name"><xsl:value-of select="$resource_group_name"/></field>

                <field name="status">
                  <xsl:choose>
                      <xsl:when test="string(/tei:TEI/@published) = 'yes'">published</xsl:when>
                      <xsl:otherwise>staffonly</xsl:otherwise>
                  </xsl:choose>
                </field>
          
              <field name="title">
                  <xsl:value-of select="./tei:head[1]"/>
              </field>
              
              <xsl:for-each select="./tei:bibl[1]/tei:date">
                  <xsl:copy-of select="mhs:docDate(.)"/>
              </xsl:for-each>
              
            <xsl:for-each select="./tei:bibl[1]/tei:author">
                <field name="author"><xsl:value-of select="."/></field>
                  <xsl:copy-of select="mhs:persRef(.)"></xsl:copy-of>
            </xsl:for-each>
            
            <xsl:for-each select="./tei:div[@type='docbody']//tei:persRef">
                  <xsl:copy-of select="mhs:persRef(.)"></xsl:copy-of>
            </xsl:for-each>
   
            <xsl:for-each select="./tei:bibl[1]/tei:subject">
                <xsl:copy-of select="mhs:subjects(.)"></xsl:copy-of>
            </xsl:for-each>

              <!-- umbrella terms-->
              <xsl:for-each select="./tei:bibl[1]/tei:subject">
                  <xsl:variable name="name"><xsl:value-of select="."/></xsl:variable>
                  <xsl:for-each select="$umbrellas/topic[@name=$name]/u">
                      <xsl:copy-of select="mhs:subjects(.)"/>
                  </xsl:for-each>
              </xsl:for-each>
              
              <xsl:variable name="topic_umbrellas">
                  <xsl:for-each select="./tei:bibl[1]/tei:subject">
                      <xsl:variable name="name"><xsl:value-of select="."/></xsl:variable>
                      <xsl:text> </xsl:text><xsl:value-of select="$umbrellas/topic[@name=$name]"/>
                  </xsl:for-each>
              </xsl:variable>
 


			<!-- also populate the free text field-->
			<xsl:variable name="topic_keywords">
				<xsl:for-each select="./tei:bibl[1]/tei:subject">
					<xsl:value-of select="."/><xsl:text> </xsl:text>
				</xsl:for-each>

			    <xsl:variable name="name"><xsl:value-of select="."/></xsl:variable>
			    <xsl:for-each select="$umbrellas/topic[@name=$name]/u">
			        <xsl:text> </xsl:text><xsl:value-of select="mhs:subjects(.)"/>
			    </xsl:for-each>
			</xsl:variable>

			<field name="topic_keywords">
				<xsl:value-of select="normalize-space($topic_keywords)"/>
			</field>


              <field name="doc_beginning">
                  <xsl:copy-of select="mhs:doc_beginning(.//*[@type='docbody']//tei:p[1], 500)"></xsl:copy-of>
              </field>
              
              

            <field name="text">
                <xsl:value-of select="normalize-space((./tei:div[@type='docbody'])[1])"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="normalize-space($topic_keywords)"/>
                
                <xsl:for-each select=".//tei:div[@type='docback']//tei:note">
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="."></xsl:value-of>
                </xsl:for-each>
            </field>
              
              
            <field name="volume"><xsl:value-of select="$volume"/></field>  

          </doc>
    </xsl:template>

   

</xsl:stylesheet>
