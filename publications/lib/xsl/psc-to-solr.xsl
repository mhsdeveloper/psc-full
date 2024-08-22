<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="2.0"
    xmlns:mhs="http://www.masshist.org/ns/1.0">
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes"/>

    <xsl:strip-space elements="none"/>

    <xsl:include href="psc-functions.xsl"/>

    <xsl:include href="psc-umbrellas.xsl"/>
    
   <!-- PARAMETERS TO CUSTOMIZE FOR SPECIFIC COLLECTION -->

    <xsl:param name="index"></xsl:param>
    <xsl:param name="resource_group_name"></xsl:param>
    <xsl:param name="resource_uri_start"></xsl:param>
    <xsl:param name="upload_date"><xsl:value-of select="format-date(current-date(), '[Y0001][M01][D01]')"/></xsl:param>
    <xsl:param name="filename"/> 
    
    
    <xsl:template match="*"> </xsl:template>

    <xsl:template match="/">
        
        <add>
          <doc>
            <field name="id"><xsl:value-of select="/tei:TEI/@xml:id"/></field>
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
                <xsl:value-of select="/tei:TEI/tei:teiHeader[1]/tei:fileDesc[1]/tei:sourceDesc[1]/tei:bibl[1]/tei:title[1]"/>
            </field>
              
              <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:fileDesc[1]/tei:sourceDesc[1]/tei:bibl[1]/tei:date">
                  <xsl:copy-of select="mhs:docDate(.)"/>
             </xsl:for-each>
              
            <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:fileDesc[1]/tei:sourceDesc[1]/tei:bibl[1]/tei:author">
                <field name="author"><xsl:value-of select="."/></field>
                <field name="person_keyword"><xsl:value-of select="."/></field>
            </xsl:for-each>

            <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:fileDesc[1]/tei:sourceDesc[1]/tei:bibl[1]/tei:recipient">
                <field name="recipient"><xsl:value-of select="."/></field>
                <field name="person_keyword"><xsl:value-of select="."/></field>
            </xsl:for-each>
            
            <xsl:for-each select="/tei:TEI/tei:text[1]/tei:body[1]//tei:persRef">
                <xsl:copy-of select="mhs:persRef(.)"></xsl:copy-of>
            </xsl:for-each>
   
            <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:textClass[1]/tei:keywords[1]/tei:list[1]/tei:item">
                 <xsl:copy-of select="mhs:subjects(.)"></xsl:copy-of>
            </xsl:for-each>

			<!-- also populate the free text field-->
			<xsl:variable name="topic_keywords">
				<xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:textClass[1]/tei:keywords[1]/tei:list[1]/tei:item">
					<xsl:value-of select="."/><xsl:text> </xsl:text>
				</xsl:for-each>
			</xsl:variable>
              

            <!-- umbrella terms-->
              <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:textClass[1]/tei:keywords[1]/tei:list[1]/tei:item">
                  <xsl:variable name="name"><xsl:value-of select="."/></xsl:variable>
                  <xsl:for-each select="$umbrellas/topic[@name=$name]/u">
                      <xsl:copy-of select="mhs:subjects(.)"/>
                  </xsl:for-each>
                 
              </xsl:for-each>
              
             <xsl:variable name="topic_umbrellas">
                 <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:textClass[1]/tei:keywords[1]/tei:list[1]/tei:item">
                     <xsl:variable name="name"><xsl:value-of select="."/></xsl:variable>
                     <xsl:for-each select="$umbrellas/topic[@name=$name]/u">
                         <xsl:text> </xsl:text><xsl:value-of select="mhs:subjects(.)"/>
                     </xsl:for-each>
                 </xsl:for-each>
             </xsl:variable>

			<field name="topic_keywords">
			    <xsl:value-of select="normalize-space($topic_keywords)"/><xsl:text> </xsl:text>
			    <xsl:value-of select="normalize-space($topic_umbrellas)"/>
			</field>
            
            
              <field name="doc_beginning">
                  <xsl:copy-of select="mhs:doc_beginning(.//*[@type='docbody']//tei:p[1], 500)"></xsl:copy-of>
              </field>
              

            <field name="text">
                <xsl:value-of select="normalize-space(/tei:TEI/tei:text[1]/tei:body[1])"/>

                <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:textClass[1]/tei:keywords[1]/tei:list[1]/tei:item">
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="mhs:subjects(.)"></xsl:value-of>
                </xsl:for-each>

                <xsl:for-each select="/tei:TEI//tei:body[1]//tei:div[@type='docback']//tei:note">
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="."></xsl:value-of>
                </xsl:for-each>
            </field>
              
              
           <field name="teiheader">
               <xsl:for-each select="/tei:TEI/tei:teiHeader[1]/tei:revisionDesc[1]/tei:listChange[@type='editorialMilestones']//tei:seg">
                   <xsl:if test="@status">
                       <xsl:value-of select="parent::tei:change/@type"/>:<xsl:value-of select="@type"/>=<xsl:value-of select="@status"/><xsl:text> </xsl:text>
                   </xsl:if>
               </xsl:for-each>
           </field>
              

          </doc>
        </add>
    </xsl:template>
   


</xsl:stylesheet>
