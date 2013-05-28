<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:fn="http://www.w3.org/2004/07/xpath-functions"
	xmlns:xdt="http://www.w3.org/2004/07/xpath-datatypes" xmlns:fox="http://xml.apache.org/fop/extensions"
	xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:func="http://exslt.org/functions"
	xmlns:php="http://php.net/xsl" 
	xmlns:functx="http://www.functx.com" 
	extension-element-prefixes="func php functx"  
	exclude-result-prefixes="xsl fo xs fn xdt fox xlink xhtml func php functx">
	

	<!-- <xsl:namespace-alias stylesheet-prefix="php" result-prefix="xsl" /> -->

	<xsl:import href="loop_params.xsl"></xsl:import>
	<xsl:import href="loop_terms.xsl"></xsl:import>

	<xsl:output method="html" version="1.0" encoding="UTF-8"
		indent="yes"></xsl:output>


	<xsl:variable name="lang">
		<xsl:value-of select="/articles/@lang"></xsl:value-of>
	</xsl:variable>

	<xsl:template match="articles">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>
		<xsl:param name="media_exists"><xsl:call-template name="media_exists"></xsl:call-template></xsl:param>
		<xsl:param name="task_exists"><xsl:call-template name="task_exists"></xsl:call-template></xsl:param>			
		<xsl:param name="index_exists"><xsl:call-template name="index_exists"></xsl:call-template></xsl:param>
		<!-- 
		<xsl:result-document href="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/tmp/test.xml" >
			<xsl:element name="config" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
				<xsl:element name="general">
					<xsl:element name="contact">test@tester.de</xsl:element>
					<xsl:element name="server">testserver</xsl:element>
					<xsl:element name="role">student</xsl:element>
					<xsl:element name="pagebreak_level">lo</xsl:element>				
					<xsl:element name="chapter_numeration">yes</xsl:element>
					<xsl:element name="manifest_type">scorm</xsl:element>
				</xsl:element>
			</xsl:element>				
		</xsl:result-document>
		-->
		<!-- <fo:root> -->
			<!-- 
			<fo:layout-master-set>
				<fo:simple-page-master master-name="cover-page"
					page-height="{$pageheight}" page-width="{$pagewidth}" margin-top="10mm"
					margin-bottom="10mm" margin-left="25mm" margin-right="15mm">
					<fo:region-body margin-top="10mm" margin-bottom="15mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="full-page"
					page-height="{$pageheight}" page-width="{$pagewidth}" margin-top="10mm"
					margin-bottom="5mm" margin-left="25mm" margin-right="15mm">
					<fo:region-body margin-top="15mm" margin-bottom="15mm" />
					<fo:region-before extent="20mm" />
					<fo:region-after extent="15mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="default-page"
					page-height="{$pageheight}" page-width="{$pagewidth}" margin-top="10mm"
					margin-bottom="5mm" margin-left="25mm" margin-right="15mm">
					<fo:region-body margin-top="15mm" margin-bottom="15mm"
						margin-left="20mm"/>
					<fo:region-before extent="20mm" />
					<fo:region-after extent="15mm" />
				</fo:simple-page-master>
				<fo:simple-page-master master-name="full-page-2column"
					page-height="{$pageheight}" page-width="{$pagewidth}" margin-top="10mm"
					margin-bottom="5mm" margin-left="25mm" margin-right="15mm">
					<fo:region-body margin-top="15mm" margin-bottom="15mm" column-count="2" column-gap="10mm"/>
					<fo:region-before extent="20mm" />
					<fo:region-after extent="15mm" />
				</fo:simple-page-master>				
			</fo:layout-master-set>
			
			<xsl:call-template name="make-declarations"></xsl:call-template>
			<xsl:call-template name="make-bookmark-tree"></xsl:call-template>
			
			<xsl:call-template name="page-sequence-cover"></xsl:call-template>
			<xsl:call-template name="page-sequence-table-of-content"></xsl:call-template>
			-->
			
			<xsl:call-template name="page-sequence-contentpages"></xsl:call-template>
			
			<!-- 
			<xsl:if test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1') or ($media_exists='1') or ($task_exists='1') or ($index_exists='1')">
				<xsl:call-template name="page-sequence-appendix"></xsl:call-template>
			</xsl:if>
 			-->
		
	</xsl:template>

	<xsl:template name="page-sequence-appendix">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>
		<xsl:param name="media_exists"><xsl:call-template name="media_exists"></xsl:call-template></xsl:param>
		<xsl:param name="task_exists"><xsl:call-template name="task_exists"></xsl:call-template></xsl:param>
		<xsl:param name="index_exists"><xsl:call-template name="index_exists"></xsl:call-template></xsl:param>			
		<fo:page-sequence master-reference="full-page" id="appendix_sequence">
			<fo:static-content font-family="Cambria,'Cambria Math'" flow-name="xsl-region-before">
				<xsl:call-template name="default-header"></xsl:call-template>			
			</fo:static-content>			
			<fo:static-content font-family="Cambria,'Cambria Math'" flow-name="xsl-region-after">
				<xsl:call-template name="default-footer"></xsl:call-template>
			</fo:static-content>
			<fo:flow font-family="Cambria,'Cambria Math'" flow-name="xsl-region-body">
				<xsl:call-template name="page-content-appendix"></xsl:call-template>
				
                <xsl:if test="$cite_exists='1'">
                    <xsl:call-template name="page-content-bibliography"></xsl:call-template>
                </xsl:if>				
                <xsl:if test="$figure_exists='1'">
                    <xsl:call-template name="page-content-list-of-figures"></xsl:call-template>
                </xsl:if>
                <xsl:if test="$table_exists='1'">
                    <xsl:call-template name="page-content-list-of-tables"></xsl:call-template>
                </xsl:if>
                <xsl:if test="$media_exists='1'">
                    <xsl:call-template name="page-content-list-of-media"></xsl:call-template>
                </xsl:if>
                <xsl:if test="$task_exists='1'">
                    <xsl:call-template name="page-content-list-of-tasks"></xsl:call-template>
                </xsl:if>
                
			</fo:flow>
		</fo:page-sequence>	
		<xsl:if test="$index_exists='1'">
		<fo:page-sequence master-reference="full-page-2column" id="index_sequence">
			<fo:static-content font-family="Cambria,'Cambria Math'" flow-name="xsl-region-before">
				<xsl:call-template name="default-header"></xsl:call-template>			
			</fo:static-content>			
			<fo:static-content font-family="Cambria,'Cambria Math'" flow-name="xsl-region-after">
				<xsl:call-template name="default-footer"></xsl:call-template>
			</fo:static-content>
			<fo:flow font-family="Cambria,'Cambria Math'" flow-name="xsl-region-body">		
            	<xsl:call-template name="page-content-index"></xsl:call-template>
			</fo:flow>
		</fo:page-sequence>	            	
        </xsl:if>                                
	</xsl:template>		


	<xsl:template name="page-content-appendix">
		<fo:block id="appendix"></fo:block>
	</xsl:template>	
	
	<xsl:template name="page-content-index">
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'index'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
				<xsl:value-of select="$word_index"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="index" keep-with-next="always">
			<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'index'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
			<xsl:value-of select="$word_index"></xsl:value-of>
		</fo:block>
		<fo:block>
			<xsl:apply-templates select="php:function('xslt_get_index', '')"></xsl:apply-templates>
		</fo:block>
	</xsl:template>		

	<xsl:template match="loop_index">
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="loop_index_group">
		<fo:block keep-together.within-column="always">
		<fo:block margin-top="5mm" font-weight="bold">
			<xsl:value-of select="@letter"></xsl:value-of>
		</fo:block>
		<xsl:apply-templates></xsl:apply-templates>
		</fo:block>
	</xsl:template>

	<xsl:template match="loop_index_item">
		<fo:block>
			<xsl:apply-templates></xsl:apply-templates>
		</fo:block>
	</xsl:template>

	<xsl:template match="loop_index_title">
		<xsl:value-of select="."></xsl:value-of>
		<xsl:text> </xsl:text>
	</xsl:template>

	<xsl:template match="loop_index_pages">
		<xsl:text> </xsl:text>
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="loop_index_page">
		<xsl:if test="@further=1">
			<xsl:text>,</xsl:text>
		</xsl:if>
		<xsl:text> </xsl:text>
		<fo:basic-link ><!-- text-decoration="underline" -->
			<xsl:attribute name="internal-destination"><xsl:value-of select="@pagetitle"></xsl:value-of></xsl:attribute>
			<fo:page-number-citation>
				<xsl:attribute name="ref-id" ><xsl:value-of select="@pagetitle"></xsl:value-of></xsl:attribute>
			</fo:page-number-citation>	
		</fo:basic-link>
	</xsl:template>

	
	<xsl:template name="page-content-bibliography">
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'bibliography'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
				<xsl:value-of select="$word_bibliography"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="bibliography" keep-with-next="always">
			<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'bibliography'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
			<xsl:value-of select="$word_bibliography"></xsl:value-of>
		</fo:block>
		<fo:block>
			<xsl:apply-templates select="php:function('get_biblio', '')"></xsl:apply-templates>
		</fo:block>
	</xsl:template>		


	<xsl:template name="page-content-list-of-figures">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:if test="$cite_exists='1'">
			<fo:block break-before="page"></fo:block>
		</xsl:if>		
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_figures'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
				<xsl:value-of select="$word_list_of_figures"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="list_of_figures" keep-with-next="always"  margin-bottom="10mm">
			<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_figures'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
			<xsl:value-of select="$word_list_of_figures"></xsl:value-of>
		</fo:block>
		<fo:table width="170mm" table-layout="fixed">
			<fo:table-body>
				<xsl:apply-templates select="//*/extension[@extension_name='loop_figure']" mode="list_of_figures"></xsl:apply-templates>
			</fo:table-body>
		</fo:table>		
		
	</xsl:template>
	
	<xsl:template match="extension" mode="list_of_figures">
		<fo:table-row>
			<fo:table-cell width="30mm">
				<fo:block>
				
				<fo:basic-link >
					<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
					
					<fo:block>
					<fo:external-graphic scaling="uniform" content-width="24mm" content-height="scale-to-fit">
						<xsl:attribute name="src"><xsl:value-of select="php:function('xslt_imagepath', link/target)"></xsl:value-of></xsl:attribute>
					</fo:external-graphic>
					</fo:block>
					
				</fo:basic-link>
				
				
				
				</fo:block>
			</fo:table-cell>
			
			<xsl:variable name="linktext">
				<xsl:choose>
					<xsl:when test="@title">
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@description"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			
			
			<fo:table-cell width="140mm">
				<fo:block text-align-last="justify" text-align="justify">
						<fo:basic-link color="black">
							<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							
							
							<xsl:value-of select="$linktext"></xsl:value-of>
						
						<fo:inline keep-together.within-line="always">
							<fo:leader leader-pattern="dots"></fo:leader>
							<fo:page-number-citation>
								<xsl:attribute name="ref-id"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							</fo:page-number-citation>
						</fo:inline>
						</fo:basic-link>					
				</fo:block>
			</fo:table-cell>			
		</fo:table-row>
	</xsl:template>
	
	
	
	<xsl:template name="page-content-list-of-tables">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:if test="($cite_exists='1') or ($figure_exists='1')">
			<fo:block break-before="page"></fo:block>
		</xsl:if>		
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_tables'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
				<xsl:value-of select="$word_list_of_tables"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="list_of_tables" keep-with-next="always" margin-bottom="10mm">
			<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_tables'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
			<xsl:value-of select="$word_list_of_tables"></xsl:value-of>
		</fo:block>
		<fo:table width="170mm" table-layout="fixed">
			<fo:table-body>
				<xsl:apply-templates select="//*/extension[@extension_name='loop_table']" mode="list_of_tables"></xsl:apply-templates>
			</fo:table-body>
		</fo:table>				
	</xsl:template>	

	<xsl:template match="extension" mode="list_of_tables">
		<fo:table-row>
			<fo:table-cell width="15mm">
				<fo:block>
				<fo:basic-link >
					<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
					<fo:block>
						<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/skins/loop/images/media/type_table.png"></fo:external-graphic>
					</fo:block>
				</fo:basic-link>
				</fo:block>
			</fo:table-cell>
			<xsl:variable name="linktext">
				<xsl:choose>
					<xsl:when test="@title">
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@description"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<fo:table-cell width="155mm">
				<fo:block text-align-last="justify" text-align="justify">
						<fo:basic-link color="black">
							<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							
							
							<xsl:value-of select="$linktext"></xsl:value-of>
						
						<fo:inline keep-together.within-line="always">
							<fo:leader leader-pattern="dots"></fo:leader>
							<fo:page-number-citation>
								<xsl:attribute name="ref-id"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							</fo:page-number-citation>
						</fo:inline>
						</fo:basic-link>					
				</fo:block>
			</fo:table-cell>			
		</fo:table-row>
	</xsl:template>


	<xsl:template name="page-content-list-of-media">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>
		<xsl:if test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1')">
			<fo:block break-before="page"></fo:block>
		</xsl:if>		
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_media'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
				<xsl:value-of select="$word_list_of_media"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="list_of_media" keep-with-next="always" margin-bottom="10mm">
			<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_media'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
			<xsl:value-of select="$word_list_of_media"></xsl:value-of>
		</fo:block>
		<fo:table width="170mm" table-layout="fixed">
			<fo:table-body>
				<xsl:apply-templates select="//*/extension[@extension_name='loop_media']" mode="list_of_media"></xsl:apply-templates>
			</fo:table-body>
		</fo:table>				
	</xsl:template>	
	
	<xsl:template match="extension" mode="list_of_media">
		<fo:table-row>
			<fo:table-cell width="15mm">
				<fo:block>
				<fo:basic-link >
					<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
					<fo:block>
						<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/skins/loop/images/media/type_video.png"></fo:external-graphic>
					</fo:block>
				</fo:basic-link>
				</fo:block>
			</fo:table-cell>
			<xsl:variable name="linktext">
				<xsl:choose>
					<xsl:when test="@title">
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@description"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<fo:table-cell width="155mm">
				<fo:block text-align-last="justify" text-align="justify">
						<fo:basic-link color="black">
							<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							<xsl:value-of select="$linktext"></xsl:value-of>
						<fo:inline keep-together.within-line="always">
							<fo:leader leader-pattern="dots"></fo:leader>
							<fo:page-number-citation>
								<xsl:attribute name="ref-id"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							</fo:page-number-citation>
						</fo:inline>
						</fo:basic-link>					
				</fo:block>
			</fo:table-cell>			
		</fo:table-row>
	</xsl:template>
	
	
	<xsl:template name="page-content-list-of-tasks">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>
		<xsl:param name="media_exists"><xsl:call-template name="media_exists"></xsl:call-template></xsl:param>
		<xsl:if test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1') or ($media_exists='1')">
			<fo:block break-before="page"></fo:block>
		</xsl:if>		
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_tasks'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
				<xsl:value-of select="$word_list_of_tasks"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="list_of_tasks" keep-with-next="always" margin-bottom="10mm">
			<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:call-template name="appendix_number">
					<xsl:with-param name="content" select="'list_of_tasks'"></xsl:with-param>
				</xsl:call-template>
				<xsl:text> </xsl:text>			
			<xsl:value-of select="$word_list_of_tasks"></xsl:value-of>
		</fo:block>
		<fo:table width="170mm" table-layout="fixed">
			<fo:table-body>
				<xsl:apply-templates select="//*/extension[@extension_name='loop_task']" mode="list_of_tasks"></xsl:apply-templates>
			</fo:table-body>
		</fo:table>				
	</xsl:template>	
	
	<xsl:template match="extension" mode="list_of_tasks">
		<fo:table-row>
			<fo:table-cell width="15mm">
				<fo:block>
				<fo:basic-link >
					<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
					<fo:block>
						<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/skins/loop/images/media/type_task.png"></fo:external-graphic>
					</fo:block>
				</fo:basic-link>
				</fo:block>
			</fo:table-cell>
			<xsl:variable name="linktext">
				<xsl:choose>
					<xsl:when test="@title">
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@description"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<fo:table-cell width="155mm">
				<fo:block text-align-last="justify" text-align="justify">
						<fo:basic-link color="black">
							<xsl:attribute name="internal-destination"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							<xsl:value-of select="$linktext"></xsl:value-of>
						<fo:inline keep-together.within-line="always">
							<fo:leader leader-pattern="dots"></fo:leader>
							<fo:page-number-citation>
								<xsl:attribute name="ref-id"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
							</fo:page-number-citation>
						</fo:inline>
						</fo:basic-link>					
				</fo:block>
			</fo:table-cell>			
		</fo:table-row>
	</xsl:template>



	<!-- Page Sequence für Cover-Page -->
	<xsl:template name="page-sequence-cover">
		<fo:page-sequence master-reference="cover-page" id="cover_sequence">
			<fo:flow font-family="Cambria,'Cambria Math'" flow-name="xsl-region-body">
				<xsl:call-template name="page-content-cover"></xsl:call-template>
			</fo:flow>
		</fo:page-sequence>
	</xsl:template>

	<!-- Page Content der Cover-Page -->
	<xsl:template name="page-content-cover">
		<fo:block text-align="right" font-size="26pt" font-weight="bold"
			id="cover" margin-bottom="10mm" margin-top="40mm">
			<xsl:value-of select="/articles/@title"></xsl:value-of>
		</fo:block>
		<fo:block text-align="right" font-size="14pt" font-weight="normal"
			margin-bottom="5mm">
			<xsl:value-of select="/articles/@url"></xsl:value-of>
		</fo:block>
		<fo:block text-align="right" font-size="12pt" margin-bottom="10mm">
			<xsl:value-of select="$word_state"></xsl:value-of>
			<xsl:text> </xsl:text>
			<xsl:value-of select="/articles/@date"></xsl:value-of>
		</fo:block>
		<fo:block text-align="right" font-size="12pt" margin-bottom="0mm" margin-top="130mm" margin-right="-5mm">
			<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="30mm" >
				<xsl:attribute name="src">
					<xsl:value-of select="/articles/@qrimage"></xsl:value-of>
				</xsl:attribute>
			</fo:external-graphic>
		</fo:block>
		<fo:block>
			<!--  <fo:external-graphic scaling="uniform" content-height="50mm" content-width="100mm" src="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/extensions/Loop/tmp/marknew.png"></fo:external-graphic> -->
			
			
			
		</fo:block>
		
	</xsl:template>

	<!-- Page Sequence für Inhaltsverzeichnis -->
	<xsl:template name="page-sequence-table-of-content">
		<fo:page-sequence master-reference="full-page"
			id="table_of_content_sequence">
			<fo:static-content font-family="Cambria,'Cambria Math'"
				flow-name="xsl-region-before">
				<xsl:call-template name="default-header"></xsl:call-template>
			</fo:static-content>
			<fo:static-content font-family="Cambria,'Cambria Math'"
				flow-name="xsl-region-after">
				<xsl:call-template name="default-footer"></xsl:call-template>
			</fo:static-content>
			<fo:flow font-family="Cambria,'Cambria Math'" flow-name="xsl-region-body"
				text-align="justify" font-size="11.5pt" line-height="15.5pt"
				orphans="3">
				<xsl:call-template name="page-content-table-of-content"></xsl:call-template>
			</fo:flow>
		</fo:page-sequence>
	</xsl:template>

	<!-- Page Content des Inhaltsverzeichnises -->
	<xsl:template name="page-content-table-of-content">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>	
		<xsl:param name="media_exists"><xsl:call-template name="media_exists"></xsl:call-template></xsl:param>
		<xsl:param name="task_exists"><xsl:call-template name="task_exists"></xsl:call-template></xsl:param>
		<xsl:param name="index_exists"><xsl:call-template name="index_exists"></xsl:call-template></xsl:param>
		<fo:block>
			<fo:marker marker-class-name="page-title-left">
				<xsl:value-of select="/articles/@title"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block>
			<fo:marker marker-class-name="page-title-right">
				<xsl:value-of select="$word_content"></xsl:value-of>
			</fo:marker>
		</fo:block>
		<fo:block id="table_of_content">
			<xsl:call-template name="font_head"></xsl:call-template>
			<xsl:value-of select="$word_content"></xsl:value-of>
		</fo:block>
		
		<xsl:call-template name="make-toc"></xsl:call-template>
		
		<xsl:if test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1') or ($media_exists='1') or ($task_exists='1') or ($index_exists='1')">
			<fo:block margin-bottom="1em"></fo:block>
			<fo:block>
				<xsl:call-template name="font_subsubhead"></xsl:call-template>
				<xsl:value-of select="$word_appendix"></xsl:value-of>
			</fo:block>		
		</xsl:if>
		
		<xsl:if test="$cite_exists='1'">
			<fo:block text-align-last="justify">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<fo:basic-link color="black">
					<xsl:attribute name="internal-destination">bibliography</xsl:attribute>
					<xsl:call-template name="appendix_number">
						<xsl:with-param name="content" select="'bibliography'"></xsl:with-param>
					</xsl:call-template>					
					<xsl:text> </xsl:text><xsl:value-of select="$word_bibliography"></xsl:value-of>
				</fo:basic-link>
				<fo:inline keep-together.within-line="always">
					<fo:leader leader-pattern="dots"></fo:leader>
					<fo:page-number-citation>
						<xsl:attribute name="ref-id">bibliography</xsl:attribute>
					</fo:page-number-citation>
				</fo:inline>
			</fo:block>		
		</xsl:if>	
		<xsl:if test="$figure_exists='1'">
			<fo:block text-align-last="justify">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<fo:basic-link color="black">
					<xsl:attribute name="internal-destination">list_of_figures</xsl:attribute>
					<xsl:call-template name="appendix_number">
						<xsl:with-param name="content" select="'list_of_figures'"></xsl:with-param>
					</xsl:call-template>					
					<xsl:text> </xsl:text><xsl:value-of select="$word_list_of_figures"></xsl:value-of>
				</fo:basic-link>
				<fo:inline keep-together.within-line="always">
					<fo:leader leader-pattern="dots"></fo:leader>
					<fo:page-number-citation>
						<xsl:attribute name="ref-id" >list_of_figures</xsl:attribute>
					</fo:page-number-citation>
				</fo:inline>
			</fo:block>		
		</xsl:if>
		<xsl:if test="$table_exists='1'">
			<fo:block text-align-last="justify">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<fo:basic-link color="black">
					<xsl:attribute name="internal-destination">list_of_tables</xsl:attribute>
					<xsl:call-template name="appendix_number">
						<xsl:with-param name="content" select="'list_of_tables'"></xsl:with-param>
					</xsl:call-template>					
					<xsl:text> </xsl:text><xsl:value-of select="$word_list_of_tables"></xsl:value-of>
				</fo:basic-link>
				<fo:inline keep-together.within-line="always">
					<fo:leader leader-pattern="dots"></fo:leader>
					<fo:page-number-citation>
						<xsl:attribute name="ref-id" >list_of_tables</xsl:attribute>
					</fo:page-number-citation>
				</fo:inline>
			</fo:block>		
		</xsl:if>			
		<xsl:if test="$media_exists='1'">
			<fo:block text-align-last="justify">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<fo:basic-link color="black">
					<xsl:attribute name="internal-destination">list_of_media</xsl:attribute>
					<xsl:call-template name="appendix_number">
						<xsl:with-param name="content" select="'list_of_media'"></xsl:with-param>
					</xsl:call-template>					
					<xsl:text> </xsl:text><xsl:value-of select="$word_list_of_media"></xsl:value-of>
				</fo:basic-link>
				<fo:inline keep-together.within-line="always">
					<fo:leader leader-pattern="dots"></fo:leader>
					<fo:page-number-citation>
						<xsl:attribute name="ref-id" >list_of_media</xsl:attribute>
					</fo:page-number-citation>
				</fo:inline>
			</fo:block>		
		</xsl:if>		
		<xsl:if test="$task_exists='1'">
			<fo:block text-align-last="justify">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<fo:basic-link color="black">
					<xsl:attribute name="internal-destination">list_of_tasks</xsl:attribute>
					<xsl:call-template name="appendix_number">
						<xsl:with-param name="content" select="'list_of_tasks'"></xsl:with-param>
					</xsl:call-template>					
					<xsl:text> </xsl:text><xsl:value-of select="$word_list_of_tasks"></xsl:value-of>
				</fo:basic-link>
				<fo:inline keep-together.within-line="always">
					<fo:leader leader-pattern="dots"></fo:leader>
					<fo:page-number-citation>
						<xsl:attribute name="ref-id" >list_of_tasks</xsl:attribute>
					</fo:page-number-citation>
				</fo:inline>
			</fo:block>		
		</xsl:if>				
		<xsl:if test="$index_exists='1'">
			<fo:block text-align-last="justify">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<fo:basic-link color="black">
					<xsl:attribute name="internal-destination">index</xsl:attribute>
					<xsl:call-template name="appendix_number">
						<xsl:with-param name="content" select="'index'"></xsl:with-param>
					</xsl:call-template>					
					<xsl:text> </xsl:text><xsl:value-of select="$word_index"></xsl:value-of>
				</fo:basic-link>
				<fo:inline keep-together.within-line="always">
					<fo:leader leader-pattern="dots"></fo:leader>
					<fo:page-number-citation>
						<xsl:attribute name="ref-id" >index</xsl:attribute>
					</fo:page-number-citation>
				</fo:inline>
			</fo:block>		
		</xsl:if>		
		
		
	</xsl:template>


	<!-- Page Sequence für Wiki-Seiten -->
	<xsl:template name="page-sequence-contentpages">
		<xsl:call-template name="page-content-contentpages"></xsl:call-template>
	</xsl:template>

	<!-- Page Content einer Wiki-Seite -->
	<xsl:template name="page-content-contentpages">
		<xsl:apply-templates select="article"></xsl:apply-templates>
	</xsl:template>


	<!-- Page Content einer Wiki-Seite -->
	<xsl:template match="article">
		<xsl:variable name="toclevel" select="@toclevel"></xsl:variable>
		<xsl:choose>
			<xsl:when test="@toclevel=''">
				<xsl:apply-templates></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
