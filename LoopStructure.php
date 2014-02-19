<?php
#----------------------------------------------------------------------------
#    LoopStructure class for extension
#----------------------------------------------------------------------------

class LoopStructure {

	function LoopStructure() {
	}

	function Render($input, $title, $options, $indexorder) {
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel;

		$localParser = new Parser();
		$output = $localParser->parse($input, $title, $options);
		$html_text = $output->getText();
		$offset = 0;
		
		// find root page
		$pattern = '@<p>(<a href=.*?</a>).*?</p>@s';
		if (!preg_match($pattern, $html_text, $matches, PREG_OFFSET_CAPTURE, $offset)) return $html_text;
		$root_page_link = $matches[1][0];		
		
		$matches=array();
		
		$l=mb_split ( '\n' , $html_text);
		$t='';
		$ist=false;
		foreach ($l as $line) {

			if ($ist && (stristr ( $line , '</div>' ))) {
				$ist=false;

			}				
			if ($ist) {
				$t.=$line;
				}
			
			if (stristr ( $line , '<div id="toc" class="toc">' )) {
				$ist=true;

			}
			
			
		}
		$toc=$t;
		
		// change TOC links
		$error=false;
		$pattern = '@<li class="toclevel-(.*?)"><a href="(#.*?)"><span class="tocnumber">(.*?)</span> <span class="toctext">(.*?)</span></a>@';
		do {
			$topic_found = preg_match($pattern, $toc, $matches, PREG_OFFSET_CAPTURE);
			if ($topic_found) {

				$item_text = $matches[4][0];
				$title = Title::newFromText($item_text);
				if ($title) {
					$page_url = $title->escapeLocalURL();
					$url_position = $matches[2][1];
					$url_length = strlen($matches[2][0]);
					
					$toc = substr_replace($toc, $page_url, $url_position, $url_length);
					
					
				} else {
					$toc = 'error: '.$matches[2][0];
					$error=true;
				}
			}
		} while ($topic_found && !$error);		

		$toc = '<h2>'.$root_page_link.'</h2>'.$toc;
		return $toc;
		

	}

	function Save($text, $article, $user, $indexorder=0) {
		$article_id = $article->getID();
		if ($article_id) {  // Verify that the page has been saved at least once
			$this->EraseInformation($article_id);
			$parsed_text = $this->Render($text, $article->mTitle, new ParserOptions($user), $indexorder);
			$this->SaveIndex($parsed_text, $article_id, $indexorder);
		}
	}

	// Erases information about this hierarchy in the database.
	function EraseInformation($index_article_id) {
		$fname = 'LoopStructure::EraseInformation';
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->delete('loopstructure',
		array(
                'IndexArticleId' => $index_article_id,
		), $fname
		);
	}

