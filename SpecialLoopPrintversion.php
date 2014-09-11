<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

global $IP;

require_once ($IP."/extensions/wiki2xml/mediawiki_converter.php");
require_once ($IP."/extensions/Math/MathRenderer.php");
require_once ($IP."/extensions/Math/MathTexvc.php");
require_once ($IP."/extensions/Loop/phpqrcode/phpqrcode.php");
require_once ($IP."/extensions/BiblioPlus/BiblioPlus.php");



function escapexml($text) {
	$text = str_replace('&', '&amp;', $text);
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('"', '&quot;', $text);
	return $text;
}


function xslt_get_page_xml($input) {
	$input_object=$input[0];
	$pagename=$input_object->textContent;
	
	$content_provider = new ContentProviderMySQL ;
	$converter = new MediaWikiConverter ;
	
	$wikitext = $content_provider->get_wiki_text ($pagename) ;
	$wikitext = html_entity_decode($wikitext,ENT_NOQUOTES,'UTF-8');
			
	$articlexml="";
	$articlexml=$converter->article2xml($pagename,$wikitext);
	
	
	$return_doc = new DOMDocument;
	$return_doc->loadXml($articlexml);
		
	return $return_doc;	
}


function xslt_get_index() {
	global $IP,	$wgOut, $wgParser, $wgLoopStructureNumbering;
	
	$sql="select indexes.in_title, page.page_id, page.page_title, loopstructure.TocNumber from (indexes left join page on indexes.in_from=page.page_id) left join loopstructure on page.page_id= loopstructure.ArticleId  order by indexes.in_title, loopstructure.TocNumber";
	$dbr = wfGetDB( DB_SLAVE );
	$res = $dbr->query( $sql, __METHOD__, true );

	$groups=array();
	setlocale(LC_CTYPE, 'de_DE');
	foreach ( $res as $row ) {
		$letter = $row->in_title;
		$letter = iconv('UTF-8', 'US-ASCII//TRANSLIT', $letter);
		$letter = mb_substr($letter,0,1);
		if ((ord($letter)>=48) && (ord($letter)<=57)) {
			$letter = '#';
		}
		$indextitle = str_replace( '_', ' ', $row->in_title);
		$groups[$letter][$indextitle][]=$row->page_title;
	}
	
	$doc = new DOMDocument('1.0');
	$doc->formatOutput = true;	
	
	$root = $doc->createElement('loop_index');
	$root = $doc->appendChild($root);
	
	foreach ($groups as $letter=>$group) {
		$loop_index_group = $doc->createElement('loop_index_group');
		$letterAttribute = $doc->createAttribute('letter');
		$letterAttribute->value = $letter;
		$loop_index_group->appendChild($letterAttribute);
		$loop_index_group = $root->appendChild($loop_index_group);
	
		foreach ($group as $item=>$pages) {
			$loop_index_item = $doc->createElement('loop_index_item');
			$loop_index_item = $loop_index_group->appendChild($loop_index_item);	
			
			$loop_index_title_value = str_replace( '_', ' ', $item );
			$loop_index_title = $doc->createElement('loop_index_title', $loop_index_title_value);
			$loop_index_title = $loop_index_item->appendChild($loop_index_title);	
			
			$loop_index_pages = $doc->createElement('loop_index_pages');
			$loop_index_pages = $loop_index_item->appendChild($loop_index_pages);	
			
			$furthervalue='0';
			foreach ($pages as $page) {
				$loop_index_page = $doc->createElement('loop_index_page');
				
				$furtherAttribute = $doc->createAttribute('further');
				$furtherAttribute->value = $furthervalue;
				$loop_index_page->appendChild($furtherAttribute);
								
				$pagetitleAttribute = $doc->createAttribute('pagetitle');
				$pagetitleAttribute->value = Title::newFromText($page);
				$loop_index_page->appendChild($pagetitleAttribute);
				
				$loop_index_pages->appendChild($loop_index_page);
				
				$furthervalue = '1';
			}
		}
	}
	return $doc;
}