<br/><xsl:text>
				
</xsl:text>
<h1>
<xsl:value-of select="$word_chapter"></xsl:value-of>
<xsl:text> </xsl:text>
<xsl:value-of select="@tocnumber"></xsl:value-of>
</h1>
<br/>
<xsl:text>
</xsl:text>
<h2><xsl:value-of select="@title"></xsl:value-of></h2>
<br/>
<xsl:text>
	
</xsl:text>

<xsl:apply-templates></xsl:apply-templates>

			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<!-- Default Header -->
	<xsl:template name="default-header">
		<fo:table table-layout="fixed" width="100%" margin-bottom="2mm">
			<fo:table-body>
				<fo:table-row>
					<fo:table-cell text-align="left">
						<fo:block line-height="13pt" margin-bottom="-3mm"
							font-weight="bold">
							<fo:retrieve-marker retrieve-class-name="page-title-left"
								retrieve-position="first-starting-within-page"
								retrieve-boundary="page-sequence"></fo:retrieve-marker>
						</fo:block>
					</fo:table-cell>
					<fo:table-cell text-align="right">
						<fo:block line-height="13pt" margin-bottom="-3mm">
							<fo:retrieve-marker retrieve-class-name="page-title-right"
								retrieve-position="first-including-carryover" retrieve-boundary="page-sequence"></fo:retrieve-marker>
						</fo:block>
					</fo:table-cell>
				</fo:table-row>
			</fo:table-body>
		</fo:table>
		<fo:block>
			<fo:leader leader-pattern="rule" leader-length="100%"
				rule-thickness="0.5pt" rule-style="solid" color="black"
				display-align="after"></fo:leader>
		</fo:block>
	</xsl:template>

	<!-- Default Footer -->
	<xsl:template name="default-footer">
		<xsl:param name="last-page-sequence-name">
			<xsl:call-template name="last-page-sequence-name"></xsl:call-template>
		</xsl:param>
		<fo:block>
			<fo:leader leader-pattern="rule" leader-length="100%"
				rule-thickness="0.5pt" rule-style="solid" color="black"
				display-align="before"></fo:leader>
		</fo:block>
		<fo:block text-align="right">
			<fo:page-number></fo:page-number>
			/
			<fo:page-number-citation-last ref-id="{$last-page-sequence-name}"></fo:page-number-citation-last>
		</fo:block>
	</xsl:template>
	
	<xsl:template match="heading">
		<xsl:variable name="level" select="@level"></xsl:variable>
		<xsl:choose>
			<xsl:when test=".=ancestor::article/@title">
			
			</xsl:when>
			<xsl:otherwise>
				
					<xsl:choose>
						<xsl:when test="$level='1'">
							<h1><xsl:value-of select="."></xsl:value-of></h1>
						</xsl:when>
						<xsl:when test="$level='2'">
							<h2><xsl:value-of select="."></xsl:value-of></h2>
						</xsl:when>
						<xsl:when test="$level='3'">
							<h3><xsl:value-of select="."></xsl:value-of></h3>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="."></xsl:value-of>
						</xsl:otherwise>
					</xsl:choose>
					
				
			</xsl:otherwise>
		</xsl:choose>
		
	</xsl:template>	
	
	<xsl:template match="paragraph">
		<xsl:if test="not(starts-with(.,'#index:'))">
			<br/><xsl:text>
			</xsl:text><xsl:apply-templates></xsl:apply-templates><br/><xsl:text>
			</xsl:text>
		</xsl:if>
	</xsl:template>
	
	
	<xsl:template match="br">