	function SaveIndex($text, $index_article_id, $indexorder=0) {
		// get hierarchy root
		
		wfDebug( __METHOD__ . ': savetext : '.print_r($text,true));
		
		$offset = 0;
		$pattern = '@(<h2><a href=")(.*?)(" title=")(.*?)(">)(.*?)(</a></h2>)@is';
		if (!preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) return;
		$root_title = $matches[4][0];
		$title = Title::newFromText($root_title);
		$root_article_id = $title->getArticleID();
		$max_level = 0;
		$parent_id[0] = $root_article_id;
		$sequence = 0;
		// add root item to the database
		$loopstructureItem = new LoopStructureItem();
		$loopstructureItem->mIndexArticleId = $index_article_id;
		$loopstructureItem->mTocLevel = 0;
		$loopstructureItem->mTocNumber = "";
		$loopstructureItem->mTocText = $root_title;
		$loopstructureItem->mSequence = $sequence++;
		$loopstructureItem->mArticleId = $root_article_id;
		$loopstructureItem->mPreviousArticleId = 0;
		$loopstructureItem->mNextArticleId = 0;
		$loopstructureItem->mParentArticleId = 0;

		$loopstructureItem->mIndexOrder = $indexorder;

		$loopstructureItem->deleteArticleId();  // remove article from any other hierarchy
		
		
		$loopstructureItem->addToDatabase();  // add article to this hierarchy
		$previousLoopStructureItem = $loopstructureItem;
		// process items
		while (true) {  // The function will return when a pattern match fails
			// find TOC level as integer
			// changed by oc $pattern = '@<li class=\"toclevel-(.*?)\">@';
			$pattern = '@<li class=\"toclevel-(.*?) tocsection-.*\">@';

			if (!preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) return;
			$toclevel = $matches[1][0];
			$offset = $matches[0][1];
			// find TOC number as string
			$pattern = '@<span class=\"tocnumber\">(.*?)</span>@';
			if (!preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) return;
			$TOC_number = $matches[1][0];
			$offset = $matches[0][1];
			// find TOC text as Unicode string
			$pattern = '@<span class=\"toctext\">(.*?)</span>@';
			if (!preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) return;
			$TOC_text = $matches[1][0];
			$offset = $matches[0][1];
			// article title
			$title = Title::newFromText($TOC_text);
			$current_article_id = $title->getArticleID();
			// parent article
			$parent_id[$toclevel] = $current_article_id;
			if ($toclevel > $max_level) $max_level = $toclevel;
			for ($i = $toclevel + 1; $i <= $max_level; $i++) {
				$parent_id[$i] = 0;  // clear lower levels to prevent using an old value in case some intermediary levels are omitted
			}
			$parentArticleId = $parent_id[$toclevel - 1];
			$parentArticleId = intval($parentArticleId);
			// add item to the database
			$loopstructureItem = new LoopStructureItem();
			$loopstructureItem->mIndexArticleId = $index_article_id;
			$loopstructureItem->mTocLevel = $toclevel;
			$loopstructureItem->mTocNumber = $TOC_number;
			$loopstructureItem->mTocText = $TOC_text;
			$loopstructureItem->mSequence = $sequence++;
			$loopstructureItem->mArticleId = $current_article_id;
			$loopstructureItem->mPreviousArticleId = $previousLoopStructureItem->mArticleId;
			$loopstructureItem->mNextArticleId = 0;
			$loopstructureItem->mParentArticleId = $parentArticleId;

			$loopstructureItem->mIndexOrder = $indexorder;

			$loopstructureItem->deleteArticleId();  // remove article from any other hierarchy
			$loopstructureItem->addToDatabase();  // add article to this hierarchy
			
			
			
			// update previous article
			$previousLoopStructureItem->mNextArticleId = $current_article_id;
			$previousLoopStructureItem->updateNextArticleId();
			$previousLoopStructureItem = $loopstructureItem;
		}
	}


function renderToc ($args) {
	
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel;
		
		if ($args['level']){
			$level=intval($args['level']);
		} else {
			$level=1;
		}		
		
		$return='';
		$article_id=($GLOBALS["wgTitle"]->mArticleID);
		$item = LoopStructureItem::newFromArticleId($article_id);
		if (!empty($item)) {
			$indexID=$item->mIndexArticleId;
			$parentID=$item->mParentArticleId;
			$aktTocLevel=$item->mTocLevel;
			$aktTocNumber=$item->mTocNumber;
			$aktTocNumber= $aktTocNumber.'.';
			
			$cond = '(';
			for ($i=0;$i<$level;$i++) {
				if($i>0) {$cond .= ' OR ';}
				$cond.='(TocLevel = '.intval($aktTocLevel+$i+1).')';
			}			
			$cond .= ')';			
			$condition = '(IndexArticleId = '.$indexID.') AND (TocNumber like "'.$aktTocNumber.'%") AND '.$cond;
			
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
				$condition
				,
				__METHOD__,
				array(
				'ORDER BY' => 'Sequence ASC'
				)
				);

				$return.='<div id="looptoc"><ul>';

				$tempArticle= Article::newFromID($article_id);
				if ($tempArticle) {
					$tempTitle=($tempArticle->getTitle());
					if ($tempTitle) {
						$tempTitle->getFullURL();
						$tempTitle->mTextform;
						$return.= '<li><a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" >';
						if ($wgLoopStructureNumbering) {
							if (($item->mTocNumber=='')&&($wgLoopStructureUseTopLevel)) {
								$return.= $item->mIndexOrder.' ';
							} else {
								$return.= $item->mTocNumber.' ';
							}
						}
						$return.= $tempTitle->mTextform.'</a></li>';
					}
				}
				$return.='<ul>';
					
				foreach ( $res as $row ) {
					$tid=$row->ArticleId;
					$tempArticle= Article::newFromID($tid);
					if ($tempArticle) {
						$tempTitle=($tempArticle->getTitle());
						if ($tempTitle) {
							$tempTitle->getFullURL();
							$tempTitle->mTextform;
							$return.= '<li><a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" >';
							if ($wgLoopStructureNumbering) {
								$return.= $row->TocNumber.' ';
							}
							$return.= $tempTitle->mTextform.'</a></li>';
						}
					}
				}
				$return.='</ul>';
					
				$return.= '</ul></div>';
		} else {
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
				$return.= '<div  id="looptoc">';
				$tree='';
				$tree.='<ul>';
				foreach ( $res as $row ) {
					$ta=$row->ArticleId;
					if (($wgLoopStructureUseTopLevel)&&($tl==0)) {
						$tn=$row->IndexOrder;
					} else {
						$tn='';
					}

					$tempArticle= Article::newFromID($ta);
					if ($tempArticle) {
						$tempTitle=($tempArticle->getTitle());
							if (strlen($tempTitle->mTextform)>20) {
								$t=substr($tempTitle->mTextform,0,20).'...';
							} else {
								$t=$tempTitle->mTextform;
							}

						$tree.='<li><a href="'.$tempTitle->getFullURL().'"><span class="tocnumber">'.$tn.'</span> <span class="toctext">'.$t.'</span></a></li>';
					}
				}
				$tree.='</ul>';
				$return.=$tree;

				$return.= '</div>';
					
		}

