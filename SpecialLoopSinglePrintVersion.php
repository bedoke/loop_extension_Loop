<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

global $IP;

require_once ($IP."/extensions/wiki2xml/mediawiki_converter.php");
require_once ($IP."/extensions/Math/MathRenderer.php");
require_once ($IP."/extensions/Math/MathTexvc.php");
require_once ($IP."/extensions/Loop/phpqrcode/phpqrcode.php");
require_once ($IP."/extensions/BiblioPlus/BiblioPlus.php");

require_once ($IP."/extensions/Loop/SpecialLoopPrintversion.php");



class SpecialLoopSinglePrintVersion extends SpecialPage {
	function __construct() {
		parent::__construct( 'SpecialLoopSinglePrintVersion' );
	}

	function execute( $par ) {
		
		$this->setHeaders();
		$this->outputHeader();
		$out = $this->getOutput();
		
		global $Biblio, $xmlg, $content_provider, $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $xml, $wgSitename, $wgServer, $IP, $wgeLoopFopConfig;

		ob_start();
		
		libxml_use_internal_errors(true);
		/*
		$test=xslt_get_index();
		var_dump($test);
		exit;
		*/

		//	$bibliographypageMsg= wfMsg('bibliographypage');
		//		var_dump($bibliographypageMsg);
		//exit;

		//$abbreviationpageMsg= wfMsg('abbreviationpage');
		//var_dump($abbreviationpageMsg);
		//exit;

		preg_match('/(?<=http:\/\/)(.+)(?=\.oncampus.de)/',$wgServer,$matches);
		
		//$tmpname=$matches[0];


#		$loop_xml=$this->get_loop_xml($tmpname);
		
		$execute_error=false;
		
		$aArticles = $this->get_articles() ;
		
		#var_dump($aArticles);exit;
		
		while ( $a = array_shift( $aArticles ) ) {
			$out->addHTML("Pr&uuml;fe Seite: ".$a.'<br/>');
			
			$tmpname=$matches[0].'_'.time();
			
			$article_xml = $this->get_article_xml($tmpname, $a);
			$loop_xml = $article_xml;
			#var_dump($article_xml);
		
		
		
		
			$loop_xml = str_replace ( '&lt;nowiki&gt;' , '<nowiki>', $loop_xml);
			$loop_xml = str_replace ( '&lt;/nowiki&gt;' , '</nowiki>', $loop_xml);
	
			if ($_SERVER["SERVER_NAME"] == 'devloop2.oncampus.de') {
				$xmlwikiFile = $IP."/tmp/".$tmpname."_wiki.xml";
				$fh = fopen($xmlwikiFile, 'w') or die("can't open xml file");
				fwrite($fh, $loop_xml);
				fclose($fh);			
			}
			#var_dump($loop_xml);
			#exit;
			
			try {
				$xml = new DOMDocument();
				$xml->loadXML($loop_xml);
			} catch (Exception $e) {
				var_dump($e);
			}
			
			
			try {
				$xsl = new DOMDocument;
				$xsl->load($IP.'/extensions/Loop/loop_xmlfo.xslt');
			} catch (Exception $e) {
				var_dump($e);			
			}
	
			try {
				$proc = new XSLTProcessor;
				$proc->registerPHPFunctions();
				$proc->importStyleSheet($xsl);
				$xmlfo = $proc->transformToXML($xml);
			} catch (Exception $e) {
				var_dump($e);
			}
			
			#var_dump($xmlfo);
			#exit;
			
			
			
			if ($_SERVER["SERVER_NAME"] == 'devloop.oncampus.de') {
				$xmlFile = $IP."/tmp/".$tmpname.".xml";
				$pdfFile = $IP."/tmp/".$tmpname.".pdf";
				#$qrFile = $IP."/tmp/".$tmpname."_qr.png";
			} else {
				$xmlFile = sys_get_temp_dir().'/'.$tmpname.".xml";
				$pdfFile = sys_get_temp_dir().'/'.$tmpname.".pdf";
				#$qrFile = sys_get_temp_dir().'/'.$tmpname."_qr.png";		
		    }
	
			$fh = fopen($xmlFile, 'w') or die("can't open xml file");
			if($fh = fopen($xmlFile, 'w')){
				// okay
			}else{
				$out->addHTML("<span style=\"color:#d32121\">Fehler beim Generieren der XML-Datei</span>".'<br/>');
				$execute_error = true;
			}
				
			fwrite($fh, $xmlfo);
			fclose($fh);
			
			#exit;
			
			//$cmd = `fop -c /opt/www/loop.oncampus.de/mediawiki-1.18.1/extensions/Loop/fop/fop.xml -fo $xmlFile -pdf $pdfFile 2>&1`;
			$cmd = `fop -c /opt/www/loop.oncampus.de/mediawiki/extensions/Loop/loop_fop.xml -fo $xmlFile -pdf $pdfFile 2>&1`;
			
			//$cmd = 'fop -c '.$wgeLoopFopConfig.' -fo '.$xmlFile.' -pdf '.$pdfFile;
			//shell_exec ($cmd);
	
			$pdfFileName = $wgSitename.".pdf";
	
			if($fh = fopen($pdfFile, 'r')){
				
			}else{
				$out->addHTML("<span style=\"color:#d32121\">Fehler beim Generieren der PDF-Datei</span>".'<br/>');
				$execute_error = true;
			}
			#$content = fread($fh, filesize($pdfFile));
			fclose($fh);
			
			
			
			
			
			
		    if ($_SERVER["SERVER_NAME"] != 'devloop.oncampus.de') {
				unlink($xmlFile);
				unlink($pdfFile);
				#unlink($qrFile);
		    }
		    
		    // beim ersten Fehler abbrechen
		    if($execute_error){
		    	break;
		    }

		}

		
		ob_end_clean();
		
		#header("Content-Type: application/pdf");
		#header('Content-Description: File Transfer');
		#header("Content-Disposition: attachment; filename=\"".$pdfFileName."\"");
		#header("Content-Disposition: inline; filename=\"".$pdfFileName."\"");
		
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			// the content length may vary if the server is using compression
			#header('Content-Length: '.strlen($content));
		}		
		