<br/><xsl:text>
</xsl:text>
	</xsl:template>
		

	<xsl:template match="sub">
		<sub><xsl:apply-templates></xsl:apply-templates></sub>
	</xsl:template>	
	
	<xsl:template match="sup">
		<sup><xsl:apply-templates></xsl:apply-templates></sup>
	</xsl:template>	
	
	<xsl:template match="big">
		<big><xsl:apply-templates></xsl:apply-templates></big>
	</xsl:template>	
	
	<xsl:template match="small">
		<small><xsl:apply-templates></xsl:apply-templates></small>
	</xsl:template>			

	<xsl:template match="bold">
		<strong><xsl:apply-templates></xsl:apply-templates></strong>
	</xsl:template>

	<xsl:template match="italics">
		<em><xsl:apply-templates></xsl:apply-templates></em>
	</xsl:template>


	<xsl:template match="space">
		<xsl:text> </xsl:text>
	</xsl:template>	
	
	
	
	
	
	<xsl:template match="extension">
		<!-- 
		<fo:block>
			<xsl:attribute name="id"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
		</fo:block>
		 -->
		<xsl:choose>
			<xsl:when test="@extension_name='loop_area'">
<xsl:text>
				
</xsl:text>			
<xsl:call-template name="looparea_name"><xsl:with-param name="areaname" select="@type"></xsl:with-param></xsl:call-template>			
<xsl:text>
				