		return $return;

	}
	


	function renderLoopToc () {
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgParser;

		$return='';

		$article_id=($GLOBALS["wgTitle"]->mArticleID);
		$item = LoopStructureItem::newFromArticleId($article_id);
		if (!empty($item)) {
			$return.= '<div  id="sidebarlooptoc">';
			$return.= '<h2>'.wfMsg('looptoc').'</h2>';
			$return.= '<div  id="sidebartoc">';
			$tree='';

			$indexId=$item->mIndexArticleId;
			$tree=LoopStructure::makeTree($indexId,0);
			$return.=$tree;

			$return.= '</div></div>';
		} else {
			// ToDo: Render Toc of LOs
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
				$return.= '<div  id="sidebarlooptoc">';
				$return.= '<h2>'.wfMsg('looptoc').'</h2>';
				$return.= '<div  id="sidebartoclo">';
				$tree='';
				$tree.='<ul>';
				foreach ( $res as $row ) {
					$ta=$row->ArticleId;
					if (($wgLoopStructureUseTopLevel)&&($tl==0)) {
						$tn=$row->IndexOrder;
					} else {
						$tn='';
					}

					$tempArticle= Article::newFromID($ta);
					if ($tempArticle) {
						$tempTitle=($tempArticle->getTitle());
						if ($tempTitle) {
							if (strlen($tempTitle->mTextform)>20) {
								$t=substr($tempTitle->mTextform,0,20).'...';
							} else {
								$t=$tempTitle->mTextform;
							}							
							
							$tree.='<li><a href="'.$tempTitle->getFullURL().'"><span class="tocnumber">'.$tn.'</span> <span class="toctext">'.$t.'</span></a></li>';
						}
					}
				}
				$tree.='</ul>';
				$return.=$tree;

				$return.= '</div></div>';
		}
		return $return;
	}



	function makeTree($index,$parent) {
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgParser;
		$thisid=($GLOBALS["wgTitle"]->mArticleID);
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
					if ($first) {$return.='<ul>';$first=false;}
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
							$return.='<li ';
							$return.= 'id="looptoc-'.$ta.'" ';
							if ($ta==$thisid) {$return.='class="jstree-selected"';}
							
							if (strlen($tempTitle->mTextform)>20) {
								$t=mb_substr($tempTitle->mTextform,0,20).'...';
							} else {
								$t=$tempTitle->mTextform;
							}
							$return.= '><a title="'.$tempTitle->mTextform.'"  href="'.$tempTitle->getFullURL().'"><span class="tocnumber">'.$tn.'</span> <span class="toctext">'.$t.'</span></a>';
							$return.= LoopStructure::makeTree($index,$ta);
							$return.='</li>';
						}
					}
				}
				if (!$first) {$return.='</ul>';}


				return $return;
	}



	function renderLoopTocFull () {
		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgParser;

		$return='';
		$return.='<div  id="speciallooptoc">';
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
				$tree.='<ul>';
				foreach ( $res as $row ) {
					$ta=$row->ArticleId;
					$ti=$row->IndexArticleId;
					if (($wgLoopStructureUseTopLevel)&&($tl==0)) {
						$tn=$row->IndexOrder;
					} else {
						$tn='';
					}

					$tempArticle= Article::newFromID($ta);
					if ($tempArticle) {
						if ($tempTitle) {
							$tempTitle=($tempArticle->getTitle());
							
							if (strlen($tempTitle->mTextform)>20) {
								$t=substr($tempTitle->mTextform,0,20).'...';
							} else {
								$t=$tempTitle->mTextform;
							}							

							$tree.='<li><a href="'.$tempTitle->getFullURL().'"><span class="tocnumber">'.$tn.'</span> <span class="toctext">'.$t.'</span></a>';

							$tree.=LoopStructure::makeTree($ti,$ta);
							$tree.='</li>';
						}
					}

				}
				$tree.='</ul>';
				$return.=$tree;

					
				$return.='</div>';
				return $return;
	}



	function renderLoopTocComplete () {

		global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgParser;
		$article_id=($GLOBALS["wgTitle"]->mArticleID);
		$item = LoopStructureItem::newFromArticleId($article_id);
		if (!empty($item)) {
			$in_index=$item->mIndexArticleId;
		} else {
			$in_index=null;
		}

		$return='';
		$return.= '<div  id="sidebarlooptoc" class="loopblock generated-sidebar">';
		$return.= '<h2>'.wfMsg('looptoc').'</h2>';
		$return.= '<div  id="sidebartoc" class="pBody">';
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
				$tree.='<ul>';
				foreach ( $res as $row ) {
					$ta=$row->ArticleId;
					$ti=$row->IndexArticleId;
					if (($wgLoopStructureUseTopLevel)&&($tl==0)) {
						$tn=$row->IndexOrder;
					} else {
						$tn='';
					}

					$tempArticle= Article::newFromID($ta);
					if ($tempArticle) {
						$tempTitle=($tempArticle->getTitle());

						if ($tempTitle) {
							
							if (strlen($tempTitle->mTextform)>30) {
								$t=substr($tempTitle->mTextform,0,30).'...';
							} else {
								$t=$tempTitle->mTextform;
							}							
							
							$tree.='<li ';
							$tree.= 'id="looptoc-'.$ta.'" ';
							if ($ti==$in_index) {
								$tree.= ' class="jstree-open" ' ;
							} else {
								$tree.= ' class="jstree-closed" ' ;
							}
							$tree.= '><a name="'.$tempTitle->mTextform.'" href="'.$tempTitle->getFullURL().'"><span class="tocnumber">'.$tn.'</span> <span class="toctext">'.$t.'</span></a>';

							$tree.=LoopStructure::makeTree($ti,$ta);
						}
					}
					$tree.='</li>';

				}
				$tree.='</ul>';
				$return.=$tree;

					
				$return.='</div></div>';
				return $return;
					

	}


}

