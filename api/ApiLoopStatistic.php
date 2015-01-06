<?php
global $IP;

require_once ($IP."/extensions/wiki2xml/mediawiki_converter.php");
require_once ($IP."/extensions/Loop/SpecialLoopPrintversion.php");


class ApiLoopStatistic extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		global $wgServer;
		wfProfileIn( __METHOD__ );

		$result   = $this->getResult();
		
		// Blocked users are, well, blocked.
		$user = $this->getUser();
		if ( $user->isBlocked() ) {
			$this->dieUsage(
				$this->msg( 'loopstatistic-error-blocked' )->escaped(),
				'userblocked'
			);
		}
		$statistic_result =array();
		
		$tmpname=substr($wgServer,7).'_'.time();
		$statistic=$this->get_loop_xml($tmpname);
		
		
		$count_figure = substr_count ( $statistic, "<extension extension_name='loop_figure'");
		#print atxt ($count_figure,'count_figure');
		$statistic_result['loop_marked_figure'] = $count_figure;
		  
		$count_table = substr_count ( $statistic, "<extension extension_name='loop_table'");
		#print atxt ($count_table,'count_table');
		$statistic_result['loop_marked_table'] = $count_table;
		
		$count_formula = substr_count ( $statistic, "<extension extension_name='loop_formula'");
		#print atxt ($count_formula,'count_formula');
		$statistic_result['loop_marked_formula'] = $count_formula;
		
		$count_listing = substr_count ( $statistic, "<extension extension_name='loop_listing'");
		#print atxt ($count_listing,'count_listing');
		$statistic_result['loop_marked_listing'] = $count_listing;
		
		$count_task = substr_count ( $statistic, "<extension extension_name='loop_task'");
		#print atxt ($count_task,'count_task');
		$statistic_result['loop_marked_task'] = $count_task;
		
		$count_media = substr_count ( $statistic, "<extension extension_name='loop_media'");
		#print atxt ($count_media,'count_media');
		$statistic_result['loop_marked_media'] = $count_media;
		
		$video = 0;
		$audio = 0;
		$rollover = 0;
		$interaction = 0;
		$animation = 0;
		$simulation = 0;
		$click = 0;
		$dragdrop = 0;
		
		
		$count_matches = preg_match_all("/(<extension extension_name=\'loop_media\')(.*)(<\/extension>)/Us", $statistic, $matches);
		#print atxt ($matches,'matches');
		if (isset($matches[0])) {
			$media_matches = $matches[0];
			#print atxt ($media_matches,'$media_matches');
			foreach ($media_matches as $media_match) {
				$video_matches_count = preg_match_all("/((type=\"video\")|(type=\'video\'))/Us", $media_match, $video_matches);
				$video = $video + $video_matches_count;
		
				$audio_matches_count = preg_match_all("/((type=\"audio\")|(type=\'audio\'))/Us", $media_match, $audio_matches);
				$audio = $audio + $audio_matches_count;
		
				$rollover_matches_count = preg_match_all("/((type=\"rollover\")|(type=\'rollover\'))/Us", $media_match, $rollover_matches);
				$rollover = $rollover + $rollover_matches_count;
		
				$interaction_matches_count = preg_match_all("/((type=\"interaction\")|(type=\'interaction\'))/Us", $media_match, $interaction_matches);
				$interaction = $interaction + $interaction_matches_count;
		
				$animation_matches_count = preg_match_all("/((type=\"animation\")|(type=\'animation\'))/Us", $media_match, $animation_matches);
				$animation = $animation + $animation_matches_count;
		
				$simulation_matches_count = preg_match_all("/((type=\"simulation\")|(type=\'simulation\'))/Us", $media_match, $simulation_matches);
				$simulation = $simulation + $simulation_matches_count;
		
				$click_matches_count = preg_match_all("/((type=\"click\")|(type=\'click\'))/Us", $media_match, $click_matches);
				$click = $click + $click_matches_count;
		
				$dragdrop_matches_count = preg_match_all("/((type=\"dragdrop\")|(type=\'dragdrop\'))/Us", $media_match, $dragdrop_matches);
				$dragdrop = $dragdrop + $dragdrop_matches_count;
		
		
			}
		}
		#print atxt ($video,'$video');
		#print atxt ($audio,'$$audio');
		#print atxt ($rollover,'$$rollover');
		#print atxt ($interaction,'$$interaction');
		#print atxt ($animation,'$$animation');
		#print atxt ($simulation,'$$simulation');
		#print atxt ($dragdrop,'$$$dragdrop');
		
		$statistic_result['loop_marked_media_video'] = $video;
		$statistic_result['loop_marked_media_audio'] = $audio;
		$statistic_result['loop_marked_media_rollover'] = $rollover;
		$statistic_result['loop_marked_media_interaction'] = $interaction;
		$statistic_result['loop_marked_media_animation'] = $animation;
		$statistic_result['loop_marked_media_simulation'] = $simulation;
		$statistic_result['loop_marked_media_dragdrop'] = $dragdrop;
		
		
		$count_loop_video_matches = preg_match_all("/(<extension extension_name=\'loop_video\')(.*)(<\/extension>)/Us", $statistic, $loop_video_matches);
		#print atxt ($count_loop_video_matches,'count_loop_video_matches');
		
		$count_embed_video_matches = preg_match_all("/(<extension extension_name=\'embed_video\')(.*)(<\/extension>)/Us", $statistic, $embed_video_matches);
		#print atxt ($embed_video_matches,'embed_video_matches');
		$youtube = 0;
		$vimeo = 0;
		if (isset($embed_video_matches[0])) {
			foreach($embed_video_matches[0] as $embed_video_match) {
				#print atxt ($embed_video_match,'$embed_video_match');
				$youtube_matches_count = preg_match_all("/((service=\"youtube\")|(service=\'youtube\')|(service=\"youtubehd\")|(service=\'youtubehd\'))/Us", $embed_video_match, $youtube_match);
				#print atxt ($embed_video_match,'$embed_video_match');
				$youtube = $youtube + $youtube_matches_count;
		
				$vimeo_matches_count = preg_match_all("/((service=\"vimeo\")|(service=\'vimeo\'))/Us", $embed_video_match, $vimeo_match);
				#print atxt ($embed_video_match,'$embed_video_match');
				$vimeo = $vimeo + $vimeo_matches_count;
			}
		}
		#print atxt ($count_embed_video_matches,'count_embed_video_matches');
		
		$statistic_result['loop_embed_video'] = $count_loop_video_matches + $count_embed_video_matches;
		
		#print atxt ($youtube,'youtube');
		#print atxt ($vimeo,'vimeo');
		$statistic_result['loop_embed_video_youtube'] = $youtube;
		$statistic_result['loop_embed_video_vimeo'] = $vimeo;
		
		
		$count_loop_audio_matches = preg_match_all("/(<extension extension_name=\'loop_audio\')(.*)(<\/extension>)/Us", $statistic, $loop_audio_matches);
		#print atxt ($count_loop_audio_matches,'count_loop_audio_matches');
		$statistic_result['loop_embed_audio'] = $count_loop_audio_matches;
		
		$result->addValue( $this->getModuleName(), 'statistic', $statistic_result);
		
		
		#$result->addValue( $this->getModuleName(), 'statistic', $loop_xml);
		
		/*
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
		'loopstructure',
		array(
			'Id',
			'IndexArticleId',
			'TocLevel',
			'TocNumber',
			'TocText',
			'Sequence',
			'ArticleId',
			'PreviousArticleId',
			'NextArticleId',
			'ParentArticleId',
			'IndexOrder'
			),
			array(
			//'TocLevel' => 0
			),
			__METHOD__,
			array(
			'ORDER BY' => 'Sequence ASC'
			)
			);		
		$structureitems = array();
		foreach ( $res as $row ) {
			if ($row->TocLevel < 2) {
				$tn = ($row->TocNumber == '') ? 0 : $row->TocNumber;
				$tl = ($row->TocLevel == '') ? 0 : $row->TocLevel;

				#$result->addValue( $this->getModuleName(), 'structureitem_'.$row->Sequence , array ('tocnumber'=>$tn,'toctext'=>$row->TocText,'article'=>$row->ArticleId));
			
				$structureitems[] = array ('toclevel'=>$tl,'tocnumber'=>$tn,'toctext'=>$row->TocText,'article'=>$row->ArticleId);
			}	
		}
		
		$result->addValue( $this->getModuleName(), 'statistic', $structureitems);
		*/
		

		

		
		wfProfileOut( __METHOD__ );
	}

	public function getAllowedParams() {
		return array();
	}


	public function getParamDescription() {
		return array();
	}


	public function mustBePosted() { return false; }


	public function isWriteMode() { return true; }


	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'anontoken' ),
			array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			array( 'code' => 'invalidpage', 'info' => 'ArticleFeedback is not enabled on this page' ),
			array( 'code' => 'invalidpageid', 'info' => 'Page ID is missing or invalid' ),
			array( 'code' => 'missinguser', 'info' => 'User info is missing' ),
		) );
	}

	public function getDescription() {
		return array(
			'Get LOOP Statistic'
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=loop-get-statistic'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': version 1.0';
	}
	
	
	
	
	
	
	
	function get_loop_xml($tmp_name) {
		global $IP, $xmlg, $content_provider, $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $xml, $wgSitename, $wgServer,$wgLanguageCode ;
	
		$content_provider = new ContentProviderMySQL ;
		$converter = new MediaWikiConverter ;
		$xml = "" ;
	
		$aArticles = $this->get_articles() ;
		$xmlg["book_title"] = $aArticles[0];
		$xmlg["namespace_template"] = "Template" ;
	
		$xmlg["site_base_url"] = str_replace ('http://' , '' , $wgServer ).'/loop';
		$xmlg["book_title"] = "No title" ;
		$xmlg['sourcedir'] = "." ;
		//$xmlg["temp_dir"] = $IP."/tmp/" ;
		$xmlg["temp_dir"] = "/var/tmp/" ;
		$xmlg['is_windows'] = false ;
		$xmlg['allow_get'] = false ;
		$xmlg["use_toolserver_url"] = false ;
		$xmlg["odt_footnote"] = 'footnote' ;
		$xmlg["allow_xml_temp_files"] = true ;
		$xmlg["use_xml_temp_files"] = false ;
		$xmlg["xhtml_source"] = false ;
		$xmlg['xhtml_justify'] = false ;
		$xmlg['xhtml_logical_markup'] = false ;
		$xmlg['text_hide_images'] = false ;
		$xmlg['text_hide_tables'] = false ;
	
	
	
		while ( $a = array_shift( $aArticles ) ) {
			$wiki2xml_authors = array () ;
	
			# Article page|Article name
			$a = explode ( '|' , $a ) ;
			if ( count ( $a ) == 1 ) $a[] = $a[0] ;
			$title_page = trim ( array_shift ( $a ) ) ;
			$title_name = trim ( array_pop ( $a ) ) ;
			set_time_limit(120);
			$wikitext = $content_provider->get_wiki_text ( $title_page ) ;
				
			// deal with special characters
			$wikitext = html_entity_decode($wikitext,ENT_NOQUOTES,'UTF-8');
				
			add_authors ( $content_provider->authors ) ;
			$articlexml="";
			$articlexml=$converter->article2xml ( $title_name , $wikitext  );
			$xml.=$articlexml;
			//append_to_xml ( $xml , $converter->article2xml ( $title_name , $wikitext , $xmlg, $aArticles ) ) ;
			#$xml .= $converter->article2xml ( $title_name , $wikitext , $xmlg, &$aArticles ) ;
		}
	
		switch ($wgLanguageCode) {
			case "de-formal":
				$lang="de";
				break;
			case "de":
				$lang="de";
				break;
			case "en":
				$lang="en";
				break;
			default:
				$lang="de";
				break;
		}
	
		// Datei Praefix nach LOOP Sprache editieren
		if($lang == "de")
			$xml = str_replace(array(">file:", ">File:", ">datei:"), ">Datei:", $xml);
		elseif($lang == "en")
		$xml = str_replace(array(">datei:", ">Datei:", ">File:"), ">file:", $xml);
	
		//$qrFile = $IP."/tmp/".$tmp_name."_qr.png";
		$qrFile = sys_get_temp_dir().'/'.$tmp_name."_qr.png";
		QRcode::png($wgServer, $qrFile);
	
		$xmlresult="<?xml version='1.0' encoding='UTF-8' ?>\n";
		$xmlresult.="<articles ";
		$xmlresult.='xmlns:xhtml="http://www.w3.org/1999/xhtml" ';
		$xmlresult.='title="'.$this->get_structure_title().'" ';
		$xmlresult.='url="'.$wgServer.'" ';
		$xmlresult.='date="'.date('d.m.Y H:i').'" ';
		$xmlresult.='lang="'.$lang.'" ';
		$xmlresult.='qrimage="'.$qrFile.'" ';
		$xmlresult.=">" ;
		$tocxml=$this->get_toc();
		$xmlresult.="<toc>".$tocxml."</toc>";
		$xmlresult.=$xml;
		$glossaryxml=$this->get_glossary();
		$xmlresult.="<glossary>".$glossaryxml."</glossary>";
		$xmlresult.="</articles>" ;
	
		return $xmlresult;
	
	}
	function get_glossary () {
		// global $converter, $content_provider;
	
		$content_provider = new ContentProviderMySQL ;
		$converter = new MediaWikiConverter ;
	
		$return = '';
	
		$glossary = Category::newFromName('Glossar');
		$glossary_entrys = $glossary->getMembers();
	
		foreach ($glossary_entrys as $glossary_entry) {
			$wikitext = $content_provider->get_wiki_text ( $glossary_entry ) ;
				
			// deal with special characters
			$wikitext = html_entity_decode($wikitext,ENT_NOQUOTES,'UTF-8');
				
			//add_authors ( $content_provider->authors ) ;
			$articlexml="";
			$articlexml=$converter->article2xml ( $glossary_entry->getText() , $wikitext  );
			$return.=$articlexml;
		}
	
		return $return;
	}
	
	
	function get_toc () {
	
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgParser;
		$in_index=null;
	
		$return='';
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
				'loopstructure',
				array(
						'Id',
						'IndexArticleId',
						'TocLevel',
						'TocNumber',
						'TocText',
						'Sequence',
						'ArticleId',
						'PreviousArticleId',
						'NextArticleId',
						'ParentArticleId',
						'IndexOrder'
				),
				array(
						'TocLevel' => 0,
				),
				__METHOD__,
				array(
						'ORDER BY' => 'IndexOrder'
				)
		);
		$tree='';
		$tree.='<chapter>';
		foreach ( $res as $row ) {
			$ta=$row->ArticleId;
			$ti=$row->IndexArticleId;
			$tl=$row->TocLevel;
			if (($wgLoopStructureUseTopLevel)&&($tl==0)) {
				$tn=$row->IndexOrder;
			} else {
				$tn='';
			}
	
			$tempArticle= Article::newFromID($ta);
			if ($tempArticle) {
				$tempTitle=($tempArticle->getTitle());
	
				if ($tempTitle) {
	
					$t=$tempTitle->mTextform;
	
					$tree.='<page ';
					$tree.= 'id="article_'.$ta.'" ';
					//$tree.= '><a name="'.$tempTitle->mTextform.'" href="'.$tempTitle->getFullURL().'"><span class="tocnumber">'.$tn.'</span> <span class="toctext">'.$t.'</span></a>';
					$tree.= ' title="'.$this->escapexml($tempTitle->mTextform).'"  href="'.$tempTitle->getFullURL().'" tocnumber="'.$tn.'" toctext="'.$this->escapexml($t).'" toclevel="'.$tl.'">';
	
					$tree.=$this->make_toc($ti,$ta);
				}
			}
			$tree.='</page>';
	
		}
		$tree.='</chapter>';
		$return.=$tree;
	
		return $return;
	}
	
	
	function get_articles($info=false) {
		$pages=array();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select('loopstructure',array('Id','IndexArticleId','TocLevel','TocNumber','TocText','Sequence','ArticleId','PreviousArticleId','NextArticleId','ParentArticleId','IndexOrder'),array(),__METHOD__,array('ORDER BY' => 'Sequence ASC'));
	
		foreach ( $res as $row ) {
			$tl=$row->TocLevel;
			$tn=$row->TocNumber;
			$tt=$row->TocText;
			$ta=$row->ArticleId;
	
			//$tempArticle= Article::newFromID($ta);
	
			$page=array();
			$page['TocLevel']=$tl;
			$page['TocNumber']=$tn;
			$page['TocText']=$tt;
			$page['ArticleId']=$ta;
	
			if ($info) {
				$pages[]=$page;
			} else {
				$pages[]=$tt;
			}
		}
		return $pages;
	}	
	
	
	
	function escapexml($text) {
		$text = str_replace('&', '&amp;', $text);
		$text = str_replace('<', '&lt;', $text);
		$text = str_replace('>', '&gt;', $text);
		$text = str_replace('"', '&quot;', $text);
		return $text;
	}	
	
	
	function get_structure_title() {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
				'loopstructure',
				array(
						'Id',
						'IndexArticleId',
						'TocLevel',
						'TocNumber',
						'TocText',
						'Sequence',
						'ArticleId',
						'PreviousArticleId',
						'NextArticleId',
						'ParentArticleId',
						'IndexOrder'
				),
				array(
						'TocLevel' => 0
				),
				__METHOD__,
				array(
				)
		);
		//$row=mysql_fetch_assoc($res->result);
		//$row=mysql_fetch_array($res->result, MYSQL_ASSOC);
		//wfDebug( __METHOD__ . ': res : '.print_r($res,true)."\n");
		//wfDebug( __METHOD__ . ': result : '.print_r($res->result,true)."\n");
		//wfDebug( __METHOD__ . ': row : '.print_r($row,true)."\n");
	
		foreach ( $res as $row ) {
			$tt=$row->TocText;
		}
		//wfDebug( __METHOD__ . ': tt : '.print_r($tt,true)."\n");
	
		return $tt;
	}	
	
	
	function make_toc($index,$parent) {
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgParser;
		$return='';
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
				'loopstructure',
				array(
						'Id',
						'IndexArticleId',
						'TocLevel',
						'TocNumber',
						'TocText',
						'Sequence',
						'ArticleId',
						'PreviousArticleId',
						'NextArticleId',
						'ParentArticleId',
						'IndexOrder'
				),
				array(
						'IndexArticleId' => $index,
						'ParentArticleId' => $parent
				),
				__METHOD__,
				array(
						'ORDER BY' => 'Sequence ASC'
				)
		);
		$first=true;
		foreach ( $res as $row ) {
			if ($first) {$return.='<chapter>';$first=false;}
			$tl=$row->TocLevel;
			$tn=$row->TocNumber;
			$tt=$row->TocText;
			$ta=$row->ArticleId;
	
			$tempArticle= Article::newFromID($ta);
			if ($tempArticle) {
				$tempTitle=($tempArticle->getTitle());
				if ($tempTitle) {
					if (!$wgLoopStructureNumbering) {
						$tn='';
					} else {
						if (($wgLoopStructureUseTopLevel)&&($tl==0)) {
							$tn=$row->IndexOrder;
						}
					}
					$return.='<page ';
					$return.= 'id="articleid_'.$ta.'" ';
	
					$t=$tempTitle->mTextform;
					$return.= ' title="'.escapexml($tempTitle->mTextform).'"  href="'.$tempTitle->getFullURL().'" tocnumber="'.$tn.'" toctext="'.escapexml($t).'" toclevel="'.$tl.'">';
					$return.= $this->make_toc($index,$ta);
					$return.='</page>';
				}
			}
		}
		if (!$first) {$return.='</chapter>';}
	
	
		return $return;
	}
	
	
	
	
	
	
	
	
	
	
}