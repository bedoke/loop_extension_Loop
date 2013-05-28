<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopTasks extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopTasks' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgUser, $wgParserConf, $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgStylePath;

		//var_dump($wgParserConf);

		$parser = new Parser( $wgParserConf );
		//$parser->disableCache();

		$parserOptions = ParserOptions::newFromUser( $wgUser );

		$parser->Options($parserOptions);

		//var_dump($parser);

		$wgOut->addModules( 'ext.LoopTasks' );
		$this->setHeaders();

		$specialtasks=array();
		$return = '<h1>'.wfMsg('looptasks').'</h1>';


		$dbr = wfGetDB( DB_SLAVE );
		/*
		$dbResult = $dbr->select(
		array( 'page', 'text' ),
		array( 'page_id', 'page_namespace', 'page_title', 'old_id', 'old_text' ),
		array(
		0 => "old_text LIKE '%loop_task%'"
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
		0 => "old_text LIKE '%loop_task%'",
		1 => "page.page_latest=revision.rev_id",
		2 => "revision.rev_text_id=text.old_id"
		),
		__METHOD__,
		array(),
		array()
		);		
		
		
		//	var_dump($dbResult);
		// wfDebug( __METHOD__ . ': name : '.print_r($dbResult,true));	

		foreach ( $dbResult as $row ) {
			//var_dump($row);
			// wfDebug( __METHOD__ . ': name : '.print_r($row,true));
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

  
  //wfDebug( __METHOD__ . ': name : '.print_r($r_old_text,true));	
  
			$matches=array();
			$parser->extractTagsAndParams( array('loop_task') , $r_old_text, $matches);
			//var_dump($matches);
			//wfDebug( __METHOD__ . ': name : '.print_r($matches,true));	
			foreach ($matches as $match) {
				//var_dump($match);
				//wfDebug( __METHOD__ . ': match : '.print_r($match,true));
				$specialtask=new LoopTask($match[1],$match[2],$parser);
				//wfDebug( __METHOD__ . ': specialtask : '.print_r($specialtask,true));
				$specialtask->setPageTitle($page_title);
				$specialtask->setPageURL($page_url);

				$specialtask->setStructureTitle($structure_title);
				$specialtask->setStructureURL($structure_url);
				$specialtask->setStructureSequence($structure_sequence);
				$specialtask->setStructureIndex($structure_index);
				$specialtask->setStructureIndexOrder($structure_index_order);
				$specialtask->setPageTocNumber($page_toc_number);

				 //var_dump($specialtask);
				 wfDebug( __METHOD__ . ': specialtask : '.print_r($specialtask,true));
				
				$specialtasks[]=$specialtask;
			}

		}

		wfDebug( __METHOD__ . ': specialtasks : '.print_r($specialtasks,true));	

		usort($specialtasks, array('SpecialLoopTasks','loop_task_index_sort'));

		$akt_structure_title='';
		$return.='<div class="loop_task_index">';
		$return.='<table >';
		foreach ($specialtasks as $task) {
				
			if ($task->index) {
					
				if ($task->structureTitle!=$akt_structure_title) {
					$return.='<tr><th colspan="2"><a href="'.$task->structureURL.'">';
					if (($wgLoopStructureNumbering) && ($wgLoopStructureUseTopLevel)){
						$return.=$task->structureIndexOrder.' ';
					}
					$return.=$task->structureTitle.'</a></th></tr>';
					$akt_structure_title=$task->structureTitle;
				}
				$return.='<tr>';
				$return.='<td class="loop_task_index_thumb">';
					//$return.='<img src="'.$wgStylePath .'/loop/images/media/type_task.png">';
					$return.='<div class="mediabox_typeicon_task"></div>';
				$return.='</td>';
				$return.='<td>';
				$return.='<div class="loop_task_index_title">'.$task->title.'</div>';
				$return.='<div class="loop_task_index_description">'.$task->description.'</div>';
				$return.='<div class="loop_task_index_link"><a href="'.$task->pageURL;
				if ($task->title) {
					$return.='#'.htmlentities(str_replace( ' ', '_', trim($task->title) ),ENT_QUOTES, "UTF-8");
				}
				$return.='">';				
				
				if ($wgLoopStructureNumbering) {
					$return.= $task->pageTocNumber.' ';
				}
				$return.= $task->pageTitle.'</a></div>';
				$return.='</td>';
				$return.='</tr>';
			}
		}
		$return.='</table></div>';


		$wgOut->addHTML($return);

	}

	public	function loop_task_index_sort ($a,$b) {
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