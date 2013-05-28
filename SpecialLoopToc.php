<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopToc extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopToc' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgLoopStructureNumbering;

		$this->setHeaders();

		$indexitems=array();
		$return = '<h1>'.wfMsg('looptoc').'</h1>';
		$cs=new LoopStructure();
		$return.=$cs->renderLoopTocFull();

		$wgOut->addHTML($return);
	}
}
               
?>