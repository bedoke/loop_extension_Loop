<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopNoToc extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopNoToc' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgLoopStructureNumbering;

		$this->setHeaders();

		$indexitems=array();
		$return = '<h1>'.wfMsg('loopnotoc').'</h1>';
		

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->query('SELECT * FROM page WHERE page_namespace=0 and page_id not in (select ArticleId from loopstructure) order by page_title');
		foreach ( $res as $row ) {
			$title = Title::newFromID($row->page_id);
			$return.= Linker::link($title).'<br/>';
		}





		$wgOut->addHTML($return);
	}
}
               
?>