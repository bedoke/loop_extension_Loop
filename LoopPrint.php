<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopPrint {
	var $input='';
	var $args=array();	
	var $button = true;
	
	
	function LoopPrint($input,$args) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser;
		
		$this->input=$input;
		$this->args=$args;		

		if (array_key_exists('button', $args)) {
			$buttonvalue=$args["button"];
			if ($buttonvalue == "false") {
				$this->button = false;
			} else {
				$this->button = true;
			}
		} else {
			$this->button = true;
		}
		
		
		return true;
	}
	

	public function render() {
		global $wgStylePath, $wgParser;
		
		$return='';
		if ($this->button == true) {
			$print_id=uniqid();
			$return.="<a href='#' alt='printversion' title='".wfMsg('loopPrintVersion')."'onClick='$(\"#$print_id\").toggle();return false;'><span class='printarea_icon'></span></a><br/>";
			$return.='<div class="printarea" id="'.$print_id.'">';
			$output = $wgParser->recursiveTagParse( $this->input);
			$return.= $output;
			$return.= '</div>';
		}			
		return $return;
	}	

}
?>