function xslt_transform_math($input) {
	global $IP;
	$input_object=$input[0];
	$mathcontent=$input_object->textContent;
	$math = new MathTexvc('\pagecolor{White}'.$mathcontent);
	$math->render('rgb 1.0 1.0 1.0');
	$mathpath=$math->getMathImageUrl();
	$imagepath=$IP.str_replace('mediawiki/','',$mathpath);

	if (file_exists ( $imagepath)) {
		$return=$imagepath;
	} else {
		$return= '';
	}


	return $return;
}

function xslt_transform_graphviz($input) {
	global $IP, $wgParser, $wgParserConf, $wgUser,$wgServer,$wgUploadPath, $wgUploadDirectory, $wgScriptPath;
	$input_object=$input[0];
	$graphvizcontent=$input_object->textContent;
	$torender='<graphviz border="frame" format="png">'.$graphvizcontent.'</graphviz>';

	$parser = new Parser( $wgParserConf );
	$parserOptions = ParserOptions::newFromUser( $wgUser );
	$parser->Options($parserOptions);
	$title = Title::newFromText("graphviz");
	$output = $parser->parse($torender, $title, $parserOptions);
	$html_text = $output->getText();

 	$array = array();
    preg_match( '/(?<=src=")([^"]*)(?=")/i', $html_text, $array ) ;
    $imgsrc = str_replace($wgScriptPath, '', $IP).$array[1]  ;


    $size=getimagesize($imgsrc);
	$width=0.214*intval($size[0]);
	if ($width>150) {
		$imagewidth='150mm';
	} else {
		$imagewidth=round($width,0).'mm';
	}
	

	$return_xml =  '<php_link_image imagepath="'.$imgsrc.'" imagewidth="'.$imagewidth.'"></php_link_image>' ;
	$return_doc = new DOMDocument;
	$return_doc->loadXml($return_xml);

	$return = $return_doc;
	return $return;
}



