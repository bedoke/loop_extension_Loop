<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:fn="http://www.w3.org/2004/07/xpath-functions"
	xmlns:xdt="http://www.w3.org/2004/07/xpath-datatypes" xmlns:fox="http://xml.apache.org/fop/extensions"
	xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:func="http://exslt.org/functions"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="func php" xmlns:functx="http://www.functx.com" exclude-result-prefixes="xhtml">

	<!-- <xsl:namespace-alias stylesheet-prefix="php" result-prefix="xsl" /> -->

	<xsl:import href="loop_params.xsl"></xsl:import>
	<xsl:import href="loop_terms.xsl"></xsl:import>

	<xsl:preserve-space elements="extension source" />

	<xsl:output method="xml" version="1.0" encoding="UTF-8"
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
		<fo:root>
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
			<xsl:call-template name="page-sequence-contentpages"></xsl:call-template>
			
			<xsl:if test="($cite_exists='1') or ($figure_exists='1') or ($table_exists='1') or ($media_exists='1') or ($task_exists='1') or ($index_exists='1')">
				<xsl:call-template name="page-sequence-appendix"></xsl:call-template>
			</xsl:if>

		</fo:root>
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
			<xsl:apply-templates select="php:function('get_biblio', '')" mode="biblio"></xsl:apply-templates>
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
			<!--
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
			-->

			<xsl:variable name="linktext">
				<xsl:choose>
					<xsl:when test="extension[@extension_name='loop_figure_title']">
						<xsl:apply-templates select="extension[@extension_name='loop_figure_title']" mode="infigure"></xsl:apply-templates>
					</xsl:when>
					<xsl:when test="@title">
						<xsl:value-of select="@title"></xsl:value-of>
					</xsl:when>
					<xsl:when test="extension[@extension_name='loop_figure_description']">
						<xsl:apply-templates select="extension[@extension_name='loop_figure_description']" mode="infigure"></xsl:apply-templates>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="@description">
							<xsl:value-of select="@description"></xsl:value-of>
						</xsl:if>
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
						<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_table.png"></fo:external-graphic>
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
						<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_video.png"></fo:external-graphic>
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
						<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_task.png"></fo:external-graphic>
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
			<!--  <fo:external-graphic scaling="uniform" content-height="50mm" content-width="100mm" src="/opt/www/loop.oncampus.de/mediawiki/extensions/Loop/tmp/marknew.png"></fo:external-graphic> -->
			
			
			
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
		<fo:page-sequence master-reference="default-page"
			id="contentpages_sequence">
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
				<xsl:call-template name="page-content-contentpages"></xsl:call-template>
			</fo:flow>
		</fo:page-sequence>
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
		<fo:block id="{@title}">
			<xsl:choose>
				<xsl:when test="$toclevel &lt; 2"> 
					<xsl:attribute name="break-before">page</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<fo:block margin-top="10mm">
					</fo:block>		
				</xsl:otherwise>
			</xsl:choose>
			<fo:block>
				<fo:marker marker-class-name="page-title-left">
				<xsl:choose>
					<xsl:when test="@toclevel=0">
						<xsl:value-of select="//articles/@title"></xsl:value-of>
					</xsl:when>
					<xsl:when test="@toclevel=1">
						<xsl:value-of select="//articles/@title"></xsl:value-of>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="preceding-sibling::node()[@toclevel &lt; $toclevel][1]/@tocnumber"></xsl:value-of>
						<xsl:text> </xsl:text>
						<xsl:value-of select="preceding-sibling::node()[@toclevel &lt; $toclevel][1]/@title"></xsl:value-of>
					</xsl:otherwise>					
				</xsl:choose>
				</fo:marker>
			</fo:block>
			<fo:block>
				<fo:marker marker-class-name="page-title-right">
					<xsl:value-of select="@tocnumber"></xsl:value-of>
					<xsl:text> </xsl:text>
					<xsl:value-of select="@title"></xsl:value-of>
				</fo:marker>
			</fo:block>
			<fo:block keep-with-next.within-page="always">
				<xsl:call-template name="font_head"></xsl:call-template>
				<xsl:value-of select="@tocnumber"></xsl:value-of>
				<xsl:text> </xsl:text>
				<xsl:value-of select="@title"></xsl:value-of>
			</fo:block>
			<fo:block keep-with-previous.within-page="always">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<xsl:apply-templates></xsl:apply-templates>
			</fo:block>
		</fo:block>

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
				<fo:block keep-with-next.within-page="always">
					<xsl:choose>
						<xsl:when test="$level='1'">
							<xsl:call-template name="font_head"></xsl:call-template>
						</xsl:when>
						<xsl:when test="$level='2'">
							<xsl:call-template name="font_subhead"></xsl:call-template>
						</xsl:when>
						<xsl:when test="$level='3'">
							<xsl:call-template name="font_subsubhead"></xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="font_subsubhead"></xsl:call-template>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="."></xsl:value-of>
				</fo:block>
			</xsl:otherwise>
		</xsl:choose>
		
	</xsl:template>	
	
	<xsl:template match="paragraph">
		<xsl:if test="not(starts-with(.,'#index:'))">
			<fo:block margin-top="7pt">
				<xsl:call-template name="font_normal"></xsl:call-template>
				<xsl:apply-templates></xsl:apply-templates>
			</fo:block>
		</xsl:if>
	</xsl:template>
	
	
	<xsl:template match="br">
		<fo:block></fo:block>
	</xsl:template>

	<xsl:template match="xhtml:br">
		<fo:block></fo:block>
	</xsl:template>		

	<xsl:template match="sub">
		<fo:inline vertical-align="sub" font-size="0.8em"><xsl:apply-templates></xsl:apply-templates></fo:inline>
	</xsl:template>	
	
	<xsl:template match="sup">
		<fo:inline vertical-align="super" font-size="0.8em"><xsl:apply-templates></xsl:apply-templates></fo:inline>
	</xsl:template>	

	<xsl:template match="xhtml:sub">
		<fo:inline vertical-align="sub" font-size="0.8em"><xsl:apply-templates></xsl:apply-templates></fo:inline>
	</xsl:template>	
	
	<xsl:template match="xhtml:sup">
		<fo:inline vertical-align="super" font-size="0.8em"><xsl:apply-templates></xsl:apply-templates></fo:inline>
	</xsl:template>	
	
	<xsl:template match="big">
		<fo:inline>
			<xsl:call-template name="font_big"></xsl:call-template>
			<xsl:apply-templates></xsl:apply-templates>
		</fo:inline>
	</xsl:template>	
	
	<xsl:template match="small">
		<fo:inline>
			<xsl:call-template name="font_small"></xsl:call-template>
			<xsl:apply-templates></xsl:apply-templates>
		</fo:inline>
	</xsl:template>			

	<xsl:template match="bold">
		<fo:inline font-weight="bold">
			<xsl:apply-templates></xsl:apply-templates>
		</fo:inline>
	</xsl:template>

	<xsl:template match="italics">
		<fo:inline font-style="italic">
			<xsl:apply-templates></xsl:apply-templates>
		</fo:inline>
	</xsl:template>


	<xsl:template match="space">
		<xsl:text> </xsl:text>
	</xsl:template>	
	
	
	<xsl:template match="extension" mode="infigure">
		<xsl:choose>
			<xsl:when test="@extension_name='loop_figure_title'">
				<fo:block><xsl:apply-templates></xsl:apply-templates></fo:block>
			</xsl:when>
			<xsl:when test="@extension_name='loop_figure_description'">
				<fo:block><xsl:apply-templates></xsl:apply-templates></fo:block>
			</xsl:when>	
		</xsl:choose>
	</xsl:template>

	
	<xsl:template match="extension">
		<fo:inline>
			<xsl:attribute name="id"><xsl:value-of select="generate-id()"></xsl:value-of></xsl:attribute>
		</fo:inline>
		<xsl:choose>
			<xsl:when test="@extension_name='loop_area'">
				<fo:block margin-left="-16mm">
					<xsl:attribute name="margin-top">4mm</xsl:attribute>
					<xsl:attribute name="margin-bottom">4mm</xsl:attribute>
					<!-- 
					<xsl:if test="name(following::node()[2])='extension'">
						<xsl:attribute name="margin-bottom">4mm</xsl:attribute>
					</xsl:if>
					 -->
					<fo:table width="170mm" table-layout="fixed" border-collapse="separate">
						<fo:table-header>
							<fo:table-row>
								<fo:table-cell width="12mm">
									<fo:block></fo:block>
								</fo:table-cell>
								<fo:table-cell width="158mm" border-start-width="2mm" border-start-style="solid" border-start-color="#cecece" border-end-width="2mm" border-end-style="solid" border-end-color="#cecece" border-before-width="0.3mm" border-before-style="solid" border-before-color="#cecece" height="0.3mm">
									<fo:block></fo:block>
								</fo:table-cell>
							</fo:table-row>				
						</fo:table-header>
						<fo:table-footer>
							<fo:table-row>
								<fo:table-cell width="12mm">
									<fo:block></fo:block>
								</fo:table-cell>
								<fo:table-cell width="158mm" border-start-width="2mm" border-start-style="solid" border-start-color="#cecece" border-end-width="2mm" border-end-style="solid" border-end-color="#cecece" border-after-width="0.3mm" border-after-style="solid" border-after-color="#cecece" height="0.3mm">
									<fo:block></fo:block>
								</fo:table-cell>
							</fo:table-row>				
						</fo:table-footer>			
						<fo:table-body>
							<fo:table-row>
								<fo:table-cell width="12mm" text-align="center">
									<fo:block>
										<fo:external-graphic scaling="uniform" content-width="12mm" content-height="scale-to-fit"> 
										<xsl:attribute name="src">/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/area/<xsl:value-of select="@type"></xsl:value-of>.png</xsl:attribute>
										</fo:external-graphic>
									</fo:block>
									<fo:block keep-with-previous.within-page="always" keep-together="auto">
										<xsl:call-template name="font_icon"></xsl:call-template>
										<!-- <xsl:value-of select="@type"></xsl:value-of> -->
										<xsl:call-template name="looparea_name"><xsl:with-param name="areaname" select="@type"></xsl:with-param></xsl:call-template>
									</fo:block>									
								</fo:table-cell>
								<fo:table-cell width="158mm" border-start-width="2mm" border-start-style="solid" border-start-color="#cecece" border-end-width="2mm" border-end-style="solid" border-end-color="#cecece" padding-start="2mm" padding-end="2mm" padding-before="0mm" padding-after="0mm">
									<fo:block margin-left="16mm" >
										<xsl:call-template name="font_normal"></xsl:call-template>
										<xsl:apply-templates></xsl:apply-templates>
									</fo:block>
								</fo:table-cell>					
							</fo:table-row>								
						</fo:table-body>
					</fo:table>

				</fo:block>
			</xsl:when>
			<xsl:when test="@extension_name='loop_toc'">
				<xsl:variable name="toctitle" select="ancestor::article[1]/@title"></xsl:variable>
				<xsl:apply-templates select="/articles/toc/descendant-or-self::page[@title=$toctitle]/."></xsl:apply-templates>
				<xsl:apply-templates select="/articles/toc/descendant-or-self::page[@title=$toctitle]/*"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="@extension_name='loop_figure_title'">
				<!-- <fo:block><xsl:apply-templates></xsl:apply-templates></fo:block> -->
			</xsl:when>
			<xsl:when test="@extension_name='loop_figure_description'">
				<!-- <fo:block><xsl:apply-templates></xsl:apply-templates></fo:block> -->
			</xsl:when>	
			<xsl:when test="@extension_name='spoiler'">
				<xsl:choose>
					<xsl:when test="@text">
						<fo:block font-weight="bold">
							<xsl:value-of select="@text"></xsl:value-of>
						</fo:block>
					</xsl:when>
					<xsl:otherwise>
						<fo:block font-weight="bold">
							<xsl:value-of select="$word_spoiler_defaulttitle"></xsl:value-of>
						</fo:block>
					</xsl:otherwise>
				</xsl:choose>
				<fo:block><xsl:apply-templates></xsl:apply-templates></fo:block>
			</xsl:when>								
			<xsl:when test="@extension_name='loop_figure'">
				<xsl:apply-templates></xsl:apply-templates>
					<xsl:variable name="figurewidth"><xsl:value-of select="php:function('xslt_figure_width', descendant::link[1])"></xsl:value-of></xsl:variable>			
				<fo:table table-layout="fixed" border-collapse="separate">
					<xsl:attribute name="width"><xsl:value-of select="$figurewidth"></xsl:value-of>mm</xsl:attribute>
					<fo:table-body>
						<!-- 
						<fo:table-row>
							<fo:table-cell number-columns-spanned="2">
								<xsl:apply-templates></xsl:apply-templates>
							</fo:table-cell>
						</fo:table-row>
						 -->
						<fo:table-row>
							<fo:table-cell  number-columns-spanned="1" width="10mm">
								<fo:block>
									<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_image.png"></fo:external-graphic>
								</fo:block>
							</fo:table-cell>
							<fo:table-cell  number-columns-spanned="1">
								<xsl:attribute name="width">
									<xsl:value-of select="$figurewidth - 10"></xsl:value-of>
									<xsl:text>mm</xsl:text>
								</xsl:attribute>
								<fo:block>
									<xsl:choose>
										<xsl:when test="extension[@extension_name='loop_figure_title']">
											<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt">
												<xsl:apply-templates select="extension[@extension_name='loop_figure_title']" mode="infigure"></xsl:apply-templates>
											</fo:block>
										</xsl:when>
										<xsl:otherwise>
											<xsl:if test="@title">
												<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt"><xsl:value-of select="@title"></xsl:value-of></fo:block>
											</xsl:if>
										</xsl:otherwise>
									</xsl:choose>	
									<xsl:choose>
										<xsl:when test="extension[@extension_name='loop_figure_description']">
											<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt">
												<xsl:apply-templates select="extension[@extension_name='loop_figure_description']" mode="infigure"></xsl:apply-templates>
											</fo:block>
										</xsl:when>
										<xsl:otherwise>
											<xsl:if test="@description">
												<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt"><xsl:value-of select="@description"></xsl:value-of></fo:block>
											</xsl:if>
										</xsl:otherwise>
									</xsl:choose>
								</fo:block>
							</fo:table-cell>
						</fo:table-row>
					</fo:table-body>
				</fo:table>				
			</xsl:when>
			<xsl:when test="@extension_name='loop_task'">
				<xsl:if test="@title">
					<fo:block font-weight="bold"><xsl:value-of select="@title"></xsl:value-of></fo:block>
				</xsl:if>
				<xsl:apply-templates></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="@extension_name='loop_table'">
				<xsl:apply-templates></xsl:apply-templates>
				<fo:table table-layout="fixed" border-collapse="separate" width="150mm">
					<fo:table-body>
						<fo:table-row>
							<fo:table-cell  number-columns-spanned="1" width="10mm">
								<fo:block>
									<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_table.png"></fo:external-graphic>
								</fo:block>
							</fo:table-cell>
							<fo:table-cell  number-columns-spanned="1">
								<xsl:attribute name="width">140mm</xsl:attribute>
								<fo:block>
									<xsl:if test="@title">
										<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt"><xsl:value-of select="@title"></xsl:value-of></fo:block>
									</xsl:if>
									<xsl:if test="@description">
										<fo:block line-height="12.5pt" font-size="9.5pt"><xsl:value-of select="@description"></xsl:value-of></fo:block>
									</xsl:if>
								</fo:block>
							</fo:table-cell>
						</fo:table-row>
					</fo:table-body>
				</fo:table>					
			</xsl:when>	
			<xsl:when test="@extension_name='loop_media'">
				<fo:table table-layout="fixed" border-collapse="separate" width="150mm">
					<fo:table-body>
						<fo:table-row>
							<fo:table-cell  number-columns-spanned="1" width="10mm">
								<fo:block>
									<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_video.png"></fo:external-graphic>
								</fo:block>
							</fo:table-cell>
							<fo:table-cell  number-columns-spanned="1">
								<xsl:attribute name="width">140mm</xsl:attribute>
								<fo:block>
									<fo:block>
										<xsl:value-of select="$word_loopmedia_notice"></xsl:value-of>
									</fo:block>
									<xsl:apply-templates></xsl:apply-templates>								
									<xsl:if test="@title">
										<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt"><xsl:value-of select="@title"></xsl:value-of></fo:block>
									</xsl:if>
									<xsl:if test="@description">
										<fo:block line-height="12.5pt" font-size="9.5pt"><xsl:value-of select="@description"></xsl:value-of></fo:block>
									</xsl:if>
								</fo:block>
							</fo:table-cell>
						</fo:table-row>
					</fo:table-body>
				</fo:table>				
				<!-- <xsl:apply-templates></xsl:apply-templates> -->
			</xsl:when>		
			<xsl:when test="@extension_name='loop_listing'">
				<fo:table table-layout="fixed" border-collapse="separate" width="150mm">
					<fo:table-body>
						<fo:table-row>
							<fo:table-cell  number-columns-spanned="1" width="10mm">
								<fo:block>
									<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="8mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/media/type_listing.png"></fo:external-graphic>
								</fo:block>
							</fo:table-cell>
							<fo:table-cell  number-columns-spanned="1">
								<xsl:attribute name="width">140mm</xsl:attribute>
								<fo:block>
									<xsl:apply-templates></xsl:apply-templates>								
									<xsl:if test="@title">
										<fo:block line-height="12.5pt" font-weight="bold" font-size="9.5pt"><xsl:value-of select="@title"></xsl:value-of></fo:block>
									</xsl:if>
									<xsl:if test="@description">
										<fo:block line-height="12.5pt" font-size="9.5pt"><xsl:value-of select="@description"></xsl:value-of></fo:block>
									</xsl:if>
								</fo:block>
							</fo:table-cell>
						</fo:table-row>
					</fo:table-body>
				</fo:table>				
				<!-- <xsl:apply-templates></xsl:apply-templates> -->
			</xsl:when>		
			<xsl:when test="@extension_name='source'">
				<fo:block font-family="monospace" wrap-option="wrap" linefeed-treatment="preserve" white-space-collapse="false" white-space-treatment="preserve">
					<!-- <xsl:apply-templates></xsl:apply-templates> -->
					<xsl:value-of select="."></xsl:value-of>
				</fo:block>
			</xsl:when>						
			<xsl:when test="@extension_name='math'">
				<fo:inline>
				
				<!-- <fo:external-graphic scaling="uniform" content-height="50mm" content-width="100mm" src="/opt/www/loop.oncampus.de/mediawiki/images/devloop/math/3/3/1/3311e580c1210b14ba019015d8c69429.png"></fo:external-graphic> -->
				<!-- /opt/www/loop.oncampus.de/mediawiki/extensions/Loop/tmp/mathetest.png  -->
				<fo:external-graphic scaling="uniform" content-width="60%">
					<xsl:attribute name="src">
					<xsl:value-of select="php:function('xslt_transform_math', .)"></xsl:value-of>
					</xsl:attribute> 
				 </fo:external-graphic>
				</fo:inline>
			</xsl:when>
			<xsl:when test="@extension_name='loop_print'">
				<fo:block>
					<xsl:value-of select="$word_printversion_begin"></xsl:value-of>
				</fo:block>
				<fo:block>
					<xsl:apply-templates></xsl:apply-templates>
				</fo:block>
				<fo:block>
					<xsl:value-of select="$word_printversion_end"></xsl:value-of>
				</fo:block>				
			</xsl:when>
			<xsl:when test="@extension_name='loop_noprint'">
				<fo:block></fo:block>				
			</xsl:when>						
			<xsl:when test="@extension_name='quiz'">
				<fo:block margin-top="10mm">
					<xsl:value-of select="$word_quiz_notice"></xsl:value-of>
				</fo:block>	
			</xsl:when>				
			<xsl:when test="@extension_name='loop_paragraph'">
				<xsl:choose>
					<xsl:when test="@type='citation'"> 
					
						<fo:table table-layout="fixed" border-collapse="separate" width="150mm">
							<fo:table-body>
								<fo:table-row>
									<fo:table-cell width="10mm">
										<fo:block>
											<fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="6mm" src="/opt/www/loop.oncampus.de/mediawiki-1.18.1/skins/lubeca/pix/print_paragraph_citation.png"></fo:external-graphic>
										</fo:block>
									</fo:table-cell>
									<fo:table-cell width="140mm">
										<fo:block>
											<xsl:apply-templates></xsl:apply-templates>
										</fo:block>								
									</fo:table-cell>
								</fo:table-row>
								
								<xsl:if test="@copyright">
									<fo:table-row>
										<fo:table-cell>
											<fo:block>
												
											</fo:block>
										</fo:table-cell>
										<fo:table-cell>
											<fo:block text-align="right">
												<xsl:value-of select="@copyright"></xsl:value-of>
											</fo:block>								
										</fo:table-cell>
									</fo:table-row>
								</xsl:if>

								
							</fo:table-body>
						</fo:table>						
					
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates></xsl:apply-templates>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="@extension_name='embed_video'">
				<fo:block></fo:block>
				<fo:block>
					<xsl:value-of select="$word_embed_video_notice"></xsl:value-of>
					<xsl:text> : </xsl:text>
					<xsl:choose>
						<xsl:when test="@service='youtube'"> 
							<fo:basic-link>
								<xsl:attribute name="external-destination">http://www.youtube.com/embed/<xsl:value-of select="@videoid"></xsl:value-of></xsl:attribute>
								<fo:inline text-decoration="underline">http://www.youtube.com/embed/<xsl:value-of select="@videoid"></xsl:value-of></fo:inline>
								<xsl:text> </xsl:text>
								<fo:inline ><fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="2mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/print/www_link.png"></fo:external-graphic></fo:inline>
							</fo:basic-link>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="@service"></xsl:value-of>
							<xsl:text> </xsl:text>
							<xsl:value-of select="@videoid"></xsl:value-of>
						</xsl:otherwise>
					</xsl:choose>
				</fo:block>
				<fo:block></fo:block>								
			</xsl:when>			
			<xsl:when test="@extension_name='graphviz'">
				<fo:block>
					<xsl:apply-templates select="php:function('xslt_transform_graphviz', .)"></xsl:apply-templates>
				</fo:block>
			</xsl:when>					
		</xsl:choose>

	</xsl:template>	