</xsl:text>			
<xsl:apply-templates></xsl:apply-templates>			
			</xsl:when>
			<xsl:when test="@extension_name='loop_toc'">
				<br/>
				<xsl:variable name="toctitle" select="ancestor::article[1]/@title"></xsl:variable>
				<xsl:apply-templates select="/articles/toc/descendant-or-self::page[@title=$toctitle]/."></xsl:apply-templates>
				<xsl:apply-templates select="/articles/toc/descendant-or-self::page[@title=$toctitle]/*"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="@extension_name='loop_figure'">
<xsl:text>
</xsl:text><br/>			
<xsl:value-of select="$word_loopfigure"></xsl:value-of><br/>
<xsl:text>
</xsl:text>
<xsl:apply-templates></xsl:apply-templates>
<xsl:if test="@title">
	<xsl:value-of select="@title"></xsl:value-of><br/>
</xsl:if>
<xsl:if test="@description">
	<xsl:value-of select="@description"></xsl:value-of><br/>
</xsl:if>
<br/>
			</xsl:when>
			<xsl:when test="@extension_name='loop_task'">
				<xsl:apply-templates></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="@extension_name='loop_table'">
<xsl:text>
</xsl:text><br/>			
<xsl:value-of select="$word_loopfigure"></xsl:value-of><br/>
<xsl:text>
</xsl:text>
<xsl:apply-templates></xsl:apply-templates>
<xsl:if test="@title">
	<xsl:value-of select="@title"></xsl:value-of><br/>
