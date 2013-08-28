<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:func="http://exslt.org/functions"
                extension-element-prefixes="func" xmlns:functx="http://www.functx.com">

    <xsl:param name="terms_file">
        <xsl:value-of select="'loop_terms.xml'"/>
    </xsl:param>

 
	<func:function name="functx:get_term_name">
		<xsl:param name="term_name_key"/>
		<func:result select="document($terms_file)/terms/msg[(@name=$term_name_key) and (@lang=$lang)]"/>
	</func:function>

	<xsl:variable name="word_content"  select="functx:get_term_name('word_content')" />
	<xsl:variable name="word_state"  select="functx:get_term_name('word_state')" />
	<xsl:variable name="word_toc"  select="functx:get_term_name('word_toc')" />
	<xsl:variable name="word_appendix"  select="functx:get_term_name('word_appendix')" />
	<xsl:variable name="word_bibliography"  select="functx:get_term_name('word_bibliography')" />
	<xsl:variable name="word_list_of_figures"  select="functx:get_term_name('word_list_of_figures')" />
	<xsl:variable name="word_list_of_tables"  select="functx:get_term_name('word_list_of_tables')" />
	<xsl:variable name="word_list_of_media"  select="functx:get_term_name('word_list_of_media')" />
	<xsl:variable name="word_list_of_tasks"  select="functx:get_term_name('word_list_of_tasks')" />	
	<xsl:variable name="word_index"  select="functx:get_term_name('word_index')" />
	
	<xsl:variable name="word_looparea_task"  select="functx:get_term_name('word_looparea_task')" />
	<xsl:variable name="word_looparea_timerequirement"  select="functx:get_term_name('word_looparea_timerequirement')" />
	<xsl:variable name="word_looparea_learningobjectives"  select="functx:get_term_name('word_looparea_learningobjectives')" />
	<xsl:variable name="word_looparea_arrangement"  select="functx:get_term_name('word_looparea_arrangement')" />
	<xsl:variable name="word_looparea_example"  select="functx:get_term_name('word_looparea_example')" />
	<xsl:variable name="word_looparea_reflection"  select="functx:get_term_name('word_looparea_reflection')" />
	<xsl:variable name="word_looparea_notice"  select="functx:get_term_name('word_looparea_notice')" />
	<xsl:variable name="word_looparea_important"  select="functx:get_term_name('word_looparea_important')" />
	<xsl:variable name="word_looparea_annotation"  select="functx:get_term_name('word_looparea_annotation')" />
	<xsl:variable name="word_looparea_definition"  select="functx:get_term_name('word_looparea_definition')" />
	<xsl:variable name="word_looparea_formula"  select="functx:get_term_name('word_looparea_formula')" />
	<xsl:variable name="word_looparea_markedsentence"  select="functx:get_term_name('word_looparea_markedsentence')" />
	<xsl:variable name="word_looparea_sourcecode"  select="functx:get_term_name('word_looparea_sourcecode')" />
	<xsl:variable name="word_looparea_summary"  select="functx:get_term_name('word_looparea_summary')" />
	<xsl:variable name="word_looparea_indentation"  select="functx:get_term_name('word_looparea_indentation')" />
	<xsl:variable name="word_looparea_norm"  select="functx:get_term_name('word_looparea_norm')" />
	<xsl:variable name="word_loopmedia_notice"  select="functx:get_term_name('word_loopmedia_notice')" />
	
	<xsl:variable name="word_quiz_notice"  select="functx:get_term_name('word_quiz_notice')" />
	
	<xsl:variable name="word_chapter"  select="functx:get_term_name('word_chapter')" />
	<xsl:variable name="word_loopfigure"  select="functx:get_term_name('word_loopfigure')" />
	<xsl:variable name="word_looptable"  select="functx:get_term_name('word_looptable')" />
	
	<xsl:variable name="word_embed_video_notice"  select="functx:get_term_name('word_embed_video_notice')" />

	<xsl:variable name="word_printversion_begin"  select="functx:get_term_name('word_printversion_begin')" />
	<xsl:variable name="word_printversion_end"  select="functx:get_term_name('word_printversion_end')" />

	<xsl:variable name="word_spoiler_defaulttitle"  select="functx:get_term_name('word_spoiler_defaulttitle')" />

	
</xsl:stylesheet>