<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopFigures extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopFigures' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel;

		//var_dump($wgParserConf);

		$parser = new Parser( $wgParserConf );
		//$parser->disableCache();

		$parserOptions = ParserOptions::newFromUser( $wgUser );

		$parser->Options($parserOptions);

		//var_dump($parser);

		$wgOut->addModules( 'ext.LoopFigures' );
		$this->setHeaders();

		$specialfigures=array();
		$return = '<h1>'.wfMsg('loopfigures').'</h1>';

		$dbr = wfGetDB( DB_SLAVE );
		$dbResult = $dbr->select(
		array( 'page', 'revision', 'text' ),
		array( 'page.page_id', 'page.page_namespace', 'page.page_title', 'text.old_text' ),
		array(
		0 => "old_text LIKE '%loop_figure%'",
		1 => "page.page_latest=revision.rev_id",
		2 => "revision.rev_text_id=text.old_id"
		),
		__METHOD__,
		array(),
		array()
		);
		//var_dump($dbResult);
		

		foreach ( $dbResult as $row ) {
			//var_dump($row);
			//wfDebug( __METHOD__ . ': row : '.print_r($row,true)."\n");	
			
			$r_page_id=$row->page_id;
			$r_page_namespace=$row->page_namespace;
			$r_page_title=$row->page_title;
			//$r_old_id=$row->old_id;
			$r_old_text=$row->old_text;

			$tempArticle= Article::newFromID($r_page_id);
			$tempTitle=($tempArticle->getTitle());
			$page_url=$tempTitle->getFullURL();
			$page_title=$tempTitle->mTextform;

			$structure_title='';
			$structure_url='';
			$structure_sequence=0;
			$structure_index=0;
			$structure_index_order=0;
			$page_toc_number=0;

			$item = LoopStructureItem::newFromArticleId($r_page_id);
			if (!empty($item)) {
				$indexID=$item->mIndexArticleId;
				$structure_sequence=$item->mSequence;
				$structure_index=$item->mIndexArticleId;
				$structure_index_order=$item->mIndexOrder;
				$page_toc_number=$item->mTocNumber;

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
				'IndexArticleId' => $indexID,
				'TocLevel' => 0
				),
				__METHOD__,
				array(
				'ORDER BY' => 'Sequence ASC'
				)
				);
				//var_dump($res);

				$row = $dbr->fetchRow( $res );
				if ($row) {
					$tempArticle= Article::newFromID($row["ArticleId"]);
					$tempTitle=($tempArticle->getTitle());
					$structure_url=$tempTitle->getFullURL();
					$structure_title=$tempTitle->mTextform;

				}
			}
			// nowiki-Abschnitte entfernen
			$pattern = "/(\r\n|\r|\n)/";
			$replacement = PHP_EOL;
			$string = preg_replace($pattern, $replacement, $r_old_text);
			
			$pattern = '@(<nowiki>)(.*?)(<\/nowiki>)@isu';
			$replace = '';
			$result = preg_replace($pattern, $replace, $string);
			
			$pattern = '@(<!--)(.*?)(-->)@isu';
			$replace = '';
			$result = preg_replace($pattern, $replace, $result);
			
			
			$r_old_text =$result;

			$matches=array();
			$parser->extractTagsAndParams( array('loop_figure') , $r_old_text, $matches);
			//var_dump($matches);
			
			$title=Title::newFromText('figure');
			
			//wfDebug( __METHOD__ . ': name : '.print_r($matches,true));	
			
			foreach ($matches as $match) {
				//var_dump($match);
				if ($match[0]=='loop_figure') {
				$specialfigure=new LoopFigure($match[1],$match[2],$title);
				$specialfigure->setPageTitle($page_title);
				$specialfigure->setPageURL($page_url);

				$specialfigure->setStructureTitle($structure_title);
				$specialfigure->setStructureURL($structure_url);
				$specialfigure->setStructureSequence($structure_sequence);
				$specialfigure->setStructureIndex($structure_index);
				$specialfigure->setStructureIndexOrder($structure_index_order);
				$specialfigure->setPageTocNumber($page_toc_number);

				// var_dump($specialfigure);
				$specialfigures[]=$specialfigure;
				}				
			}
				
				
		}
		
		// wfDebug( __METHOD__ . ': specialfigures : '.print_r($specialfigures,true)."\n");
		
		usort($specialfigures, array('SpecialLoopFigures','loop_figure_index_sort'));

		$akt_structure_title='';
		$return.='<div class="loop_figure_index">';
		$return.='<table >';
		foreach ($specialfigures as $figure) {
				
			if ($figure->index) {
					
				if ($figure->structureTitle!=$akt_structure_title) {
					$return.='<tr><th colspan="2"><a href="'.$figure->structureURL.'">';
					if (($wgLoopStructureNumbering) && ($wgLoopStructureUseTopLevel)){
						$return.=$figure->structureIndexOrder.' ';
					}
					$return.=$figure->structureTitle.'</a></th></tr>';
					$akt_structure_title=$figure->structureTitle;
				}
				$return.='<tr>';
				$return.='<td class="loop_figure_index_thumb">';
				if ($figure->file) {
					wfDebug( __METHOD__ . ': figure file : '.print_r($figure->file,true)."\n");
					$file = wfLocalFile($figure->file);
					wfDebug( __METHOD__ . ': file : '.print_r($file,true)."\n");
					$thumb = $file->transform( array( 'width' => 100, 'height' => 100 ) );
					$return.= $thumb->toHtml( array( 'desc-link' => true ) );
				}	else {
					$return.='&nbsp;';
				}
				$return.='</td>';
				$return.='<td>';
				$return.='<div class="loop_figure_index_title">'.$figure->title.'</div>';
				$return.='<div class="loop_figure_index_description">'.$figure->description.'</div>';
				$return.='<div class="loop_figure_index_link"><a href="'.$figure->pageURL;
				if ($figure->title) {
					$return.='#'.htmlentities(str_replace( ' ', '_', trim($figure->title) ),ENT_QUOTES, "UTF-8");
				}
				$return.='">';
				if ($wgLoopStructureNumbering) {
					$return.= $figure->pageTocNumber.' ';
				}
				$return.= $figure->pageTitle.'</a></div>';
				$return.='</td>';
				$return.='</tr>';
			}
		}
		$return.='</table></div>';


		$wgOut->addHTML($return);

	}
		
	public	function loop_figure_index_sort ($a,$b) {
		$return=0;

		$a_lo = $a->structureIndex;
		$a_p = $a->structureSequence;
		$a_io = $a->structureIndexOrder;
		$b_lo = $b->structureIndex;
		$b_p = $b->structureSequence;
		$b_io = $b->structureIndexOrder;

		if ($a_io > $b_io) {
			$return = +1;
		} else if ($a_io < $b_io) {
			$return = -1;
		} else {
			if ($a_lo > $b_lo) {
				$return = +1;
			} else if ($a_lo < $b_lo) {
				$return = -1;
			} else {
				if ($a_p > $b_p) {
					$return = +1;
				} else if ($a_p < $b_p) {
					$return = -1;
				} else {
					$return=0;
				}
			}
		}




		return $return;

	}


}

?>