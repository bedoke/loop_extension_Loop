<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopPrivacy extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopPrivacy' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgServer, $wgeLoopImprintUrl;

		$this->setHeaders();
		$content ='<h2>'.wfMsg('loopprivacy').'</h2>';
		$content .= '<iframe style="border: 0; height: 200px; width: 600px;" src="https://noc.oncampus.de/piwik/index.php?module=CoreAdminHome&action=optOut&language=de"></iframe>'; 
		
		$wgOut->addHTML($content);
	}
}
               
?>