#----------------------------------------------------------------------------
#    LoopStructureItem class for extension
#----------------------------------------------------------------------------

class LoopStructureItem {

	var $mId;
	var $mIndexArticleId;
	var $mTocLevel;
	var $mTocNumber;
	var $mTocText;
	var $mSequence;
	var $mArticleId;
	var $mPreviousArticleId;
	var $mNextArticleId;
	var $mParentArticleId;
	var $mIndexOrder;

	function LoopStructureItem() {
	}

	static function newFromArticleId($article_id) {
		$article_id = intval($article_id);
		$fname = 'LoopStructureItem::newFromID';
		$dbr =& wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow(
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
            	array( 'ArticleId' => $article_id ),
            	$fname
            	);

            	if ( $row !== false ) {
            		$item = new LoopStructureItem();
            		$item->mId = $row->Id;
            		$item->mIndexArticleId = $row->IndexArticleId;
            		$item->mTocLevel = $row->TocLevel;
            		$item->mTocNumber = $row->TocNumber;
            		$item->mTocText = $row->TocText;
            		$item->mSequence = $row->Sequence;
            		$item->mArticleId = $row->ArticleId;
            		$item->mPreviousArticleId = $row->PreviousArticleId;
            		$item->mNextArticleId = $row->NextArticleId;
            		$item->mParentArticleId = $row->ParentArticleId;
            		$item->mIndexOrder = $row->IndexOrder;
            	} else {
            		$item = NULL;
            	}
            	return $item;
	}

