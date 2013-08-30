<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopNoprint {
	var $input='';
	var $args=array();	
	
	
	function LoopNoprint($input,$args) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser;
		
		$this->input=$input;
		$this->args=$args;		
		
		return true;
	}
	

	public function render() {
		global $wgStylePath, $wgParser;
		
		$return='';
		$noprint_id=uniqid();
		$return.='<div class="noprintarea" id="'.$noprint_id.'">';
		$output = $wgParser->recursiveTagParse( $this->input);
		$return.= $output;
		$return.= '</div>';
		
		return $return;
	}	

}
?>