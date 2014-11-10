<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

global $IP;

require_once ($IP."/extensions/Loop/SpecialLoopPrintversion.php");

require_once ($IP."/extensions/wiki2xml/mediawiki_converter.php");
require_once ($IP."/extensions/Math/MathRenderer.php");
require_once ($IP."/extensions/Math/MathTexvc.php");
require_once ($IP."/extensions/Loop/phpqrcode/phpqrcode.php");
require_once ($IP."/extensions/Biblio.php");



class SpecialLoopChapteraudio extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopChapteraudio' );
	}

	function execute( $par ) {
		global $Biblio, $xmlg, $content_provider, $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $xml, $wgSitename, $wgServer, $IP,$wgRequest, $wgText2Speech, $wgeLoopChapterAudioUrl, $wgeLoopFfmpegCmd;

		if (!$wgText2Speech === true) {
			$return.= wfMsg('loop_no_text2speech_notice');	
			$wgOut->addHTML($return);
			return true;
		}
		
		$chapter = $wgRequest->getText('chapter');

		$type = $wgRequest->getText('type');
		if (!isset($type)) {
			$type='mp3';
		}

		$text = $wgRequest->getText('text');



		#preg_match('/(?<=http:\/\/)(.+)(?=\.oncampus.de)/',$wgServer,$matches);
		#$tmpname=$matches[0].'_'.time();
		$tmpname=$wgServer.'_'.time();
		
		$id3tag_album = $this->get_structure_title(); //$matches[0];
		$num_chapters= $this->get_num_chapters();
		$id3tag_track = str_pad($chapter, 2, "0", STR_PAD_LEFT).'/'.str_pad($num_chapters, 2, "0", STR_PAD_LEFT);
		$id3tag_year = date('Y');
		$idtag_title = str_pad($chapter, 2, "0", STR_PAD_LEFT).' '.$this->get_chapter_title($chapter);

		$mp3filename=$matches[0].'_'.str_pad($chapter, 2, "0", STR_PAD_LEFT).'.mp3';
		$m4bfilename=$matches[0].'_'.str_pad($chapter, 2, "0", STR_PAD_LEFT).'.m4b';
		//$tmpname=$matches[0];


		/*
		 * XML-Version des LOOP-Kapitels erzeugen
		 */

		$loop_xml=$this->get_loop_xml($tmpname,$chapter);

		#var_dump($loop_xml);
		#exit;

		/*
		 * XML mittels XSLT in Textvariante überführen
		 */

		try {
			$xml = new DOMDocument();
			$xml->loadXML($loop_xml);
		} catch (Exception $e) {
			var_dump($e);
		}

		try {
			$xsl = new DOMDocument;
			$xsl->load($IP.'/extensions/Loop/loop_audio.xslt');
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

		if($text=='true') {
				
				
			$xmlfo = htmlspecialchars_decode($xmlfo);
			echo $xmlfo;
			exit;
		}




		$url=$wgeLoopChapterAudioUrl;

		$post='play='.urlencode($xmlfo);
		
		$cha = curl_init();
		curl_setopt($cha, CURLOPT_URL, ($url));
		curl_setopt($cha, CURLOPT_ENCODING, "UTF-8" );
		curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cha, CURLOPT_POST, true);
		curl_setopt($cha,CURLOPT_POSTFIELDS,$post);
		curl_setopt($cha,CURLOPT_TIMEOUT,600);

		$resultxml = curl_exec($cha);
		if (!$resultxml) {
			throw new Exception("Error getting data from server ($url): " . curl_error($cha));
		}
		curl_close($cha);

		$audiofile=$resultxml;

		$mp3File = sys_get_temp_dir().'/'.$tmpname.".mp3";
		//$mp3File_temp = sys_get_temp_dir().'/'.$tmpname."_temp.mp3";


		
		$mp3content = file_get_contents($audiofile);


		/*
		 * MP3-Datei vom Linguatec-Server holen und lokal speichern
		 */

		//$fh = fopen($mp3File_temp, 'w') or die("can't open mp3 file");
		$fh = fopen($mp3File, 'w') or die("can't open mp3 file");
		fwrite($fh, $mp3content);
		fclose($fh);




		
		
		/*
		 * ID3 Tags setzen
		 */
		$TextEncoding = 'UTF-8';

		require_once ($IP."/extensions/Loop/getid3/getid3.php");
		// Initialize getID3 engine
		$getID3 = new getID3;
		$getID3->setOption(array('encoding'=>$TextEncoding));

		require_once ($IP."/extensions/Loop/getid3/write.php");
		// Initialize getID3 tag-writing module
		$tagwriter = new getid3_writetags;
		//$tagwriter->filename = '/path/to/file.mp3';
		$tagwriter->filename = $mp3File;

		//$tagwriter->tagformats = array('id3v1');
		$tagwriter->tagformats = array('id3v1', 'id3v2.3');
		//$tagwriter->tagformats = array('id3v2.3');

		// set various options (optional)
		$tagwriter->overwrite_tags = true;
		//$tagwriter->overwrite_tags = false;
		$tagwriter->tag_encoding = $TextEncoding;
		//$tagwriter->remove_other_tags = true;
		$tagwriter->remove_other_tags = false;

		// populate data array
		$TagData = array(
	'title'         => array($idtag_title),
	'artist'        => array('oncampus'),
	'album'         => array($id3tag_album),
	'year'          => array($id3tag_year),
	'genre'         => array('E-Learning'),
	'track'         => array($id3tag_track),
		);
		$tagwriter->tag_data = $TagData;

		// write tags
		/*
		 if ($tagwriter->WriteTags()) {
		 echo 'Successfully wrote tags<br>';
		 if (!empty($tagwriter->warnings)) {
		 echo 'There were some warnings:<br>'.implode('<br><br>', $tagwriter->warnings);
		 }
		 } else {
		 echo 'Failed to write tags!<br>'.implode('<br><br>', $tagwriter->errors);
		 }
		 exit;
		 */
		$tagwriter->WriteTags();



		/*
		 * Umwandeln der MP3-Datei in das M4A-Format und Umbenennen in M4B
		 *
		 *  ./ffmpeg -i loop.mp3 -c:a libvo_aacenc loop.m4a
		 *
		 */


		if ($type=='m4b') {
			$m4aFile=substr($mp3File, 0, strlen($mp3File)-3).'m4a';
			$command= $wgeLoopFfmpegCmd.' -i '.$mp3File.' -c:a libvo_aacenc '.$m4aFile;
			// echo $command;
			
			shell_exec ($command);
			
			/*
			 * M4A als M4B-Datei ausliefern
			 */
			header("Content-Disposition: attachment; filename=" . urlencode($m4bfilename));
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");
			header("Content-Length: " . filesize($m4aFile));
			flush(); // this doesn't really matter.

			$fp = fopen($m4aFile, "r");
			while (!feof($fp))
			{
				echo fread($fp, 65536);
				flush(); // this is essential for large downloads
			}
			fclose($fp);			
			


		} else {


			/*
			 * MP3-Datei ausliefern
			 */
			header("Content-Disposition: attachment; filename=" . urlencode($mp3filename));
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");
			header("Content-Length: " . filesize($mp3File));
			flush(); // this doesn't really matter.

			$fp = fopen($mp3File, "r");
			while (!feof($fp))
			{
				echo fread($fp, 65536);
				flush(); // this is essential for large downloads
			}
			fclose($fp);

		}

	}

	
