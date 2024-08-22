<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:tei="http://www.tei-c.org/ns/1.0"
	xmlns:FGEA="http://rotunda.upress.virginia.edu/FGEA"
	xmlns:fn="http://www.w3.org/2005/xpath-functions"
	xmlns:mhs="http://www.masshist.org/ns/1.0"
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns="http://www.tei-c.org/ns/1.0"
	exclude-result-prefixes="html xsl mhs tei FGEA fn #default">
<!--
	Originally from TEI Boilerplate stylesheet, by John A. Walsh, Nov 17, 2011
-->

	<xsl:output encoding="UTF-8" method="xml" omit-xml-declaration="yes"/>

	<xsl:param name="inlineCSS" select="true()"/>
	<xsl:param name="displayPageBreaks" select="true()"/>



<!-- special characters -->
	<xsl:param name="quot">
		<text>"</text>
	</xsl:param>
	<xsl:param name="apos">
		<text>'</text>
	</xsl:param>

<!-- interface text -->
	<xsl:param name="pbNote">
		<text>page: </text>
	</xsl:param>
	<xsl:param name="altTextPbFacs">
		<text>view page image(s)</text>
	</xsl:param>



<!-- setup: paths, urls -->

	<!-- the prefix for link href attrs to other documents -->
	<xsl:param name="docLinkPrefix"/>

	<!-- the prefix for link href attrs to short title displays -->
	<xsl:param name="stLinkPrefix" />

	<xsl:param name="baseURL">../</xsl:param>

	<!-- the prefix for links to jpeg scans -->
	<xsl:param name="scansLinkPrefix"><xsl:value-of select="$docLinkPrefix"/>images/</xsl:param>

	<xsl:key name="ids" match="//*" use="@xml:id"/>

	<xsl:template match="/" name="htmlShell" priority="99">
		<xsl:attribute name="class"><xsl:value-of select="./@type"/><xsl:text> </xsl:text><xsl:value-of select="@subtype"/></xsl:attribute>
		<xsl:apply-templates/>
	</xsl:template>





<!-- basic full copy-->
	<xsl:template match="@*">
		<!-- copy select elements -->
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/>
		</xsl:copy>
	</xsl:template>





<!-- template for all elements, that removes any namespacing (for web browser-end display) -->

	<xsl:template match="*">
		<xsl:element name="{name()}" namespace="">
			<xsl:apply-templates select="@*|node()"/>
			<xsl:if test="string(.) = ''"><xsl:text> </xsl:text></xsl:if>
		</xsl:element>
	</xsl:template>


	<xsl:template match="tei:head">
		<xsl:choose>
			<xsl:when test="ancestor::tei:table"/>	
			<xsl:otherwise>
				<xsl:element name="header" namespace="">
					<xsl:copy-of select="@*" />
					<xsl:apply-templates />
				</xsl:element>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



<!-- Template to omit processing instructions from output.
-->
	<xsl:template match="processing-instruction()" priority="10"/>



	<xsl:template name="rendition">
		<xsl:if test="@rendition">
			<xsl:attribute name="rendition">
				<xsl:value-of select="@rendition"/>
			</xsl:attribute>
		</xsl:if>
	</xsl:template>




<!-- Transforms TEI ptr element to html a (link) element. -->
	<xsl:template match="tei:persRef" priority="99">
		<xsl:variable name="href">
			<xsl:choose>
				<xsl:when test="@key"><xsl:value-of select="@key"/></xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@ref"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:element name="a" namespace="">
			<xsl:attribute name="data-husc"><xsl:value-of select="$href"/></xsl:attribute>
			<xsl:attribute name="class">persRef</xsl:attribute>
			<!-- <xsl:value-of select="string(.)"/> -->
			<xsl:apply-templates/>
		</xsl:element>

	</xsl:template>




<!-- Transforms TEI ptr element to html a (link) element. -->
	<xsl:template match="tei:ptr[@target]" priority="99">
			<xsl:choose>
				<xsl:when test="@type='insRef'">
					<a class="insRef" data-insid="{@target}">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
						</svg>
						<xsl:value-of select="substring-after(@target, 'ins')"/>
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
						</svg>
					</a>
				</xsl:when>
				<xsl:when test="contains(@target, 'fn')">
					<a class="noteRef" data-fnid="{@target}"><xsl:value-of select="substring-after(@target, 'fn')"/></a>
				</xsl:when>
				<xsl:when test="@type='fn'">
					<a class="noteRef" data-fnid="{@target}"><xsl:value-of select="substring-after(@target, 'n')"/></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="{@target}">
						<xsl:value-of select="normalize-space(@target)"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>