</xsl:if>
<xsl:if test="@description">
	<xsl:value-of select="@description"></xsl:value-of><br/>
</xsl:if>
<br/>			
				<xsl:apply-templates></xsl:apply-templates>
			</xsl:when>	
			<xsl:when test="@extension_name='loop_media'">
<xsl:value-of select="$word_loopmedia_notice"></xsl:value-of>
<xsl:apply-templates></xsl:apply-templates>
<xsl:if test="@title">
	<xsl:value-of select="@title"></xsl:value-of><br/>
</xsl:if>
<xsl:if test="@description">
	<xsl:value-of select="@description"></xsl:value-of><br/>
</xsl:if>
<br/>
			</xsl:when>		
			<xsl:when test="@extension_name='math'">
<!-- 
				<fo:block>
			
				<fo:external-graphic scaling="uniform" content-width="60%">
					<xsl:attribute name="src">
					<xsl:value-of select="php:function('xslt_transform_math', .)"></xsl:value-of>
					</xsl:attribute> 
				 </fo:external-graphic>
				</fo:block>
 -->
 	<xsl:value-of select="."></xsl:value-of>				
			</xsl:when>
			<xsl:when test="@extension_name='loop_print'">
				<br/>
					<xsl:apply-templates></xsl:apply-templates>
				<br/>
			</xsl:when>			
			<xsl:when test="@extension_name='quiz'">
				<br/>
					<xsl:value-of select="$word_quiz_notice"></xsl:value-of>
				<br/>	
			</xsl:when>				
			
			
	
		</xsl:choose>

	</xsl:template>	
	


<xsl:template match="table">
	<table><xsl:apply-templates></xsl:apply-templates></table>
</xsl:template>

<xsl:template match="tablerow">
	<tr><xsl:apply-templates></xsl:apply-templates></tr>