function xslt_transform_link($input) {
	
	if(isset($_GET['textonly']))
		$textonly = $_GET['textonly'];
	
	libxml_use_internal_errors(true);
	
	$return='';
	$childs=array();
	$input_object=$input[0];
	//wfDebug( __METHOD__ . ': input : '.print_r($input_object->C14N(),true)."\n");
	
	/*
	wfDebug( __METHOD__ . ': input_obj : '.print_r($input_object,true)."\n");
	
    $children = $input_object->childNodes; 
    foreach ($children as $child) { 
        wfDebug( __METHOD__ . ': input_child : '.print_r($child->C14N(),true)."\n");
		if ($child->tagName == 'part') {
			wfDebug( __METHOD__ . ': input_part : '.print_r($child->nodeValue,true)."\n");
		}
    } 	
	*/
	
	//$input_value=print_r($input_object->tagName,true);

	if ($input_object->hasAttribute('type')) {
		$childs['type']=$input_object->getAttribute('type');
	}
	if ($input_object->hasAttribute('href')) {
		$childs['href']=$input_object->getAttribute('href');
	}

	$link_childs=$input_object->childNodes;
	$num_childs=$link_childs->length;

	for ($i = 0; $i < $num_childs; $i++) {
		$child=$link_childs->item($i);
		if (isset($child->tagName)) {
			$child_name=$child->tagName;
			if ($child_name=='') {$child_name='text';}
			$child_value=$child->textContent;
		} else {
			$child_name='text';
			#$child_value='';
			$child_value=$child->textContent;
		}
		
		
		
		if ($child_name == 'part') {
			if (substr($child_value, -2) == 'px') {
				$child_name = 'width';
				$child_value = substr($child_value,0,-2);
			} elseif (($child_value == 'right') || ($child_value == 'left') || ($child_value == 'center')) {
				$child_name = 'align';
				
			}
		}
		
		
		
		
		
		$childs[$child_name]=$child_value;
	}

	if (!array_key_exists('type', $childs)) {
		$childs['type']='internal';
	}
	if (array_key_exists('text', $childs)) {
		$childs['text']=escapexml($childs['text']);
	}
  //wfDebug( __METHOD__ . ': input_childs : '.print_r($childs,true)."\n");

	if ($childs['type']=='external') {
		$return_xml =  '<php_link_external href="'.$childs['href'].'">'.$childs['text'].'</php_link_external>' ;
	} else {
		$target=$childs['target'];
		$target_array=explode(':',$target);
		if (($target_array[0]=='Datei')||($target_array[0]=='File')||($target_array[0]=='Bild')||($target_array[0]=='Image')||($target_array[0]=='file')) {
			
			if($textonly){
				$imagepath = 'http://loop.oncampus.de/mediawiki/extensions/Loop/placeholder.png';
				$imagewidth = '10mm';
				$return_xml =  '<php_link_image imagepath="'.$imagepath.'" imagewidth="'.$imagewidth.'"></php_link_image>';
			}else{
			
				$target_uri=$target_array[1];
				$filetitle=Title::newFromText( $target_uri, NS_FILE );
				$file = wfLocalFile($filetitle);
				if (is_object($file)) {
				$imagepath=$file->getLocalRefPath();
				} else {
				$imagepath='';
				}
					
				$imagewidth='150mm';
				/*
				if (array_key_exists('part', $childs)) {
					$part=$childs['part'];
					if (stristr('px',$part)) {
						$part_array=explode('px',$part);
						if (count($part_array)==2) {
							$px=trim($part_array[0]);
							$mm=round(0.214*intval($px),0);
							$imagewidth=$mm.'mm';
						}
					} else {
					    $size=getimagesize($imagepath);
						$width=0.214*intval($size[0]);
						if ($width>150) {
							$imagewidth='150mm';
						} else {
							$imagewidth=round($width,0).'mm';
						}				
					}
					*/
				if (array_key_exists('width', $childs)) {
					$width=0.214*intval($childs['width']);
					if ($width>150) {
						$imagewidth='150mm';
					} else {
						$imagewidth=round($width,0).'mm';
					}								
				} else {
					if ($imagepath!='') {
						$size=getimagesize($imagepath);
						$width=0.214*intval($size[0]);
						if ($width>150) {
							$imagewidth='150mm';
						} else {
							$imagewidth=round($width,0).'mm';
						}				
					} else {
						$imagewidth='150mm';
					}
				}
					
				//$input_value=print_r($childs,true);
				$return_xml =  '<php_link_image imagepath="'.$imagepath.'" imagewidth="'.$imagewidth.'" ';
				if (isset($childs['align'])) {
					$return_xml .= ' align="'.$childs['align'].'" ';
				}
				$return_xml .=  '></php_link_image>';
			
			}
				
		} else if (($target_array[0]=='Kategorie')||($target_array[0]=='Category')||($target_array[0]=='kategorie')||($target_array[0]=='category')) {
		
			$return_xml = '';
		
		} else {
			// internal Link
			if (!array_key_exists('text', $childs)) {
          if(array_key_exists('part',$childs)) {
            $childs['text']=$childs['part'];
          } else {
            $childs['text']=$target;
          }
			} 
			if (!array_key_exists('href', $childs)) {
				$childs['href']=$target;
			}
			$return_xml =  '<php_link_internal href="'.$childs['href'].'">'.$childs['text'].'</php_link_internal>' ;
		}



	}
	$return_doc = new DOMDocument;
	#$return_doc->loadXml($return_xml);
	
	$old_error_handler = set_error_handler("myErrorHandler");
	
	libxml_use_internal_errors(true);
	
	try {
		$return_doc->loadXml($return_xml);
	} catch ( Exception $e ) {
		
	}	
	restore_error_handler();
	
	$return=$return_doc;

	return $return;
}