<!-- Transforms TEI figure element to html img element. -->

	<xsl:template match="tei:figure[tei:graphic[@url]]" priority="99">
		<xsl:copy>
			<xsl:apply-templates select="@*"/>
			<figure>
				<img alt="{normalize-space(tei:figDesc)}" src="{tei:graphic/@url}"/>
				<xsl:apply-templates
					select="*[local-name() != 'graphic' and local-name() != 'figDesc']"/>
			</figure>
		</xsl:copy>
	</xsl:template>

<!-- Transforms TEI figure/head to HTML figcaption -->
	<xsl:template match="tei:figure/tei:head">
		<figcaption><xsl:apply-templates/></figcaption>
	</xsl:template>


	<xsl:template match="tei:pb">
			<xsl:variable name="pageno">
				<xsl:choose>
					<xsl:when test="contains(@n, 'p')">
						<xsl:value-of select="substring-after(@n, 'p')"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@n"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>

			<xsl:choose>
				<xsl:when test="parent::tei:table">
					<tr class="pbContainer"><xsl:attribute name="id">pageBreak<xsl:value-of select="$pageno"/></xsl:attribute>
						<td>
							<span class="pb">
								<xsl:attribute name="id">pageBreak<xsl:value-of select="$pageno"/></xsl:attribute>
								<xsl:value-of select="$pageno"/>
							</span>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<xsl:element name="span" namespace="">
						<xsl:attribute name="class">pb</xsl:attribute>
						<xsl:attribute name="type"><xsl:value-of select="@type"/></xsl:attribute>
						<xsl:if test="@facs"><xsl:attribute name="facs"><xsl:value-of select="@facs"/></xsl:attribute></xsl:if>
						<xsl:if test="@subtype"><xsl:attribute name="subtype"><xsl:value-of select="@subtype"/></xsl:attribute></xsl:if>
						<xsl:attribute name="id">pageBreak<xsl:value-of select="$pageno"/></xsl:attribute>
						<xsl:value-of select="$pageno"/>
						<xsl:if test="./@mhs:scanPage">
							<a class="scanPage" title="View a scan of the page from the print edition">
								<xsl:attribute name="href">
									<xsl:value-of select="$scansLinkPrefix"/><xsl:value-of select="./@mhs:scanPage"/>
								</xsl:attribute>
								image
							</a>
						</xsl:if>
					</xsl:element>

				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>



	<xsl:template match="tei:note">
		<xsl:element name="note" namespace="">
			<xsl:apply-templates select="@*"/>
			<xsl:if test="contains(@xml:id, '-fn')">
				<label><xsl:value-of select="substring-after(@xml:id, '-fn')"/></label>
			</xsl:if>
			<xsl:if test="@n">
				<xsl:element name="span" namespace="">
					<xsl:attribute name="class">label</xsl:attribute>
			 		<xsl:value-of select="@n"/>.
				</xsl:element>
			</xsl:if>
			<xsl:apply-templates />
		</xsl:element>
	</xsl:template>


	<xsl:template match="tei:unclear">
		<xsl:element name="unclear" namespace="">
			<xsl:choose>
				<xsl:when test="string(.) = ''">illegible</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:element>
	</xsl:template>



	<xsl:template match="tei:div[@type='insertion']">
		<xsl:element name="div" namespace="">
			<xsl:attribute name="class">insertion</xsl:attribute>
			<xsl:attribute name="id"><xsl:value-of select="@xml:id"/></xsl:attribute>
			<xsl:element name="h3" namespace="">
				<xsl:attribute name="class">insertionHeading</xsl:attribute>Insertion
				<xsl:value-of select="substring-after(@xml:id, 'ins')"/>
			</xsl:element>
			<xsl:copy-of select="*"/>
		</xsl:element>
	</xsl:template>

<!--
		*
		*
		*
		* Templates from the old FFP xslt 2.0, converted for our uses
		*
		*
		*
		*
-->

	<xsl:include href="tei-attributes.xsl"/>

	<xsl:include href="tei-tables.xsl"/>

	<xsl:include href="date-formatting.xsl"/>

</xsl:stylesheet>