</xsl:template>

    <xsl:template match="tablecell">
        <td><xsl:apply-templates></xsl:apply-templates></td>
    </xsl:template>

    <xsl:template match="tablehead">
		<th><xsl:apply-templates></xsl:apply-templates></th>
    </xsl:template>

	
	<xsl:template match="chapter">
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="page">
		<!-- <fo:block>
			<xsl:value-of select="@tocnumber"></xsl:value-of>
			<xsl:text> </xsl:text>
			<fo:basic-link text-decoration="underline">
				<xsl:attribute name="internal-destination"><xsl:value-of select="@title"></xsl:value-of></xsl:attribute>
				<xsl:value-of select="@title"></xsl:value-of>
			</fo:basic-link>		
		</fo:block>
		 -->
		 
		<xsl:value-of select="@tocnumber"></xsl:value-of>
		<xsl:text> </xsl:text>
		<a>
		<xsl:attribute name="href"><xsl:value-of select="@title"></xsl:value-of></xsl:attribute>
		<xsl:value-of select="@title"></xsl:value-of>
		</a><br/><xsl:text>
		</xsl:text>
	</xsl:template>
	
	
	<xsl:template name="looparea_name">
		<xsl:param name="areaname"></xsl:param>
		<xsl:choose>
			<xsl:when test="$areaname='task'"><xsl:value-of select="$word_looparea_task"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='timerequirement'"><xsl:value-of select="$word_looparea_timerequirement"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='learningobjectives'"><xsl:value-of select="$word_looparea_learningobjectives"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='arrangement'"><xsl:value-of select="$word_looparea_arrangement"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='example'"><xsl:value-of select="$word_looparea_example"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='reflection'"><xsl:value-of select="$word_looparea_reflection"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='notice'"><xsl:value-of select="$word_looparea_notice"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='important'"><xsl:value-of select="$word_looparea_important"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='annotation'"><xsl:value-of select="$word_looparea_annotation"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='definition'"><xsl:value-of select="$word_looparea_definition"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='formula'"><xsl:value-of select="$word_looparea_formula"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='markedsentence'"><xsl:value-of select="$word_looparea_markedsentence"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='sourcecode'"><xsl:value-of select="$word_looparea_sourcecode"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='summary'"><xsl:value-of select="$word_looparea_summary"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='indentation'"><xsl:value-of select="$word_looparea_indentation"></xsl:value-of></xsl:when>
			<xsl:when test="$areaname='norm'"><xsl:value-of select="$word_looparea_norm"></xsl:value-of></xsl:when>
						
		</xsl:choose>
		
	</xsl:template>
	
	
	

	<xsl:template match="list">
		<ul>
			<xsl:apply-templates></xsl:apply-templates>
		</ul>
	</xsl:template>



	<xsl:template match="listitem">
