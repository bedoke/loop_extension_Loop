<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

global $IP;

require_once ($IP."/extensions/Loop/SpecialLoopPrintversion.php");

class SpecialLoopAudio extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopAudio' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgText2Speech;

		$this->setHeaders();

		$indexitems=array();
		//$return = '<h1>'.wfMsg('looptoc').'</h1>';
		$return = '<h1>Audio</h1>';
		if ($wgText2Speech === true) {
			$return.=$this->get_toc();	
		} else {
			$return.= wfMsg('loop_no_text2speech_notice');	
		}
		
		

		$wgOut->addHTML($return);
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
				'TocLevel' => '1',
				),
				__METHOD__,
				array(
				'ORDER BY' => 'Sequence'
				)
				);
				$tree='';
				$tree.='<table>';
				foreach ( $res as $row ) {
					$ta=$row->ArticleId;
					$ti=$row->IndexArticleId;
					$tl=$row->TocLevel;
					$tn=$row->TocNumber;

					$tempArticle= Article::newFromID($ta);
					if ($tempArticle) {
						$tempTitle=($tempArticle->getTitle());

						if ($tempTitle) {
								
							$t=$tempTitle->mTextform;
								
							$tree.='<tr>';
							if ($wgLoopStructureNumbering) {
								$tree.='<td>'.$tn.'</td>';
							}
							$tree.= '<td>'.$tempTitle->mTextform.'</td>';
							$tree.='<td><a href="/loop/Special:LoopChapteraudio?chapter='.$tn.'"><div class="audio_download_icon">MP3</div></a></td>';
							$tree.='<td><a href="/loop/Special:LoopChapteraudio?chapter='.$tn.'&type=m4b"><div class="audio_download_icon">M4B</div></a></td>';
							$tree.='<td><a href="/loop/Special:LoopChapteraudio?chapter='.$tn.'&text=true"><div class="audio_download_icon">Text</div></a></td></tr>';

						}
					}

				}
				$tree.='</table>';
				$return.=$tree;

				return $return;
	}	
	
}
               
?>