function xslt_get_config($paramter) {
	global $wgLoopShowPagetitle;
	$return = '';
	
	switch ($paramter) {
		case 'wgLoopShowPagetitle':
			if ($wgLoopShowPagetitle) {
				$return = 'true';
			} else {
				$return = 'false';
			}
				
			break;
		default:
			break;
	}
	
	return $return;
}


function xslt_figure_width($input) {
	$return='';
	$childs=array();
	$input_object=$input[0];
	
	# edit ANDRE/AS:	
	if(is_object($input[0])){

		//$input_value=print_r($input_object->tagName,true);
	
		if ($input_object->hasAttribute('type')) {
			$childs['type']=$input_object->getAttribute('type');
		}
		if ($input_object->hasAttribute('href')) {
			$childs['href']=$input_object->getAttribute('href');
		}
	
		$link_childs=$input_object->childNodes;
		$num_childs=$link_childs->length;
	
		for ($i = 0; $i < $num_childs; $i++) {
			$child=$link_childs->item($i);
			$child_name=$child->tagName;
			if ($child_name=='') {$child_name='text';}
			$child_value=$child->textContent;
			$childs[$child_name]=$child_value;
		}
	
		if (!isset($childs['type'])) {
			$childs['type']='';
		}
		
		if ($childs['type']=='external') {
			$return =  '150' ;
		} else {
			$target=$childs['target'];
			$target_array=explode(':',$target);
			if (($target_array[0]=='Datei')||($target_array[0]=='File')||($target_array[0]=='Bild')||($target_array[0]=='Image')||($target_array[0]=='file')) {
					
				$target_uri=$target_array[1];
				$filetitle=Title::newFromText( $target_uri, NS_FILE );
				$file = wfLocalFile($filetitle);
				
				if (is_object($file)) {
				
					$imagepath=$file->getLocalRefPath();
						
					$imagewidth='150';
					if (array_key_exists('part', $childs)) {
						$part=$childs['part'];
						$part_array=explode('px',$part);
						if (count($part_array)==2) {
							$px=trim($part_array[0]);
							$mm=round(0.214*intval($px),0);
							$imagewidth=$mm;
						}
					} else {
						if ($imagepath!='') {
							$size=getimagesize($imagepath);
							$width=0.214*intval($size[0]);
							if ($width>150) {
								$imagewidth='150';
							} else {
								$imagewidth=round($width,0);
							}		
						} else {
							$imagewidth='150';
						}
					}				
				} else {
					$imagewidth='150';
				}
				//$input_value=print_r($childs,true);
				$return =  $imagewidth;
					
			} else {
				// internal Link
				if (!array_key_exists('text', $childs)) {
					$childs['text']=$target;
				}
				if (!array_key_exists('href', $childs)) {
					$childs['href']=$target;
				}
				$return= '150' ;
			}
	
	
	
		}
	
		return $return;
		
	}else{
		
		// input is non-object
		
	}
	
}


function xslt_linktype($input) {
	$linktype='Page';

	if (is_array($input)) {
		$input_object=$input[0];
		$input_value=$input_object->textContent;
		$input_array=explode(':',$input_value);
		if (count($input_array)==2) {
			$target_type=trim($input_array[0]);
			$target_uri=trim($input_array[1]);
			if (($target_type=='Datei')||($target_type=='File')||($target_type=='Bild')||($target_type=='Image')||($target_type=='file')) {
				$linktype='Datei';
			}
		}
	}

	return $linktype;
}

function xslt_imagewidth($input) {
	$imagewidth='150mm';
	if (is_array($input)) {
		if (count($input)>0) {
			$input_object=$input[0];
			$input_value=$input_object->textContent;
			$input_array=explode('px',$input_value);
			if (count($input_array)==2) {
				$px=trim($input_array[0]);
				$mm=round(0.214*intval($px),0);
				$imagewidth=$mm.'mm';
			}
		}
	}
	return $imagewidth;
}

