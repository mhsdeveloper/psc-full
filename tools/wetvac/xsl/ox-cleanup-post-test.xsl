<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns="http://www.tei-c.org/ns/1.0" exclude-result-prefixes="xs tei xsl #default" version="1.0">
    <xsl:strip-space elements="NONE"/>
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="no"/>

    <xsl:param name="wetvacDate"/>

    <xsl:variable name="docid">
        <xsl:value-of select="/tei:TEI/tei:text[1]/tei:body[1]/tei:div[1]/@xml:id"/>
    </xsl:variable>


    <!-- This template includes EVERYTHING by default -->
    <xsl:template match="node() | @*">
        <xsl:copy>
            <xsl:apply-templates select="node() | @*"/>
        </xsl:copy>
    </xsl:template>


    <xsl:template match="/tei:TEI">
        <xsl:processing-instruction name="xml-model"> href="http://www.primarysourcecoop.org/publications/pub/schema/primarysourcecoop_rev2.rng" type="application/xml" schematypens="http://relaxng.org/ns/structure/1.0"</xsl:processing-instruction>
        <TEI>
            <xsl:copy-of select="@*"/>
            <teiHeader>
                <fileDesc>
                    <titleStmt>
                        <title>
                            <xsl:copy-of select="string(/tei:TEI/tei:text/tei:body/tei:head)"/>
                        </title>
                        <respStmt>
                            <resp>Transcribed by </resp>
                            <name>
                                <xsl:copy-of
                                    select="string(/tei:TEI/tei:text/tei:body/tei:transcriber)"/>
                            </name>
                            <note>Transcribed on <date type="transcription">
                                    <xsl:attribute name="when"><xsl:copy-of
                                            select="string(/tei:TEI/tei:text/tei:body/tei:transcriptionDate)"
                                        /></xsl:attribute>
                                </date>
                            </note>
                        </respStmt>
                    </titleStmt>

                    <publicationStmt>
                        <publisher>Primary Source Cooperative</publisher>
                        <date></date>
                        <availability>
                            <p>Online version 1.</p>
                            <licence>Available under Creative Commons license CC BY-NC-SA
                                Attribution--NonCommercial--ShareAlike.</licence>
                        </availability>
                    </publicationStmt>


                    <seriesStmt>
                        <title>
                            <xsl:if test="/tei:TEI/tei:text/tei:body/tei:edition">
                                <xsl:copy-of select="string(/tei:TEI/tei:text/tei:body/tei:edition)"
                                />
                            </xsl:if>
                        </title>
                        <xsl:for-each select="/tei:TEI/tei:text/tei:body/tei:editor">
                            <editor>
                                <xsl:copy-of select="string(.)"/>
                            </editor>
                            <xsl:text>
                                </xsl:text>
                        </xsl:for-each>
                    </seriesStmt>



                    <sourceDesc>
                        <bibl>
                            <xsl:if test="/tei:TEI/tei:text/tei:body/tei:date">
                                <date type="creation">
                                    <xsl:attribute name="when">
                                        <xsl:copy-of
                                            select="string(/tei:TEI/tei:text/tei:body/tei:date)"/>
                                    </xsl:attribute>
                                    
                                    <xsl:value-of select="substring(/tei:TEI/tei:text/tei:body/tei:date, 9, 2)"/>
                                    <xsl:text> </xsl:text>
                                    <xsl:variable name="month"><xsl:value-of select="substring(/tei:TEI/tei:text/tei:body/tei:date, 6, 2)"/></xsl:variable>
                                        <xsl:choose>
                                           <xsl:when test="$month = '01'">Jan</xsl:when>
                                           <xsl:when test="$month = '02'">Feb</xsl:when>
                                            <xsl:when test="$month = '03'">Mar</xsl:when>
                                            <xsl:when test="$month = '04'">Apr</xsl:when>
                                            <xsl:when test="$month = '05'">May</xsl:when>
                                            <xsl:when test="$month = '06'">Jun</xsl:when>
                                            <xsl:when test="$month = '07'">Jul</xsl:when>
                                            <xsl:when test="$month = '08'">Aug</xsl:when>
                                            <xsl:when test="$month = '09'">Sep</xsl:when>
                                            <xsl:when test="$month = '10'">Oct</xsl:when>
                                            <xsl:when test="$month = '11'">Nov</xsl:when>
                                            <xsl:when test="$month = '12'">Dec</xsl:when>
                                            <xsl:otherwise></xsl:otherwise>
                                        </xsl:choose>
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="substring(/tei:TEI/tei:text/tei:body/tei:date, 1, 4)"/>
                                </date>
                            </xsl:if>

                            <xsl:for-each select="/tei:TEI/tei:text/tei:body/tei:author">
                                <author>
                                    <xsl:copy-of select="string(.)"/>
                                </author>
                                <xsl:text>
                                </xsl:text>
                            </xsl:for-each>
                            <xsl:for-each select="/tei:TEI/tei:text/tei:body/tei:recipient">
                                <recipient>
                                    <xsl:copy-of select="string(.)"/>
                                </recipient>
                                <xsl:text>
                                </xsl:text>
                            </xsl:for-each>

                            <title>
                                <xsl:copy-of select="string(/tei:TEI/tei:text/tei:body/tei:head)"/>
                            </title>
                        </bibl>

                        <msDesc>
                            <msIdentifier>
                                <xsl:for-each
                                    select="/tei:TEI/tei:text/tei:body/tei:note[@type = 'repository']">
                                    <repository>
                                        <xsl:copy-of select="string(.)"/>
                                    </repository>
                                    <xsl:text>
                                    </xsl:text>
                                </xsl:for-each>
                                <xsl:for-each
                                    select="/tei:TEI/tei:text/tei:body/tei:note[@type = 'collection']">
                                    <collection>
                                        <xsl:copy-of select="string(.)"/>
                                    </collection>
                                    <xsl:text>
                                    </xsl:text>
                                </xsl:for-each>
                                <idno/>
                            </msIdentifier>
                        </msDesc>

                    </sourceDesc>
                </fileDesc>

                <encodingDesc>
                    <appInfo>
                        <application ident="MHS-WETVAC" version="0.9">
                            <xsl:attribute name="when">
                                <xsl:value-of select="$wetvacDate"/>
                            </xsl:attribute>
                            <label>MHS-WETVAC</label>
                        </application>
                    </appInfo>
                    <editorialDecl>
                        <p>This XML document follows the Primary Source Cooperative's encoding guidelines
                            and XML schema adapted and customized with TEI.</p>
                        <p>Hold this space for succinct statements about editorial principles here and/or
                            link to the website with more detailed editorial descriptions.</p>
                    </editorialDecl>
                </encodingDesc>

                <profileDesc>
                    <textClass>
                        <keywords scheme="primarysource-keywords">
                            <list>
                                <xsl:for-each select="/tei:TEI/tei:text/tei:body/tei:subject">
                                    <item>
                                        <xsl:copy-of select="string(.)"/>
                                    </item>
                                    <xsl:text>
                                    </xsl:text>
                                </xsl:for-each>
                            </list>
                        </keywords>
                    </textClass>
                </profileDesc>


                <revisionDesc>
                    <change when="" who=""/>
                </revisionDesc>
                

            </teiHeader>

            <xsl:apply-templates/>

        </TEI>
    </xsl:template>


    <!-- elements to block; these get pulled in purposely elsewhere -->
    <xsl:template
        match="tei:date | tei:author | tei:recipient | tei:editor | tei:edition | tei:subject | tei:transcriber | tei:transcriptionDate | tei:head | tei:dateline | tei:salute | tei:farewell | tei:signed | tei:postscript | tei:div | tei:note"/>


    <!-- remove listChange -->
    <xsl:template match="tei:listChange">
        <xsl:apply-templates/>
    </xsl:template>


    <xsl:template match="tei:body">
        <body>
            <div type="docbody">

                <opener>
                    <xsl:apply-templates select="tei:dateline" mode="insert"/>
                    <xsl:apply-templates select="tei:salute" mode="insert"/>
                </opener>

                <xsl:apply-templates/>

                <closer>
                    <xsl:apply-templates select="tei:farewell" mode="insert"/>
                    <xsl:apply-templates select="tei:signed" mode="insert"/>
                </closer>

                <xsl:apply-templates select="tei:postscript" mode="insert"/>

            </div>

            <xsl:for-each select=".//tei:div[@type = 'insertion']">
                <xsl:copy>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates/>
                </xsl:copy>
            </xsl:for-each>

            <div type="docback">
                <!-- source notes first -->
                <note type="source">
                    <xsl:apply-templates select="tei:note[@type = 'source']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'doctype']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'repository']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'collection']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'condition']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'address']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'endorsement']" mode="insert"/>
                    <xsl:apply-templates select="tei:note[@type = 'notation']" mode="insert"/>
                </note>

                <xsl:apply-templates select="tei:note[@type = 'fn']" mode="insert"/>

                <xsl:for-each select="//tei:div[@type = 'docbody']//tei:note[@place = 'comment']">
                    <xsl:element name="note">
                        <xsl:attribute name="type">fn</xsl:attribute>
                        <xsl:attribute name="subtype">inline-comment</xsl:attribute>
                        <xsl:apply-templates/>
                    </xsl:element>
                </xsl:for-each>
            </div>
        </body>
    </xsl:template>


    <xsl:template match="tei:p">

        <xsl:choose>
            <xsl:when test="parent::tei:opener">
                <xsl:apply-templates/>
                <xsl:element name="lb"/>
            </xsl:when>
            <xsl:when test="parent::tei:closer">
                <xsl:apply-templates/>
                <xsl:element name="lb"/>
            </xsl:when>

            <xsl:otherwise>
                <p xmlns="http://www.tei-c.org/ns/1.0">
                    <xsl:apply-templates/>
                </p>

            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="tei:div[@type = 'insertion']" mode="insert">
        <xsl:copy>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates mode="nest"/>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="tei:note | tei:dateline | tei:salute | tei:signed" mode="insert">
        <xsl:copy>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="tei:farewell" mode="insert">
        <salute>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates/>
        </salute>
    </xsl:template>


    <xsl:template match="tei:postscript" mode="insert">
        <postscript>
            <xsl:apply-templates/>
            <xsl:apply-templates select="tei:signed" mode="insert"/>
        </postscript>
    </xsl:template>

    <!-- skip notes that are comments 
    <xsl:template match="tei:note">
        <xsl:choose>
            <xsl:when test="@place">
            </xsl:when>
            <xsl:otherwise>
                <xsl:copy>
                    <xsl:apply-templates select="node()|@*"/>
                </xsl:copy>                
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    -->

</xsl:stylesheet>
