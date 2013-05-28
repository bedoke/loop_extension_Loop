<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopPrint {
	var $input='';
	var $args=array();	
	
	
	function LoopPrint($input,$args) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser;
		
		$this->input=$input;
		$this->args=$args;		
		
		return true;
	}
	

	public function render() {
		global $wgStylePath, $wgParser;
		
		$return='';
		$print_id=uniqid();
		$return.="<a href='#' alt='printversion' title='".wfMsg('loopPrintVersion')."'onClick='$(\"#$print_id\").toggle();return false;'><span class='printarea_icon'></span></a><br/>";
		$return.='<div class="printarea" id="'.$print_id.'">';
		$output = $wgParser->recursiveTagParse( $this->input);
		$return.= $output;
		$return.= '</div>';
				
		
		
		return $return;
	}	

}
?>