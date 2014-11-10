<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopImprint extends SpecialPage {
	function __construct() {
		parent::__construct( 'LoopImprint' );
	}

	function execute( $par ) {
		global $wgOut, $wgParser, $wgServer, $wgeLoopImprintUrl;

		$this->setHeaders();

	
		$return = '';		
		$loop = mb_substr($wgServer,7);
		$url=$wgeLoopImprintUrl.'?loop='.$loop;
		
		
		$cha = curl_init();
		curl_setopt($cha, CURLOPT_URL, ($url));
		curl_setopt($cha, CURLOPT_ENCODING, "UTF-8" );
		curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cha, CURLOPT_FOLLOWLOCATION, true);
		 
		$return .= curl_exec($cha);
		
		#echo "<!-- RETURN:".$return." -->";
		curl_close($cha);

		$wgOut->addHTML($return);
	}
}
               
?>