function xslt_imagepath($input) {
	$imagepath='';

	if (is_array($input)) {
		$input_object=$input[0];
		$input_value=$input_object->textContent;
		$input_array=explode(':',$input_value);
		if (count($input_array)==2) {
			$target_uri=trim($input_array[1]);
			#wfDebug( __METHOD__ . ': target_uri : '.print_r($target_uri,true)."\n");
			$filetitle=Title::newFromText( $target_uri, NS_FILE );
			#wfDebug( __METHOD__ . ': filetitle : '.print_r($filetitle,true)."\n");
			$file = wfLocalFile($filetitle);
			#wfDebug( __METHOD__ . ': file : '.print_r($file,true)."\n");
			/*
			if ($file->exists()) {
				#wfDebug( __METHOD__ . ': exists : '.print_r('ja',true)."\n");
			} else {
				#wfDebug( __METHOD__ . ': exists : '.print_r('nein',true)."\n");
			}
			*/
			if (is_object($file)) {
			$imagepath=$file->getLocalRefPath();
			} else {
			$imagepath='';
			}
			
			#wfDebug( __METHOD__ . ': imagepath : '.print_r($imagepath,true)."\n");	
		}
	}

	return $imagepath;
}



function targetPath($input) {

	if (is_array($input)) {

		$input_object=$input[0];
		$input_value=$input_object->textContent;

		$input_array=explode(':',$input_value);
		if (count($input_array)==2) {
			$target_type=trim($input_array[0]);
			$target_uri=trim($input_array[1]);
				
			if (($target_type=='Datei')||($target_type=='File')||($target_type=='Bild')||($target_type=='Image')||($target_type=='file')) {

				$filetitle=Title::newFromText( $target_uri, NS_FILE );
				$file = wfLocalFile($filetitle);
				$fullpath=$file->getLocalRefPath();

				//$return_xml='<fo:block><fo:external-graphic scaling="uniform" content-width="140mm" content-height="scale-to-fit" src="'.$fullpath.'"></fo:external-graphic></fo:block>';


				//$return_xml =  "<fo:block>".$input_value."</fo:block>" ;
				$return_xml =  '<php_image>'.$fullpath.'</php_image>' ;
				$return_doc = new DOMDocument;
				$return_doc->loadXml($return_xml);
				 
				//var_dump($return_doc);
				//exit;
				 
				$return=$return_doc;

			} else {
				$return_xml =  "<fo:block>".$input_value."</fo:block>" ;
				$return_doc = new DOMDocument;
				$return_doc->loadXml($return_xml);
				$return=$return_doc;
			}
				
		} else {
			$return_xml =  "<fo:block>".$input_value."</fo:block>" ;
			$return_doc = new DOMDocument;
			$return_doc->loadXml($return_xml);
			$return=$return_doc;
		}

			
	} else {
		$return_xml =  "<fo:block>".$input_value."</fo:block>" ;
		$return_doc = new DOMDocument;
		$return_doc->loadXml($return_xml);
		$return=$return_doc;
	}


	return $return;
}


