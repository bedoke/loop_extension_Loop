<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}

class LoopSidenote {

	var $type='';
	var $copyright='';
	var $input='';
	var $args=array();


	function LoopSidenote($input,$args) {

		$sidenotetypes=array('none','marginalnote','keyword');

		$this->input=$input;
		$this->args=$args;

		if (array_key_exists('type', $args)) {
			$sidenotetype=$args["type"];
			if (in_array($sidenotetype,$sidenotetypes)) {
				$this->type=$args["type"];
			} else {
				$this->type="none";
			}
		} else {
			$this->type="none";
		}
	

		return true;
	}


	function render() {
		global $wgStylePath, $wgParser;

		$return='';
		switch ($this->type) {
			case 'none':
			case 'marginalnote':
			case 'keyword':
				$return.='<div class="loopsidenote">';
				$return.='<div class="'.$this->type.'">';
				#$return.='<div class="sidenote_typeicon"></div>';				
				$return.='<div class="sidenote_content">';
				$output = $wgParser->recursiveTagParse($this->input);
				$return.= $output;
				$return.='</div>';
				
	
				$return.='</div>';
				$return.='</div>';
				
				#$return.='<div class="clearer"></div>';


				break;

			default:
				$return.= $wgParser->recursiveTagParse($this->input);
		}
		return $return;
	}

}

?>