<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopFormulas extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopFormulas' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgStylePath;

		//var_dump($wgParserConf);

		$parser = new Parser( $wgParserConf );
		//$parser->disableCache();

		$parserOptions = ParserOptions::newFromUser( $wgUser );

		$parser->Options($parserOptions);

		//var_dump($parser);

		$wgOut->addModules( 'ext.LoopFormulas' );
		$this->setHeaders();

		$specialformulas=array();
		$return = '<h1>'.wfMsg('loopformulas').'</h1>';


		$dbr = wfGetDB( DB_SLAVE );
		/*
		$dbResult = $dbr->select(
		array( 'page', 'text' ),
		array( 'page_id', 'page_namespace', 'page_title', 'old_id', 'old_text' ),
		array(
		0 => "old_text LIKE '%loop_formula%'"
		),
		__METHOD__,
		array(),
		array( 'text' =>
		array( 'LEFT JOIN', 'page_latest=old_id' )
		)
		);
		*/
		
		$dbResult = $dbr->select(
		array( 'page', 'revision', 'text' ),
		array( 'page.page_id', 'page.page_namespace', 'page.page_title', 'text.old_text' ),
		array(
		0 => "old_text LIKE '%loop_formula%'",
		1 => "page.page_latest=revision.rev_id",
		2 => "revision.rev_text_id=text.old_id"
		),
		__METHOD__,
		array(),
		array()
		);		
		
		
		//	var_dump($dbResult);


		foreach ( $dbResult as $row ) {
			//var_dump($row);
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

			//var_dump($r_page_id);
			
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
  $r_old_text =$result;				
			$matches=array();
			$parser->extractTagsAndParams( array('loop_formula') , $r_old_text, $matches);
			//var_dump($matches);
			foreach ($matches as $match) {
				//var_dump($match);
				$specialformula=new LoopFormula($match[1],$match[2]);
				$specialformula->setPageTitle($page_title);
				$specialformula->setPageURL($page_url);

				$specialformula->setStructureTitle($structure_title);
				$specialformula->setStructureURL($structure_url);
				$specialformula->setStructureSequence($structure_sequence);
				$specialformula->setStructureIndex($structure_index);
				$specialformula->setStructureIndexOrder($structure_index_order);
				$specialformula->setPageTocNumber($page_toc_number);

				// var_dump($specialformula);
				$specialformulas[]=$specialformula;
			}

		}



		usort($specialformulas, array('SpecialLoopFormulas','loop_formula_index_sort'));

		$akt_structure_title='';
		$return.='<div class="loop_formula_index">';
		$return.='<table >';
		foreach ($specialformulas as $formula) {
				
			if ($formula->index) {
					
				if ($formula->structureTitle!=$akt_structure_title) {
					$return.='<tr><th colspan="2"><a href="'.$formula->structureURL.'">';
					if (($wgLoopStructureNumbering) && ($wgLoopStructureUseTopLevel)){
						$return.=$formula->structureIndexOrder.' ';
					}
					$return.=$formula->structureTitle.'</a></th></tr>';
					$akt_structure_title=$formula->structureTitle;
				}
				$return.='<tr>';
				$return.='<td class="loop_formula_index_thumb">';
					//$return.='<img src="'.$wgStylePath .'/loop/images/media/type_formula.png">';
					$return.='<div class="mediabox_typeicon_formula"></div>';
				$return.='</td>';
				$return.='<td>';
				$return.='<div class="loop_formula_index_title">'.$formula->title.'</div>';
				$return.='<div class="loop_formula_index_description">'.$formula->description.'</div>';
				$return.='<div class="loop_formula_index_link"><a href="'.$formula->pageURL;
				if ($formula->title) {
					$return.='#'.htmlentities(str_replace( ' ', '_', trim($formula->title) ),ENT_QUOTES, "UTF-8");
				}
				$return.='">';				
				
				if ($wgLoopStructureNumbering) {
					$return.= $formula->pageTocNumber.' ';
				}
				$return.= $formula->pageTitle.'</a></div>';
				$return.='</td>';
				$return.='</tr>';
			}
		}
		$return.='</table></div>';


		$wgOut->addHTML($return);

	}

	public	function loop_formula_index_sort ($a,$b) {
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