function CombineMultipleMP3sTo($FilenameOut, $FilenamesIn) {
	global $IP;

	foreach ($FilenamesIn as $nextinputfilename) {
		if (!is_readable($nextinputfilename)) {
			echo 'Cannot read "'.$nextinputfilename.'"<BR>';
			return false;
		}
	}
	/*
	if (!is_writeable($FilenameOut)) {
		echo 'Cannot write "'.$FilenameOut.'"<BR>';
		return false;
	}
*/
	
	require_once ($IP."/extensions/Loop/getid3/getid3.php");
	ob_start();
	if ($fp_output = fopen($FilenameOut, 'wb')) {

		ob_end_clean();
		// Initialize getID3 engine
		$getID3 = new getID3;
		foreach ($FilenamesIn as $nextinputfilename) {

			$CurrentFileInfo = $getID3->analyze($nextinputfilename);
			if ($CurrentFileInfo['fileformat'] == 'mp3') {

				ob_start();
				if ($fp_source = fopen($nextinputfilename, 'rb')) {

					ob_end_clean();
					$CurrentOutputPosition = ftell($fp_output);

					// copy audio data from first file
					fseek($fp_source, $CurrentFileInfo['avdataoffset'], SEEK_SET);
					while (!feof($fp_source) && (ftell($fp_source) < $CurrentFileInfo['avdataend'])) {
						fwrite($fp_output, fread($fp_source, 32768));
					}
					fclose($fp_source);

					// trim post-audio data (if any) copied from first file that we don't need or want
					$EndOfFileOffset = $CurrentOutputPosition + ($CurrentFileInfo['avdataend'] - $CurrentFileInfo['avdataoffset']);
					fseek($fp_output, $EndOfFileOffset, SEEK_SET);
					ftruncate($fp_output, $EndOfFileOffset);

				} else {

					$errormessage = ob_get_contents();
					ob_end_clean();
					echo 'failed to open '.$nextinputfilename.' for reading';
					fclose($fp_output);
					return false;

				}

			} else {

				echo $nextinputfilename.' is not MP3 format';
				fclose($fp_output);
				return false;

			}

		}

	} else {

		$errormessage = ob_get_contents();
		ob_end_clean();
		echo 'failed to open '.$FilenameOut.' for writing';
		return false;

	}

	fclose($fp_output);
	return true;
}
	

	function smartReadFile($location, $filename, $mimeType='application/octet-stream')
	{ if(!file_exists($location))
	{ header ("HTTP/1.0 404 Not Found");
	return;
	}
	 
	$size=filesize($location);
	$time=date('r',filemtime($location));
	 
	$fm=@fopen($location,'rb');
	if(!$fm)
	{ header ("HTTP/1.0 505 Internal server error");
	return;
	}
	 
	$begin=0;
	$end=$size;
	 
	if(isset($_SERVER['HTTP_RANGE']))
	{ if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
	{ $begin=intval($matches[0]);
	if(!empty($matches[1]))
	$end=intval($matches[1]);
	}
	}
	 
	if($begin>0||$end<$size)
	header('HTTP/1.0 206 Partial Content');
	else
	header('HTTP/1.0 200 OK');

	header("Content-Type: $mimeType");
	header('Cache-Control: public, must-revalidate, max-age=0');
	header('Pragma: no-cache');
	header('Accept-Ranges: bytes');
	header('Content-Length:'.($end-$begin));
	header("Content-Range: bytes $begin-$end/$size");
	header("Content-Disposition: inline; filename=$filename");
	header("Content-Transfer-Encoding: binary\n");
	header("Last-Modified: $time");
	header('Connection: close');

	$cur=$begin;
	fseek($fm,$begin,0);

	while(!feof($fm)&&$cur<$end&&(connection_status()==0))
	{ print fread($fm,min(1024*16,$end-$cur));
	$cur+=1024*16;
	}
	}

	function get_loop_xml($tmp_name,$chapter) {
		global $IP, $xmlg, $content_provider, $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $xml, $wgSitename, $wgServer,$wgLanguageCode ;

		$content_provider = new ContentProviderMySQL ;
		$converter = new MediaWikiConverter ;
		$xml = "" ;

		$aArticles = $this->get_articles($chapter) ;
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

		//$qrFile = $IP."/tmp/".$tmp_name."_qr.png";
		$qrFile = sys_get_temp_dir().'/'.$tmp_name."_qr.png";
		QRcode::png($wgServer, $qrFile);

		$xmlresult="<?xml version='1.0' encoding='UTF-8' ?>\n";
		$xmlresult.="<articles ";
		$xmlresult.='title="'.$this->get_structure_title().'" ';
		$xmlresult.='url="'.$wgServer.'" ';
		$xmlresult.='date="'.date('d.m.Y').'" ';
		$xmlresult.='lang="'.$lang.'" ';
		$xmlresult.='qrimage="'.$qrFile.'" ';
		$xmlresult.=">" ;
		$tocxml=$this->get_toc();
		$xmlresult.="<toc>".$tocxml."</toc>";
		$xmlresult.=$xml;
		$xmlresult.="</articles>" ;

		return $xmlresult;

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









	function get_articles($chapter='1') {

		$pages=array();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->query("select * from loopstructure as ls where SUBSTR(ls.TocNumber,1,1) = '".$chapter."' order by Sequence");

		foreach ( $res as $row ) {
			$tt=$row->TocText;
			$pages[]=$tt;
		}
		return $pages;
	}

	function get_num_chapters() {

		$pages=array();
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->query("select * from loopstructure as ls where TocLevel=1 order by Sequence DESC");
		$first=true;
		$max=0;
		foreach ( $res as $row ) {
			if ($first==true) {
				$max=$row->TocNumber;
				$first=false;
			}
		}
		return $max;
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
				$row=mysql_fetch_assoc($res->result);
				return $row["TocText"];
	}

	function get_chapter_title($chapter) {
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
				'TocNumber' => $chapter
				),
				__METHOD__,
				array(
				)
				);
				$row=mysql_fetch_assoc($res->result);
				return $row["TocText"];
	}

}
?>