	/**
	 * Add object to the database
	 */
	function addToDatabase() {
		if ($this->mArticleId!=0) {
		$fname = 'LoopStructureItem::addToDatabase';
		$dbw =& wfGetDB( DB_MASTER );
		$this->mId = $dbw->nextSequenceValue( 'LoopStructureItem_id_seq' );
		$dbw->insert( 'loopstructure',
		array(
                'Id' => $this->mId,
                'IndexArticleId' => $this->mIndexArticleId,
                'TocLevel' => $this->mTocLevel,
                'TocNumber' => $this->mTocNumber,
                'TocText' => $this->mTocText,
                'Sequence' => $this->mSequence,
                'ArticleId' => $this->mArticleId,
                'PreviousArticleId' => $this->mPreviousArticleId,
                'NextArticleId' => $this->mNextArticleId,
                'ParentArticleId' => $this->mParentArticleId,
            	'IndexOrder' => $this->mIndexOrder
		), $fname
		);
		$this->mId = $dbw->insertId();
		}
	}

	/**
	 * Update NextArticleId in the database
	 */
	function updateNextArticleId() {
		$fname = 'LoopStructureItem::updateNextArticleId';
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->update( 'loopstructure',
		array( 'NextArticleId' => $this->mNextArticleId ),
		array( 'Id' => $this->mId ),
		$fname );
	}

	// Deletes any record with the current ArticleId from the database.
	function deleteArticleId() {
		$fname = 'LoopStructureItem::delete';
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->delete('loopstructure',
		array(
                'ArticleId' => $this->mArticleId,
		), $fname
		);
	}

}


#----------------------------------------------------------------------------
#    Parser functions implementation
#----------------------------------------------------------------------------


function wfLoopStructureGetItem($parser) {
	$title = $parser->mTitle;
	if ($title == NULL) return NULL;
	$article_id = intval($title->getArticleID());
	if (!$article_id) return NULL;
	$item = LoopStructureItem::newFromArticleId($article_id);
	return $item;
}



function wfLoopStructureArticleLink($article_id, $description = '') {
	if (empty($article_id)) return "";
	$title = Title::newFromID($article_id);
	if ($title == NULL) return "";
	if (!$title->exists()) return "";
	$article_title = $title->getPrefixedText();
	if ($article_title == NULL) return "";
	if ($description) $description = "|$description";
	return "[[" . $article_title . $description . "]]";
}


function wfLoopStructureBreadcrumb($item, $max_length=65) {
	global $wgLoopStructureNumbering, $wgLoopStructureUseTopLevel, $wgLoopStructureUseMainpageForBreadcrumb;

	if ($wgLoopStructureUseMainpageForBreadcrumb) {
		$tempTitle=Title::newMainPage();
		$max_length=$max_length-strlen($tempTitle->mTextform);
	}
	
	if ($item == NULL) return "&nbsp;";
	$ancestors = array();
	$tempItem = $item;
	$tocnumber=$item->mTocNumber;
	while ($tempItem->mParentArticleId) {
		$ancestors[] = $tempItem->mParentArticleId;
		$tempItem = LoopStructureItem::newFromArticleId($tempItem->mParentArticleId);
	}


	$breadcrumb = Title::newFromID($item->mArticleId);
	if ($breadcrumb) {
	if ($wgLoopStructureNumbering) {
		if (($tocnumber==0)&&($wgLoopStructureUseTopLevel)) {
			$breadcrumb=$item->mIndexOrder.' '.$breadcrumb;
		} else {
			$breadcrumb=$tocnumber.' '.$breadcrumb;
		}
	}
	$breadcrumb_len=strlen($breadcrumb);
	$max_length=$max_length-$breadcrumb_len-3;
	$bc_len=0;
	$bc=array();
	$i=0;
	foreach ($ancestors as $ancestor) {
		$tempArticle= Article::newFromID($ancestor);

		if ($tempArticle) {
			$tempTitle=($tempArticle->getTitle());
			$title_text=$tempTitle->mTextform;
			//$title_text=urldecode($tempTitle->getPartialURL());
			
			$bc_len=$bc_len+strlen($title_text);
			$bc[$i][0]=$title_text;
			$bc[$i][1]=$tempTitle->getFullURL();

			if ($wgLoopStructureNumbering) {
				$tI = LoopStructureItem::newFromArticleId($ancestor);
				$tn=$tI->mTocNumber;
				if (($tn=='')&&($wgLoopStructureUseTopLevel)) {
					$tn=$tI->mIndexOrder;
				}
				$bc[$i][0]=$tn.' '.$bc[$i][0];
			}


			$i++;}
	}
	$item_len=floor(($max_length-($i*3))/$i);
	foreach ($bc as $b) {
		if (strlen($b[0])>$item_len) {
			$t=mb_substr($b[0],0,($item_len-2)).'..';
		} else {
			$t=$b[0];
		}
		$templink='<a href="'.$b[1].'" title="'.$b[0].'" alt="'.$b[0].'">'.$t.'</a>';
		$breadcrumb = "$templink &raquo; " . $breadcrumb;
	}
	} else {
		$breadcrumb='&nbsp;';
	}

	return $breadcrumb;
}