<xsl:template match="table">
   <fo:table table-layout="fixed" border-style="solid" border-width="0.5pt" border-color="black" border-collapse="collapse" padding="0.6pt" space-after="12.5pt">
   		<fo:table-body>
    		<xsl:apply-templates></xsl:apply-templates>
    	</fo:table-body>
   </fo:table>
</xsl:template>

<xsl:template match="tablerow">
	<fo:table-row keep-together.within-column="always">
		<xsl:apply-templates></xsl:apply-templates>
	</fo:table-row>
</xsl:template>

    <xsl:template match="tablecell">
        <fo:table-cell padding="3pt" border-style="solid" border-width="0.5pt" border-color="black" border-collapse="collapse">
        	<fo:block>
        		<xsl:apply-templates></xsl:apply-templates>
        	</fo:block>
        </fo:table-cell>
    </xsl:template>

    <xsl:template match="tablehead">
        <fo:table-cell padding="3pt" border-style="solid" border-width="0.5pt" border-color="black" border-collapse="collapse">
        	<fo:block font-weight="bold">
        		<xsl:apply-templates></xsl:apply-templates>
        	</fo:block>
        </fo:table-cell>
    </xsl:template>

	
	<xsl:template match="chapter">
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="page">
		<fo:block text-align="left">
			<xsl:value-of select="@tocnumber"></xsl:value-of>
			<xsl:text> </xsl:text>
			<fo:basic-link text-decoration="underline">
				<xsl:attribute name="internal-destination"><xsl:value-of select="@title"></xsl:value-of></xsl:attribute>
				<xsl:value-of select="@title"></xsl:value-of>
			</fo:basic-link>		
		</fo:block>
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
		<fo:list-block
			start-indent="inherited-property-value(&apos;start-indent&apos;) + 2mm"
			provisional-distance-between-starts="8mm"
			provisional-label-separation="2mm" space-before="4pt" space-after="4pt"
			display-align="before">
			<xsl:apply-templates></xsl:apply-templates>
		</fo:list-block>
	</xsl:template>



	<xsl:template match="listitem">
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
	</xsl:template>
	
	
	<xsl:template name="font_icon">
		<xsl:attribute name="font-size" >8.5pt</xsl:attribute>
		<xsl:attribute name="font-weight" >bold</xsl:attribute>
		<xsl:attribute name="line-height" >12pt</xsl:attribute>
		<xsl:attribute name="margin-bottom" >1mm</xsl:attribute>
	</xsl:template>	

	<xsl:template name="font_small">
		<xsl:attribute name="font-size">9.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="line-height">12.5pt</xsl:attribute>
	</xsl:template>
	<xsl:template name="font_normal">
		<xsl:attribute name="font-size">11.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="line-height">18.5pt</xsl:attribute>
	</xsl:template>
	<xsl:template name="font_big">
		<xsl:attribute name="font-size">12.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">normal</xsl:attribute>
		<xsl:attribute name="line-height">18.5pt</xsl:attribute>
	</xsl:template>	
	<xsl:template name="font_subsubhead">
		<xsl:attribute name="font-size">11.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="line-height">18.5pt</xsl:attribute>
		<xsl:attribute name="margin-top">7pt</xsl:attribute>
	</xsl:template>
	<xsl:template name="font_subhead">
		<xsl:attribute name="font-size">13.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="line-height">15.5.pt</xsl:attribute>
		<xsl:attribute name="margin-top">7pt</xsl:attribute>
	</xsl:template>
	<xsl:template name="font_head">
		<xsl:attribute name="font-size">14.5pt</xsl:attribute>
		<xsl:attribute name="font-weight">bold</xsl:attribute>
		<xsl:attribute name="line-height">16.5pt</xsl:attribute>
		<xsl:attribute name="margin-top">7pt</xsl:attribute>
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
		<fo:basic-link>
			<xsl:attribute name="external-destination"><xsl:value-of select="@href"></xsl:value-of></xsl:attribute>
			<fo:inline text-decoration="underline"><xsl:value-of select="."></xsl:value-of></fo:inline>
			<xsl:text> </xsl:text>
			<fo:inline ><fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="2mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/print/www_link.png"></fo:external-graphic></fo:inline>
		</fo:basic-link>
	</xsl:template>	

	<xsl:template match="php_link_internal">
		<fo:basic-link text-decoration="underline">
			<xsl:attribute name="internal-destination"><xsl:value-of select="@href"></xsl:value-of></xsl:attribute>
			<xsl:value-of select="."></xsl:value-of>
		</fo:basic-link>
	</xsl:template>		
	
	<xsl:template match="php_link_image">
		<fo:block>
			<fo:external-graphic scaling="uniform" content-height="scale-to-fit">
				<xsl:attribute name="src" ><xsl:value-of select="@imagepath"></xsl:value-of></xsl:attribute>
				<xsl:attribute name="content-width" ><xsl:value-of select="@imagewidth"></xsl:value-of></xsl:attribute>
			</fo:external-graphic>
		</fo:block>		
	</xsl:template>
	
	<xsl:template match="cite">
		<xsl:variable name="citetext">
			<xsl:value-of select="."></xsl:value-of>
		</xsl:variable>
		<fo:basic-link >
			<xsl:attribute name="internal-destination">bibliography</xsl:attribute>
			<fo:inline text-decoration="underline" font-style="italic"><xsl:value-of select="translate($citetext,'+',' ')"></xsl:value-of></fo:inline>
			<xsl:text> </xsl:text>		
			<fo:inline><fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="3mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/print/literature.png"></fo:external-graphic></fo:inline>
		</fo:basic-link>
	</xsl:template>	
	
	<xsl:template match="ol">
		<fo:list-block
			start-indent="inherited-property-value(&apos;start-indent&apos;) + 0mm"
			provisional-distance-between-starts="35mm"
			provisional-label-separation="12mm" space-before="4pt" space-after="4pt"
			display-align="before">
			<xsl:apply-templates></xsl:apply-templates>
		</fo:list-block>
	</xsl:template>

	<xsl:template match="ul">
		<fo:list-block
			start-indent="inherited-property-value(&apos;start-indent&apos;) + 2mm"
			provisional-distance-between-starts="8mm"
			provisional-label-separation="2mm" space-before="4pt" space-after="4pt"
			display-align="before">
			<xsl:apply-templates></xsl:apply-templates>
		</fo:list-block>
	</xsl:template>	
	
	
	
	<xsl:template match="extension" mode="biblio">
		<xsl:if test="@href">
					<fo:basic-link>
			<xsl:attribute name="external-destination"><xsl:value-of select="@href"></xsl:value-of></xsl:attribute>
			<fo:inline text-decoration="underline"><xsl:value-of select="."></xsl:value-of></fo:inline>
			<xsl:text> </xsl:text>
			<fo:inline ><fo:external-graphic scaling="uniform" content-height="scale-to-fit" content-width="2mm" src="/opt/www/loop.oncampus.de/mediawiki/skins/loop/images/print/www_link.png"></fo:external-graphic></fo:inline>
			 
			<xsl:text> (</xsl:text>
			<xsl:value-of select="@href"></xsl:value-of>
			<xsl:text>)</xsl:text>
			 
		</fo:basic-link>	
		</xsl:if>
	</xsl:template>	


	<xsl:template match="span" mode="biblio_key">
		<xsl:choose>
			<xsl:when test="@class='bibkey'">
				<fo:inline font-weight="bold"><xsl:apply-templates select="." ></xsl:apply-templates></fo:inline>
			</xsl:when>
			<xsl:otherwise>
				
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="span" mode="biblio">
		<xsl:choose>
			<xsl:when test="@class='bibkey'">
				
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="." ></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="span" mode="biblio_entry">
		<xsl:choose>
			<xsl:when test="@class='bibkey'">
				
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="." ></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	


	<xsl:template match="ul" mode="biblio">
		<fo:list-block provisional-label-separation="4mm" provisional-distance-between-starts="50mm">
			<xsl:apply-templates  mode="biblio"></xsl:apply-templates>
		</fo:list-block>
	</xsl:template>

	<xsl:template match="li" mode="biblio">
		<fo:list-item space-before="4pt">
			<fo:list-item-label end-indent="120mm">
				<fo:block font-weight="bold" wrap-option="wrap">
					<xsl:value-of select="descendant::span[@class='bibkey']"></xsl:value-of>
					<!-- <xsl:apply-templates select="span" mode="biblio_key"></xsl:apply-templates> -->
				</fo:block>
			</fo:list-item-label>
			<fo:list-item-body   start-indent="body-start()">
				<fo:block>
					<xsl:apply-templates select="." mode="biblio_entry"></xsl:apply-templates>
				</fo:block>
			</fo:list-item-body>
		</fo:list-item>
	</xsl:template>






	<xsl:template match="space" mode="biblio_entry">
		<xsl:text> </xsl:text>
	</xsl:template>	


	<xsl:template match="i" mode="biblio_entry">
		<fo:inline font-style="italic">
			<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
		</fo:inline>
	</xsl:template>	

	<xsl:template match="b" mode="biblio_entry">
		<fo:inline font-weight="bold">
			<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
		</fo:inline>
	</xsl:template>	

	<xsl:template match="preblock" mode="biblio_entry">
		<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="preline" mode="biblio_entry">
		<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="div" mode="biblio_entry">
		<xsl:apply-templates mode="biblio_entry"></xsl:apply-templates>
	</xsl:template>	


	<xsl:template match="preblock" >
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="preline" >
    <fo:block font-family="Courier">
      <xsl:apply-templates></xsl:apply-templates>
    </fo:block>
	</xsl:template>
	
</xsl:stylesheet>