<li>
<xsl:apply-templates select="*[not(name()='list')] | text()"></xsl:apply-templates>
<xsl:apply-templates select="list"></xsl:apply-templates>
</li>
<xsl:text>
</xsl:text>
	<!-- 
		<fo:list-item>
			<fo:list-item-label end-indent="label-end()">
				<xsl:choose>
					<xsl:when test="../@type='numbered'">
						<fo:block><xsl:number level="single" count="listitem" format="1."/></fo:block>
					</xsl:when>
					<xsl:when test="../@type='ident'">
						<fo:block padding-before="2pt"></fo:block>
					</xsl:when>						
					<xsl:otherwise>
						<fo:block padding-before="2pt">&#x2022;</fo:block>
					</xsl:otherwise>
				</xsl:choose>
			</fo:list-item-label>
			<fo:list-item-body start-indent="body-start()">
				<fo:block>
					<xsl:apply-templates select="*[not(name()='list')] | text()"></xsl:apply-templates>
				</fo:block>
				<xsl:apply-templates select="list"></xsl:apply-templates>
			</fo:list-item-body>
		</fo:list-item>
		 -->
	</xsl:template>
	
	
	<xsl:template name="font_icon">
		<!-- 
		<xsl:attribute name="font-size" >8.5pt</xsl:attribute>
		<xsl:attribute name="font-weight" >bold</xsl:attribute>
		<xsl:attribute name="line-height" >12pt</xsl:attribute>
		<xsl:attribute name="margin-bottom" >1mm</xsl:attribute>
		 -->
	</xsl:template>	

	<xsl:template name="font_small">
	<!--
		<xsl:attribute name="font-size">9.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="line-height">12.5pt</xsl:attribute>
		 -->
	</xsl:template>
	<xsl:template name="font_normal">
	<!--
		<xsl:attribute name="font-size">11.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="line-height">18.5pt</xsl:attribute>
		 -->
	</xsl:template>
	<xsl:template name="font_big">
	<!--
		<xsl:attribute name="font-size">12.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="line-height">18.5pt</xsl:attribute>
		 -->
	</xsl:template>	
	<xsl:template name="font_subsubhead">
	<!--
		<xsl:attribute name="font-size">11.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="line-height">18.5pt</xsl:attribute>
		 -->
	</xsl:template>
	<xsl:template name="font_subhead">
	<!--
		<xsl:attribute name="font-size">13.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="line-height">15.5.pt</xsl:attribute>
		<xsl:attribute name="margin-top">11pt</xsl:attribute>
		 -->
	</xsl:template>
	<xsl:template name="font_head">
	<!--
		<xsl:attribute name="font-size">14.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="line-height">16.5pt</xsl:attribute>
		<xsl:attribute name="margin-top">7pt</xsl:attribute>
		<xsl:attribute name="margin-bottom">7pt</xsl:attribute>
		 -->
	</xsl:template>

	<!-- Gibt den Namen der letzten Page-Sequence im Dokument zurück -->
	<xsl:template name="last-page-sequence-name">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>
		<xsl:param name="media_exists"><xsl:call-template name="media_exists"></xsl:call-template></xsl:param>
		<xsl:param name="task_exists"><xsl:call-template name="task_exists"></xsl:call-template></xsl:param>			
		<xsl:param name="index_exists"><xsl:call-template name="index_exists"></xsl:call-template></xsl:param>		
		
		
		<xsl:choose>
			<xsl:when test="($index_exists='1')">
				<xsl:text>index_sequence</xsl:text>
			</xsl:when>
			<xsl:when test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1') or ($media_exists='1') or ($task_exists='1')">
				<xsl:text>appendix_sequence</xsl:text>
			</xsl:when>			
			<xsl:otherwise>
				<xsl:text>contentpages_sequence</xsl:text>		
			</xsl:otherwise>
		</xsl:choose>	
	</xsl:template>
	
	
	<xsl:template name="cite_exists">
		<xsl:choose>
			<xsl:when test="//*/cite">
				<xsl:text>1</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	
	
	<xsl:template name="figure_exists">
		<xsl:choose>
			<xsl:when test="//*/extension[@extension_name='loop_figure']">
				<xsl:text>1</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>			
	
	<xsl:template name="table_exists">
		<xsl:choose>
			<xsl:when test="//*/extension[@extension_name='loop_table']">
				<xsl:text>1</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="media_exists">
		<xsl:choose>
			<xsl:when test="//*/extension[@extension_name='loop_media']">
				<xsl:text>1</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	
	
	<xsl:template name="task_exists">
		<xsl:choose>
			<xsl:when test="//*/extension[@extension_name='loop_task']">
				<xsl:text>1</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>		

	<xsl:template name="index_exists">
		<xsl:choose>
			<xsl:when test="//*/paragraph[starts-with(.,'#index')]">
				<xsl:text>1</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>0</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>			

	<xsl:template name="appendix_number">
		<xsl:param name="content"></xsl:param>
		
		
		<xsl:variable name="c_bibliography" ><xsl:call-template name="cite_exists"></xsl:call-template></xsl:variable>	
		<xsl:variable name="c_figures" ><xsl:call-template name="figure_exists"></xsl:call-template></xsl:variable>
		<xsl:variable name="c_tables" ><xsl:call-template name="table_exists"></xsl:call-template></xsl:variable>
		<xsl:variable name="c_media" ><xsl:call-template name="media_exists"></xsl:call-template></xsl:variable>
		<xsl:variable name="c_tasks" ><xsl:call-template name="task_exists"></xsl:call-template></xsl:variable>
		<xsl:variable name="c_index" ><xsl:call-template name="index_exists"></xsl:call-template></xsl:variable>

		<xsl:variable name="temp_nr">
			<xsl:choose>
				<xsl:when test="$content='bibliography'">
					<xsl:value-of select="$c_bibliography"></xsl:value-of>
				</xsl:when>
				<xsl:when test="$content='list_of_figures'">
					<xsl:value-of select="$c_bibliography + $c_figures"></xsl:value-of>
				</xsl:when>				
				<xsl:when test="$content='list_of_tables'">
					<xsl:value-of select="$c_bibliography + $c_figures + $c_tables"></xsl:value-of>
				</xsl:when>
				<xsl:when test="$content='list_of_media'">
					<xsl:value-of select="$c_bibliography + $c_figures + $c_tables + $c_media"></xsl:value-of>
				</xsl:when>				
				<xsl:when test="$content='list_of_tasks'">
					<xsl:value-of select="$c_bibliography + $c_figures + $c_tables + $c_media + $c_tasks"></xsl:value-of>
				</xsl:when>
				<xsl:when test="$content='index'">
					<xsl:value-of select="$c_bibliography + $c_figures + $c_tables + $c_media + $c_tasks + $c_index"></xsl:value-of>
				</xsl:when>												
																
			</xsl:choose>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$temp_nr='1'"><xsl:text>I</xsl:text></xsl:when>
			<xsl:when test="$temp_nr='2'"><xsl:text>II</xsl:text></xsl:when>
			<xsl:when test="$temp_nr='3'"><xsl:text>III</xsl:text></xsl:when>
			<xsl:when test="$temp_nr='4'"><xsl:text>IV</xsl:text></xsl:when>
			<xsl:when test="$temp_nr='5'"><xsl:text>V</xsl:text></xsl:when>
			<xsl:when test="$temp_nr='6'"><xsl:text>VI</xsl:text></xsl:when>
		</xsl:choose>

	</xsl:template>
	
	
	
	<xsl:template name="make-bookmark-tree">
		<xsl:param name="cite_exists"><xsl:call-template name="cite_exists"></xsl:call-template></xsl:param>
		<xsl:param name="figure_exists"><xsl:call-template name="figure_exists"></xsl:call-template></xsl:param>
		<xsl:param name="table_exists"><xsl:call-template name="table_exists"></xsl:call-template></xsl:param>
		<xsl:param name="media_exists"><xsl:call-template name="media_exists"></xsl:call-template></xsl:param>
		<xsl:param name="task_exists"><xsl:call-template name="task_exists"></xsl:call-template></xsl:param>		
		<xsl:param name="index_exists"><xsl:call-template name="index_exists"></xsl:call-template></xsl:param>
		<fo:bookmark-tree>
			<fo:bookmark internal-destination="cover">
				<fo:bookmark-title><xsl:value-of select="normalize-space(//article[1]/@title)"></xsl:value-of></fo:bookmark-title>
			</fo:bookmark>
			<fo:bookmark internal-destination="table_of_content">
				<fo:bookmark-title><xsl:value-of select="$word_toc"></xsl:value-of></fo:bookmark-title>
			</fo:bookmark>			
			<xsl:apply-templates select="toc"  mode="bookmark"></xsl:apply-templates>
			<xsl:if test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1') or ($media_exists='1') or ($task_exists='1')">
				<fo:bookmark internal-destination="appendix">
					<fo:bookmark-title><xsl:value-of select="$word_appendix"></xsl:value-of></fo:bookmark-title>
					
					<xsl:if test="$cite_exists='1'">
						<fo:bookmark internal-destination="bibliography">
							<fo:bookmark-title>
								<xsl:call-template name="appendix_number">
									<xsl:with-param name="content" select="'bibliography'"></xsl:with-param>
								</xsl:call-template>
								<xsl:text> </xsl:text>							
								<xsl:value-of select="$word_bibliography"></xsl:value-of>
							</fo:bookmark-title>
						</fo:bookmark>
					</xsl:if>
					<xsl:if test="$figure_exists='1'">
						<fo:bookmark internal-destination="list_of_figures">
							<fo:bookmark-title>
								<xsl:call-template name="appendix_number">
									<xsl:with-param name="content" select="'list_of_figures'"></xsl:with-param>
								</xsl:call-template>
								<xsl:text> </xsl:text>							
								<xsl:value-of select="$word_list_of_figures"></xsl:value-of>
							</fo:bookmark-title>
						</fo:bookmark>
					</xsl:if>
					<xsl:if test="$table_exists='1'">
						<fo:bookmark internal-destination="list_of_tables">
							<fo:bookmark-title>
								<xsl:call-template name="appendix_number">
									<xsl:with-param name="content" select="'list_of_tables'"></xsl:with-param>
								</xsl:call-template>
								<xsl:text> </xsl:text>							
								<xsl:value-of select="$word_list_of_tables"></xsl:value-of>
							</fo:bookmark-title>
						</fo:bookmark>
					</xsl:if>		
					<xsl:if test="$media_exists='1'">
						<fo:bookmark internal-destination="list_of_media">
							<fo:bookmark-title>
								<xsl:call-template name="appendix_number">
									<xsl:with-param name="content" select="'list_of_media'"></xsl:with-param>
								</xsl:call-template>
								<xsl:text> </xsl:text>							
								<xsl:value-of select="$word_list_of_media"></xsl:value-of>
							</fo:bookmark-title>
						</fo:bookmark>
					</xsl:if>											
					<xsl:if test="$task_exists='1'">
						<fo:bookmark internal-destination="list_of_tasks">
							<fo:bookmark-title>
								<xsl:call-template name="appendix_number">
									<xsl:with-param name="content" select="'list_of_tasks'"></xsl:with-param>
								</xsl:call-template>
								<xsl:text> </xsl:text>							
								<xsl:value-of select="$word_list_of_tasks"></xsl:value-of>
							</fo:bookmark-title>
						</fo:bookmark>
					</xsl:if>									
					<xsl:if test="$index_exists='1'">
						<fo:bookmark internal-destination="index">
							<fo:bookmark-title>
								<xsl:call-template name="appendix_number">
									<xsl:with-param name="content" select="'index'"></xsl:with-param>
								</xsl:call-template>
								<xsl:text> </xsl:text>							
								<xsl:value-of select="$word_index"></xsl:value-of>
							</fo:bookmark-title>
						</fo:bookmark>
					</xsl:if>														
				</fo:bookmark>			
			</xsl:if>
		</fo:bookmark-tree>			
	</xsl:template>
	
	<xsl:template match="toc" mode="bookmark">
		<xsl:apply-templates select="chapter"  mode="bookmark"></xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="chapter" mode="bookmark">
		<xsl:apply-templates select="page"  mode="bookmark"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="page" mode="bookmark">
		<fo:bookmark>
			<xsl:if test="@toclevel &gt; 0"> 
				<xsl:attribute name="starting-state"><xsl:text>hide</xsl:text></xsl:attribute>
			</xsl:if>
			<xsl:attribute name="internal-destination"><xsl:value-of select="@title"></xsl:value-of></xsl:attribute>
			<fo:bookmark-title>
				<xsl:value-of select="@tocnumber"></xsl:value-of>
				<xsl:text> </xsl:text>
				<xsl:value-of select="@title"></xsl:value-of>
			</fo:bookmark-title>
			<xsl:apply-templates select="chapter"  mode="bookmark"></xsl:apply-templates>
		</fo:bookmark>
	</xsl:template>	
	
	
	<xsl:template name="make-toc">
		<xsl:apply-templates select="toc"  mode="toc"></xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="toc" mode="toc">
		<xsl:apply-templates select="chapter"  mode="toc"></xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="chapter" mode="toc">
		<xsl:apply-templates select="page"  mode="toc"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="page" mode="toc">
		<fo:block text-align-last="justify">
			<xsl:call-template name="font_normal"></xsl:call-template>	
			<xsl:if test="@toclevel &gt; 0">
				<xsl:attribute name="margin-left">
					<xsl:value-of select="@toclevel - 1"></xsl:value-of><xsl:text>em</xsl:text>
				</xsl:attribute>
			</xsl:if>
			

			<fo:basic-link color="black">
				<xsl:attribute name="internal-destination" ><xsl:value-of select="@title"></xsl:value-of></xsl:attribute>
				<xsl:value-of select="@tocnumber"></xsl:value-of>
				<xsl:text> </xsl:text>
				<xsl:value-of select="@title"></xsl:value-of>
			</fo:basic-link>
			<fo:inline keep-together.within-line="always">
				<fo:leader leader-pattern="dots"></fo:leader>
				<fo:page-number-citation>
					<xsl:attribute name="ref-id" ><xsl:value-of select="@title"></xsl:value-of></xsl:attribute>
				</fo:page-number-citation>
			</fo:inline>				
		</fo:block>
		<xsl:apply-templates select="chapter"  mode="toc"></xsl:apply-templates>	
		
		
		
	</xsl:template>		
	
	<xsl:template name="make-declarations">
			<fo:declarations>
			  <x:xmpmeta xmlns:x="adobe:ns:meta/">
				<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
				  <rdf:Description rdf:about="" xmlns:dc="http://purl.org/dc/elements/1.1/">
					<!-- Dublin Core properties go here -->
					<dc:title><xsl:value-of select="/articles/@title"></xsl:value-of></dc:title>
					<dc:creator><xsl:value-of select="/articles/@url"></xsl:value-of></dc:creator>
					<dc:description><xsl:value-of select="/articles/@title"></xsl:value-of></dc:description>
				  </rdf:Description>
				  <rdf:Description rdf:about="" xmlns:xmp="http://ns.adobe.com/xap/1.0/">
					<xmp:CreatorTool>LOOP</xmp:CreatorTool>
				  </rdf:Description>
				</rdf:RDF>
			  </x:xmpmeta>
			</fo:declarations>	
	</xsl:template>



	 
	<xsl:template match="link">
		<xsl:apply-templates select="php:function('xslt_transform_link', .)"></xsl:apply-templates>
	</xsl:template> 
	
	<xsl:template match="php_link">
		<xsl:value-of select="."></xsl:value-of>
	</xsl:template>
	
	<xsl:template match="php_link_external">
		<a>
		<xsl:attribute name="href"><xsl:value-of select="@href"></xsl:value-of></xsl:attribute>
		<xsl:value-of select="."></xsl:value-of>
		</a>
	</xsl:template>	

	<xsl:template match="php_link_internal">
		<a>
		<xsl:attribute name="href"><xsl:value-of select="@href"></xsl:value-of></xsl:attribute>
		<xsl:value-of select="."></xsl:value-of>
		</a>
	</xsl:template>		
	
	<xsl:template match="php_link_image">
	<!-- 
		<fo:block>
			<fo:external-graphic scaling="uniform" content-height="scale-to-fit">
				<xsl:attribute name="src" ><xsl:value-of select="@imagepath"></xsl:value-of></xsl:attribute>
				<xsl:attribute name="content-width" ><xsl:value-of select="@imagewidth"></xsl:value-of></xsl:attribute>
			</fo:external-graphic>
		</fo:block>
 	-->
 			
	</xsl:template>
	
	<xsl:template match="cite">
		<xsl:text> [</xsl:text>
		<xsl:value-of select="."></xsl:value-of>
		<xsl:text>] </xsl:text>
		<!-- 
		<xsl:variable name="citetext">
			<xsl:value-of select="."></xsl:value-of>
		</xsl:variable>
		<fo:basic-link >
			<xsl:attribute name="internal-destination">bibliography</xsl:attribute>
			<fo:inline text-decoration="underline" font-style="italic"><xsl:value-of select="translate($citetext,'+',' ')"></xsl:value-of></fo:inline>
			<xsl:text> </xsl:text>		
			<fo:inline><fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="3mm" src="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/skins/loop/images/print/literature.png"></fo:external-graphic></fo:inline>
		</fo:basic-link>
		 -->
	</xsl:template>	
	
	<xsl:template match="ol">
		<ol><xsl:text>
		</xsl:text><xsl:apply-templates></xsl:apply-templates><xsl:text>
		</xsl:text></ol>
	</xsl:template>
	
	<xsl:template match="li">
		<fo:list-item>
			<fo:list-item-label end-indent="label-end()">
				<fo:block padding-left="-20mm">
					<xsl:apply-templates select="preblock" mode="biblio_label"></xsl:apply-templates>
				</fo:block>
			</fo:list-item-label>
			<fo:list-item-body start-indent="body-start()">
				<fo:block>
					<xsl:apply-templates select="." mode="biblio_entry"></xsl:apply-templates>
				</fo:block>
			</fo:list-item-body>
		</fo:list-item>
	</xsl:template>	

	<xsl:template match="preblock" mode="biblio_label">
		<xsl:apply-templates mode="biblio_label"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="preblock" mode="biblio_entry">
		<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="preline" mode="biblio_label">
		<xsl:value-of select="span"></xsl:value-of>
	</xsl:template>
	<xsl:template match="preline" mode="biblio_entry">
		<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
	</xsl:template>
	
	
	<xsl:template match="span" mode="biblio_entry">
	</xsl:template>
	
	<xsl:template match="i" mode="biblio_entry">
		<em><xsl:apply-templates></xsl:apply-templates></em>
	</xsl:template>	
	
	<xsl:template match="extension" mode="biblio_entry">
		<xsl:if test="@href">
					<fo:basic-link>
			<xsl:attribute name="external-destination"><xsl:value-of select="@href"></xsl:value-of></xsl:attribute>
			<fo:inline text-decoration="underline"><xsl:value-of select="."></xsl:value-of></fo:inline>
			<xsl:text> </xsl:text>
			<fo:inline ><fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="2mm" src="/opt/www/devloop.oncampus.de/mediawiki-1.18.1/skins/loop/images/print/www_link.png"></fo:external-graphic></fo:inline>
			 
			<xsl:text> (</xsl:text>
			<xsl:value-of select="@href"></xsl:value-of>
			<xsl:text>)</xsl:text>
			 
		</fo:basic-link>	
		</xsl:if>
	</xsl:template>	
	<xsl:template match="space" mode="biblio_entry">
		<xsl:text> </xsl:text>
	</xsl:template>	

	<xsl:template match="preblock" >
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="preline" >
    <tt><xsl:apply-templates></xsl:apply-templates></tt>
	</xsl:template>
	
</xsl:stylesheet>