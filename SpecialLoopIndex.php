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

		$indexitems=array();
		$return = '<h1>'.wfMsg('loopindex').'</h1>';

		$sql="select indexes.in_title, page.page_id, page.page_title, loopstructure.TocNumber from (indexes left join page on indexes.in_from=page.page_id) left join loopstructure on page.page_id= loopstructure.ArticleId  order by LOWER(indexes.in_title), loopstructure.TocNumber";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->query( $sql, __METHOD__, true );

		$act_index_title='';
		$act_index_letter='';
		$return.='<div id="loopindex"><table>';
		$first=true;
		$closed=false;
		foreach ( $res as $row ) {
			if (substr($row->in_title,0,1)!=$act_index_letter) {
				$act_index_letter=substr($row->in_title,0,1);
				if ((!$first)&&(!closed)) {
					$return.= '</td></tr>';
					$closed=true;
				}				
				
				$return.= '<tr><td><div class="loopindex_letter">'.$act_index_letter.'</div></td><td>&nbsp;</td></tr>';
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