function get_biblio() {
	$return_doc='';
	$content_provider = new ContentProviderMySQL ;

	$bibliopagename=wfMsg('bibliographypage');
	$wikitext = $content_provider->get_wiki_text ($bibliopagename) ;

	
	if (!$wikitext=='') {

		$lbib=new BiblioPlus();

		$parser = new Parser( $wgParserConf );
		$parserOptions = ParserOptions::newFromUser( $wgUser );
		$parser->Options($parserOptions);

		$parser_data = array();

		$bibt=Title::newFromText($bibliopagename);

		$parser_data['parser'] = $parser;
		$parser_data['title'] = $bibt;
		$parser_data['options'] = $parser->getOptions();
		 
		$matches=array();
		$parser->extractTagsAndParams( array('biblio') , $wikitext, $matches);
		
		
		if (count($matches)>0) {
			$b=array_pop($matches);
			$bibtext=$b[1];
			$test=$lbib->render_biblio($bibtext,$parser_data ,true,true,true);
			$return_xml =  trim($test);
			$converter = new MediaWikiConverter ;
			$articlexml=$converter->article2xml ( $bibliopagename, $return_xml  );

//wfDebug( __METHOD__ . ': articlexml : '.print_r($articlexml,true)."\n");	

			$return_doc = new DOMDocument;
			$return_doc->loadXml($articlexml);
		}
	}
	
	if($return_doc=='') {
		$return_doc = new DOMDocument;
		$return_doc->loadXml('<i></i>');
	}
	
//wfDebug( __METHOD__ . ': return_doc : '.print_r($return_doc->saveXML(),true)."\n");	
	return $return_doc;
}

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	return true;
}


class SpecialLoopPrintversion extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopPrintversion' );
	}

	function execute( $par ) {
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
		$tmpname=$matches[0].'_'.time();
		//$tmpname=$matches[0];


		$loop_xml=$this->get_loop_xml($tmpname);
		
		$loop_xml = str_replace ( '&lt;nowiki&gt;' , '<nowiki>', $loop_xml);
		$loop_xml = str_replace ( '&lt;/nowiki&gt;' , '</nowiki>', $loop_xml);

		if ($_SERVER["SERVER_NAME"] == 'devloop.oncampus.de') {
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
		$qrFile = $IP."/tmp/".$tmpname."_qr.png";
		} else {
		$xmlFile = sys_get_temp_dir().'/'.$tmpname.".xml";
		$pdfFile = sys_get_temp_dir().'/'.$tmpname.".pdf";
		$qrFile = sys_get_temp_dir().'/'.$tmpname."_qr.png";		
    }

		$fh = fopen($xmlFile, 'w') or die("can't open xml file");
		fwrite($fh, $xmlfo);
		fclose($fh);
		
		#exit;
		
		//$cmd = `fop -c /opt/www/loop.oncampus.de/mediawiki-1.18.1/extensions/Loop/fop/fop.xml -fo $xmlFile -pdf $pdfFile 2>&1`;
		$cmd = `fop -c /opt/www/loop.oncampus.de/mediawiki/extensions/Loop/loop_fop.xml -fo $xmlFile -pdf $pdfFile 2>&1`;
		
		//$cmd = 'fop -c '.$wgeLoopFopConfig.' -fo '.$xmlFile.' -pdf '.$pdfFile;
		//shell_exec ($cmd);

		$pdfFileName = $wgSitename.".pdf";

		$fh = fopen($pdfFile, 'r') or die("can't open pdf file<br/><br/>".$xmlfo."<pre>".print_r($cmd,true).'</pre>');
		$content = fread($fh, filesize($pdfFile));
		fclose($fh);

		
		ob_end_clean();
		
		header("Content-Type: application/pdf");
		header('Content-Description: File Transfer');
		header("Content-Disposition: attachment; filename=\"".$pdfFileName."\"");
		#header("Content-Disposition: inline; filename=\"".$pdfFileName."\"");
		
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			// the content length may vary if the server is using compression
			header('Content-Length: '.strlen($content));
		}		
		echo $content;
		ob_start();
		
    if ($_SERVER["SERVER_NAME"] != 'devloop.oncampus.de') {
		unlink($xmlFile);
		unlink($pdfFile);
		unlink($qrFile);
    }

		exit;

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
							$tree.= ' title="'.escapexml($tempTitle->mTextform).'"  href="'.$tempTitle->getFullURL().'" tocnumber="'.$tn.'" toctext="'.escapexml($t).'" toclevel="'.$tl.'">';

							$tree.=$this->make_toc($ti,$ta);
						}
					}
					$tree.='</page>';

				}
				$tree.='</chapter>';
				$return.=$tree;

				return $return;
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


}
?>