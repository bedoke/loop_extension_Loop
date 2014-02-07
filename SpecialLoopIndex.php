<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopIndex extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopIndex' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgLoopStructureNumbering;

		$wgOut->addModules( 'ext.LoopIndex' );
		$this->setHeaders();

		setlocale(LC_CTYPE, 'cs_CZ');
		
		$indexitems=array();
		$return = '<h1>'.wfMsg('loopindex').'</h1>';

		$sql="select indexes.in_title, page.page_id, page.page_title, loopstructure.TocNumber from (indexes left join page on indexes.in_from=page.page_id) left join loopstructure on page.page_id= loopstructure.ArticleId  order by LOWER(indexes.in_title), loopstructure.TocNumber";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->query( $sql, __METHOD__, true );
		
		$act_index_title='';
		$act_index_letter='';		
		$letters=array();
		foreach ( $res as $row ) {
			$act_letter = mb_substr($row->in_title,0,1);
			$act_letter = iconv('UTF-8', 'US-ASCII//TRANSLIT', $act_letter);
			if ((ord($act_letter)>=48) && (ord($act_letter)<=57)) {
				$act_letter = '#';
			}
			if ($act_letter!=$act_index_letter) {
				$act_index_letter=$act_letter;
				$letters[] = $act_letter;
			}
		}
		$allletters=array('#','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		
		$return.= '<table><tr>';
		foreach ( $allletters as $letter ) {
			if (in_array($letter,$letters)) {
				$return.='<td><a href="#indexletter_'.ord($letter).'">'.$letter.'</a></td>';
			} else {
				$return.='<td>'.$letter.'</td>';
			}
		}
		$return.= '</tr></table>';
		
		$act_index_title='';
		$act_index_letter='';
		$return.='<div id="loopindex"><table>';
		$first=true;
		$closed=false;
		foreach ( $res as $row ) {
			$act_letter = mb_substr($row->in_title,0,1);
			$act_letter = iconv('UTF-8', 'US-ASCII//TRANSLIT', $act_letter);		
			if ((ord($act_letter)>=48) && (ord($act_letter)<=57)) {
				$act_letter = '#';
			}
			if ($act_letter!=$act_index_letter) {
				$act_index_letter=$act_letter;
				if ((!$first)&&(!closed)) {
					$return.= '</td></tr>';
					$closed=true;
				}				
				
				$return.= '<tr><td><div class="loopindex_letter" id="indexletter_'.ord($act_index_letter).'">'.$act_index_letter.'</div></td><td>&nbsp;</td></tr>';
			}
			
			//var_dump($row);
			if ($row->in_title!=$act_index_title) {
				$act_index_title=$row->in_title;
				if ((!$first)&&(!closed)) {
					$return.= '</td></tr>';
				}
				$closed=false;
				
				//$act_index_title = str_replace( '_', ' ', Sanitizer::decodeCharReferencesAndNormalize( $act_index_title ) );
				
				$act_index_title_print = Sanitizer::decodeCharReferencesAndNormalize( str_replace( '_', ' ', $act_index_title ) );
				
				$return.= '<tr><td class="loopindex_title">'.$act_index_title_print.'</td><td class="loopindex_pages">';
				$link_title='';
				if (($wgLoopStructureNumbering) && ($row->TocNumber)) {
					$link_title.=$row->TocNumber.' ';
				}
				$link_title.=$row->page_title;
				$link = Linker::link(Title::newFromText($row->page_title),$link_title);
				$return.= $link;

			} else {
				$return.= ', ';
				$link_title='';
				if (($wgLoopStructureNumbering) && ($row->TocNumber)) {
					$link_title.=$row->TocNumber.' ';
				}
				$link_title.=$row->page_title;
				$link = Linker::link(Title::newFromText($row->page_title),$link_title);
				$return.= $link;
				
			}
		}
		if (!$first) {
			$return.='</td></tr>';
		}
		$return.='</table></div>';


		$wgOut->addHTML($return);
	}
}
//  $s = $skin->makeLinkObj(Title::newFromText(wfMsgForContent('pagecategorieslink')), $msg)
               
?>