<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}

class LoopParagraph {

	var $type='';
	var $copyright='';
	var $input='';
	var $args=array();


	function LoopParagraph($input,$args) {

		$paragraphtypes=array('none','citation');

		$this->input=$input;
		$this->args=$args;

		if (array_key_exists('type', $args)) {
			$paragraphtype=$args["type"];
			if (in_array($paragraphtype,$paragraphtypes)) {
				$this->type=$args["type"];
			} else {
				$this->type="none";
			}
		} else {
			$this->type="none";
		}
	
		if (array_key_exists('copyright', $args)) {
			$this->copyright=$args["copyright"];
		}

		return true;
	}


	function render() {
		global $wgStylePath, $wgParser;

		$return='';
		switch ($this->type) {
			case 'citation':
				$return.='<div class="loopparagraph">';
				$return.='<div class="ciation">';
				
				$return.='<div class="paragraph_typeicon"></div>';				
				
				$return.='<div class="paragraph_content">';
				$output = $wgParser->recursiveTagParse($this->input);
				$return.= $output;
				$return.='</div>';
				
				if ($this->copyright!=''){$return.='<div class="paragraph_copyright">'.$this->copyright.'</div>';}
				
				$return.='</div>';
				$return.='</div>';
				
				$return.='<div class="clearer"></div>';

				break;
			case 'none':
			default:
				$return.= $wgParser->recursiveTagParse($this->input);
		}
		return $return;
	}

}

?>