	    // beim ersten Fehler abbrechen
	    if($execute_error){
	    	return;
	    }else{
	    	$out->addHTML("<span style=\"color:#019e2e\">Alle Seiten erfolgreich getestet</span>".'<br/>');
	    }

		

		ob_start();
		
		return;

	}


	function get_article_xml($tmp_name, $article) {
		global $IP, $xmlg, $content_provider, $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $xml, $wgSitename, $wgServer,$wgLanguageCode ;

		$content_provider = new ContentProviderMySQL ;
		$converter = new MediaWikiConverter ;
		$xml = "" ;

		#$aArticles = $this->get_articles() ;
		#$xmlg["book_title"] = $aArticles[0];
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



		#while ( $a = array_shift( $aArticles ) ) {
			
			$a = $article;
			
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
		#}

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
		#$qrFile = sys_get_temp_dir().'/'.$tmp_name."_qr.png";
		#QRcode::png($wgServer, $qrFile);

		$xmlresult="<?xml version='1.0' encoding='UTF-8' ?>\n";
		$xmlresult.="<articles ";
		$xmlresult.='xmlns:xhtml="http://www.w3.org/1999/xhtml" ';
		#$xmlresult.='title="'.$this->get_structure_title().'" ';
		$xmlresult.='url="'.$wgServer.'" ';
		$xmlresult.='date="'.date('d.m.Y H:i').'" ';
		$xmlresult.='lang="'.$lang.'" ';
		#$xmlresult.='qrimage="'.$qrFile.'" ';
		$xmlresult.=">" ;
		#$tocxml=$this->get_toc();
		#$xmlresult.="<toc>".$tocxml."</toc>";
		$xmlresult.=$xml;
		#$glossaryxml=$this->get_glossary();
		#$xmlresult.="<glossary>".$glossaryxml."</glossary>";
		$xmlresult.="</articles>" ;

		return $xmlresult;

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




}
?>