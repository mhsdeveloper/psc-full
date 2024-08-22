<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tei="http://www.tei-c.org/ns/1.0"
    xmlns="http://www.tei-c.org/ns/1.0" exclude-result-prefixes="xs tei xsl #default" version="1.0">
    <xsl:strip-space elements="NONE"/>
    <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="no"/>
    <xsl:param name="wetvacDate"/>


    <xsl:param name="doneMileTranscription">3</xsl:param>
    <xsl:param name="doneMilePersRef">3</xsl:param>
    <xsl:param name="doneMileSubjects">3</xsl:param>
    <xsl:param name="doneMileAnnotation">4</xsl:param>

    <xsl:variable name="docid">
        <xsl:value-of select="/tei:TEI/tei:text[1]/tei:body[1]/tei:div[1]/@xml:id"/>
    </xsl:variable>

    <xsl:variable name="year">
        <xsl:value-of select="substring($wetvacDate, 1, 4)"/>
    </xsl:variable>


    <!-- This template includes EVERYTHING by default -->
    <xsl:template match="node() | @*">
        <xsl:copy>
            <xsl:apply-templates select="node() | @*"/>
        </xsl:copy>
    </xsl:template>


    <xsl:template match="/tei:TEI">
        <xsl:text xml:space="preserve"></xsl:text>
        
        <xsl:processing-instruction name="xml-model"> href="https://www.primarysourcecoop.org/publications/pub/schema/primarysourcecoop_rev2.2.rng" type="application/xml" schematypens="http://relaxng.org/ns/structure/1.0"</xsl:processing-instruction>
        <xsl:processing-instruction name="xml-stylesheet">type="text/css" href="https://www.primarysourcecoop.org/publications/pub/css/authorview_rev2.css"</xsl:processing-instruction>
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
                        <date>
                            <xsl:attribute name="when">
                                <xsl:value-of select="$year"/>
                            </xsl:attribute>
                            <xsl:value-of select="$year"/>
                        </date>
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
                                    <xsl:variable name="year">
                                        <xsl:value-of
                                            select="substring(/tei:TEI/tei:text/tei:body/tei:date, 1, 4)"
                                        />
                                    </xsl:variable>
                                    <xsl:variable name="month">
                                        <xsl:value-of
                                            select="substring(/tei:TEI/tei:text/tei:body/tei:date, 6, 2)"
                                        />
                                    </xsl:variable>
                                    <xsl:variable name="day">
                                        <xsl:choose>
                                            <xsl:when
                                                test="substring(/tei:TEI/tei:text/tei:body/tei:date, 9, 1) = '0'">
                                                <xsl:value-of
                                                  select="substring(/tei:TEI/tei:text/tei:body/tei:date, 10, 2)"
                                                />
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:value-of
                                                  select="substring(/tei:TEI/tei:text/tei:body/tei:date, 9, 2)"
                                                />
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:variable>
                                    <xsl:variable name="prettyMonth">
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
                                        </xsl:choose>
                                    </xsl:variable>
                                    <xsl:value-of select="$day"/>
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="$prettyMonth"/>
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="$year"/>
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

                            <ab>
                                <note/>
                            </ab>
                        </msDesc>

                    </sourceDesc>
                </fileDesc>

                <encodingDesc>
                    <appInfo>
                        <application ident="MHS-WETVAC" version="0.9.1">
                            <xsl:attribute name="when">
                                <xsl:value-of select="$wetvacDate"/>
                            </xsl:attribute>
                            <label>MHS-WETVAC</label>
                        </application>
                    </appInfo>
                    <editorialDecl>
                        <p>Hold this space for succinct statements about editorial principles here
                            and/or link to the website with more detailed editorial
                            descriptions.</p>
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
                    <listChange type="editorialMilestones">
                        <change type="Transcription" who="WETVAC">
                            <xsl:attribute name="when">
                                <xsl:value-of select="$wetvacDate"/>
                            </xsl:attribute>
                            <xsl:choose>
                               <xsl:when test="$doneMileTranscription = '3'"><xsl:attribute name="status">complete</xsl:attribute></xsl:when>
                                <xsl:otherwise><xsl:attribute name="status">in-progress</xsl:attribute></xsl:otherwise>
                            </xsl:choose>
                            
                            <xsl:choose>
                                <xsl:when test="$doneMileTranscription = '2'">
                                    <seg type="initial-transcription" status="done"/>
                                    <seg type="verification1" status="done"/>
                                    <seg type="verification2" status="not-begun"/> 
                                </xsl:when>
                                <xsl:when test="$doneMileTranscription = '3'">
                                    <seg type="initial-transcription" status="done"/> 
                                    <seg type="verification1" status="done"/>
                                    <seg type="verification2" status="done"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <seg type="initial-transcription" status="done"/>
                                    <seg type="verification1" status="not-begun"/>
                                    <seg type="verification2" status="not-begun"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </change>
                            


                        <change type="persRef" who="WETVAC">
                            <xsl:attribute name="when">
                                <xsl:value-of select="$wetvacDate"/>
                            </xsl:attribute>
                            <xsl:choose>
                                <xsl:when test="$doneMilePersRef = '3'"><xsl:attribute name="status">complete</xsl:attribute></xsl:when>
                                <xsl:otherwise><xsl:attribute name="status">in-progress</xsl:attribute></xsl:otherwise>
                            </xsl:choose>

                            <xsl:choose>
                                <xsl:when test="$doneMilePersRef = '1'">
                                    <seg type="all-persRefs-added" status="done"/>
                                    <seg type="accuracy-confirmed" status="not-begun"/>
                                    <seg type="HUSCs-verified" status="not-begun"/>
                                </xsl:when>
                                
                                <xsl:when test="$doneMilePersRef = '2'">
                                    <seg type="all-persRefs-added" status="done"/>
                                    <seg type="accuracy-confirmed" status="done"/>
                                    <seg type="HUSCs-verified" status="not-begun"/>
                                </xsl:when>
                                
                                <xsl:when test="$doneMilePersRef = '3'">
                                    <seg type="all-persRefs-added" status="done"/>
                                    <seg type="accuracy-confirmed" status="done"/>
                                    <seg type="HUSCs-verified" status="done"/>
                                </xsl:when>
                               
                                <xsl:otherwise>
                                    <seg type="all-persRefs-added" status="not-begun"/>
                                    <seg type="accuracy-confirmed" status="not-begun"/>
                                    <seg type="HUSCs-verified" status="not-begun"/>
                                </xsl:otherwise>
                                
                            </xsl:choose>
                        </change>


                        <change type="Subjects" who="WETVAC">
                            <xsl:attribute name="when">
                                <xsl:value-of select="$wetvacDate"/>
                            </xsl:attribute>
                            <xsl:choose>
                                <xsl:when test="$doneMileSubjects = '3'"><xsl:attribute name="status">complete</xsl:attribute></xsl:when>
                                <xsl:otherwise><xsl:attribute name="status">in-progress</xsl:attribute></xsl:otherwise>
                            </xsl:choose>
                            
                            <xsl:choose>
                              
                                <xsl:when test="$doneMileSubjects = '1'">
                                    <seg type="all-Subjects-added" status="done"/>
                                    <seg type="all-approved" status="not-begun"/>
                                    <seg type="all-confirmed-with-Database" status="not-begun"/>
                                </xsl:when>
                                
                                <xsl:when test="$doneMileSubjects = '2'">
                                    <seg type="all-Subjects-added" status="done"/>
                                    <seg type="all-approved" status="done"/>
                                    <seg type="all-confirmed-with-Database" status="not-begun"/>
                                </xsl:when>
                                
                                <xsl:when test="$doneMileSubjects = '3'">
                                    <seg type="all-Subjects-added" status="done"/>
                                    <seg type="all-approved" status="done"/>
                                    <seg type="all-confirmed-with-Database" status="underway"/>
                                </xsl:when>

                                <xsl:otherwise>
                                    <seg type="all-Subjects-added" status="not-begun"/>
                                    <seg type="all-approved" status="not-begun"/>
                                    <seg type="all-confirmed-with-Database" status="not-begun"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </change>
                            
      
                        <change type="Annotation" who="WETVAC">
                            <xsl:attribute name="when">
                                <xsl:value-of select="$wetvacDate"/>
                            </xsl:attribute>
                            <xsl:choose>
                                <xsl:when test="$doneMileAnnotation = '4'"><xsl:attribute name="status">complete</xsl:attribute></xsl:when>
                                <xsl:otherwise><xsl:attribute name="status">in-progress</xsl:attribute></xsl:otherwise>
                            </xsl:choose>
                            
                             <xsl:choose>
                                 <xsl:when test="$doneMileAnnotation = '1'">
                                     <seg type="source-note-complete" status="information-confirmed"/>
                                     <seg type="all-drafted" status="not-begun"/>
                                     <seg type="all-edited" status="not-begun"/>
                                     <seg type="all-approved" status="not-begun"/>
                                 </xsl:when>
                                 
                                 <xsl:when test="$doneMileAnnotation = '2'">
                                     <seg type="source-note-complete" status="information-confirmed"/>
                                     <seg type="all-drafted" status="done"/>
                                     <seg type="all-edited" status="not-begun"/>
                                     <seg type="all-approved" status="not-begun"/>
                                 </xsl:when>
                                 
                                 <xsl:when test="$doneMileAnnotation = '3'">
                                     <seg type="source-note-complete" status="information-confirmed"/>
                                     <seg type="all-drafted" status="done"/>
                                     <seg type="all-edited" status="done"/>
                                     <seg type="all-approved" status="not-begun"/>
                                 </xsl:when>
                                 
                                 <xsl:when test="$doneMileAnnotation = '4'">
                                     <seg type="source-note-complete" status="information-confirmed"/>
                                     <seg type="all-drafted" status="done"/>
                                     <seg type="all-edited" status="done"/>
                                     <seg type="all-approved" status="underway"/>
                                 </xsl:when>

                                 <xsl:otherwise>
                                     <seg type="source-note-complete" status="information-added"/>
                                     <seg type="all-drafted" status="not-begun"/>
                                     <seg type="all-edited" status="not-begun"/>
                                     <seg type="all-approved" status="not-begun"/>
                                 </xsl:otherwise>
                                 
                             </xsl:choose>
                        </change>

                        <change type="xmlReview" status="coming-soon" who="" when="1000-10-10"/>
                        <change type="OK-to-Publish" status="no" who="" when="1000-10-10"/>
                    </listChange>

                    <listChange type="programmaticUpdates">
                        <change type="" status="" who="" when="1000-10-10"/>
                    </listChange>
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