/* -------------------------------------------------------------------------------------------------- */

function wfLoopStructureNavigation($item) {
	global $wgStylePath, $wgServer, $wgScriptPath;


	$return='';
	if (!empty($item)) {
		$prevID=$item->mPreviousArticleId;
		$nextID=$item->mNextArticleId;
		$indexID=$item->mIndexArticleId;
		$parentID=$item->mParentArticleId;
		$aktSequence=$item->mSequence;
		$aktTocLevel=$item->mTocLevel;

		$return.='<a href="'.$wgServer.'/'.$wgScriptPath.'/" alt="Home" title="Home"><div class="navicon_main"></div></a> ';
		/*
		if ($aktTocLevel==0) {
			$return.= '<div class="navicon_home_in"></div>';
		} else {
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
                'ParentArticleId'
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
				foreach ( $res as $row ) {

					$tid=$row->ArticleId;

					$tempArticle= Article::newFromID($tid);
					if ($tempArticle) {
						$tempTitle=($tempArticle->getTitle());
						$tlevel=$row->TocLevel;
						$tempTitle->getFullURL();
						$tempTitle->mTextform;
						$return.= '<a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" title="'.$tempTitle->mTextform.'"><div class="navicon_home"></div></a> ';
					}

				}
		}
		*/

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
                'ParentArticleId'
                ),
                array(
                0 => "Sequence < '".$aktSequence."'",
                1 => "IndexArticleId = '".$indexID."'",
                2 => "TocLevel = 1"
                ),
                __METHOD__,
                array(
				'ORDER BY' => 'Sequence DESC'
				)
				);

				$row = $dbr->fetchRow( $res );
				if ($row) {
					$tid=$row["ArticleId"];
					$tempArticle= Article::newFromID($tid);
					$tempTitle=($tempArticle->getTitle());
					$return.='<a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" title="'.$tempTitle->mTextform.'"><div class="navicon_previouscap"></div></a>';
				} else {
					$return.= '<div class="navicon_previouscap_in"></div>';
				}
					
					

				if (!empty($prevID)) {
					$tempArticle= Article::newFromID($prevID);
					$tempTitle=($tempArticle->getTitle());
					$return.= '<a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" title="'.$tempTitle->mTextform.'"><div class="navicon_back"></div></a>';
				} else {
					$return.= '<div class="navicon_back_in"></div>';
				}

				if (!empty($indexID)) {
					$tempArticle= Article::newFromID($indexID);
					$tempTitle=($tempArticle->getTitle());
					$return.= '<a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" title="'.wfMsg('looptoc').'"><div class="navicon_directory"></div></a>';
				} else {
					$return.= '<div class="navicon_directory_in"></div>';
				}



				if (!empty($nextID)) {
					$tempArticle= Article::newFromID($nextID);
					$tempTitle=($tempArticle->getTitle());
					$return.= '<a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" title="'.$tempTitle->mTextform.'"><div class="navicon_next"></div></a>';
				} else {
					$return.= '<div class="navicon_next_in"></div>';
				}


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
                'ParentArticleId'
                ),
                array(
                0 => "Sequence > '".$aktSequence."'",
                1 => "IndexArticleId = '".$indexID."'",
                2 => "TocLevel = 1"
                ),
                __METHOD__,
                array(
				'ORDER BY' => 'Sequence ASC'
				)
				);

				$row = $dbr->fetchRow( $res );
				if ($row) {
					$tid=$row["ArticleId"];
					$tempArticle= Article::newFromID($tid);
					$tempTitle=($tempArticle->getTitle());
					$return.= '<a href="'.$tempTitle->getFullURL().'" alt="'.$tempTitle->mTextform.'" title="'.$tempTitle->mTextform.'"><div class="navicon_nextcap"></div></a>';
				} else {
					$return.= '<div class="navicon_nextcap_in"></div>';
				}

	} else {
		$return='&nbsp;';
	}
	return $return;
}
/* --------------------------- */



?>