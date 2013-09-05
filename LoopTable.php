<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}

class LoopTable {

	var $title='';
	var $description='';
	var $copyright='';
	var $index=true;
	var $render='default';
	var $show_copyright=false;
	var $input='';
	var $args=array();
	
	var $structureTitle='';	
	var $structureURL='';
	var $pageTitle='';
	var $pageURL='';
	var $structureIndex=0;	
	var $structureIndexOrder=0;
	var $structureSequence=0;
	var $pageTocNumber=0;
	var $posOnPage=0;

	var $render_options=array('none','icon','marked','default');
	
	function LoopTable($input,$args) {
		global $wgLoopTableDefaultRenderOption;
		
		$this->input=$input;
		$this->args=$args;

		if (array_key_exists('title', $args)) {
			$this->title=$args["title"];
		} 
		if (array_key_exists('description', $args)) {
			$this->description=trim($args["description"]);
		} 
		if (array_key_exists('copyright', $args)) {
			$this->copyright=$args["copyright"];
		} 
		if (array_key_exists('index', $args)) {
			if ($args["index"]=='false') {
				$this->index=false;
			}
		}
		if (array_key_exists('show_copyright', $args)) {
			if ($args["show_copyright"]=='true') {
				$this->show_copyright=true;
			}
		}	
		if (array_key_exists('render', $args)) {
			if (in_array($args["render"],$this->render_options)) {
				if ($args["render"]=='default') {
					$this->render=$wgLoopTableDefaultRenderOption;
				} else {
					$this->render=$args["render"];
				}
			} else  {
				$this->render=$wgLoopTableDefaultRenderOption;
			}
		} else  {
			$this->render=$wgLoopTableDefaultRenderOption;
		}			 		
		return true;
	}	

	function setStructureTitle($structureTitle)	{ $this->structureTitle=$structureTitle; return true; }	
	function setStructureURL($structureURL)	{ $this->structureURL=$structureURL; return true; }
	function setPageTitle($pageTitle)	{ $this->pageTitle=$pageTitle; return true; }
	function setPageURL($pageURL)	{ $this->pageURL=$pageURL; return true; }
	function setStructureIndex($structureIndex)	{ $this->structureIndex=$structureIndex; return true; }	
	function setStructureIndexOrder($structureIndexOrder)	{ $this->structureIndexOrder=$structureIndexOrder; return true; }
	function setStructureSequence($structureSequence)	{ $this->structureSequence=$structureSequence; return true; }	
	function setPageTocnumber($pageTocNumber)	{ $this->pageTocNumber=$pageTocNumber; return true; }	
	
	function render() {
		global $wgStylePath,  $wgParser;
		
		$return='';
		if ($this->title!='') {
			$return.='<div id="'.htmlentities(str_replace( ' ', '_', trim($this->title) ),ENT_QUOTES, "UTF-8").'"></div>';
		}


		switch ($this->render) {
			case 'marked':
			case 'icon':
				$return.='<div class="mediabox_'.$this->render.'">';
				$return.='<div class="mediabox_content">';
				$output = $wgParser->recursiveTagParse($this->input);
				$return.= $output;
				$return.='</div>';
				$return.='<div class="mediabox_footer">';
				//$return.='<div class="mediabox_typeicon"><img src="'.$wgStylePath .'/loop/images/media/type_table.png"></div>';
				$return.='<div class="mediabox_typeicon"><div class="mediabox_typeicon_table"></div></div>';
				$return.='<div class="mediabox_footertext">';
				if ($this->title!='') {$return.='<span class="mediabox_title">'.$this->title.'</span><br/>';}
				if ($this->description!='') {$return.='<span class="mediabox_description">'.$this->description.'</span><br/>';}
				if (($this->show_copyright==true)&&($this->copyright!='')){$return.='<span class="mediabox_copyright">'.$this->copyright.'</span>';}
				$return.='</div>';
				$return.='<div class="clearer"></div>';
				$return.='</div>';
				$return.='</div><div class="clearer"></div>';

				break;
			case 'none':
			default:
				$return.= $wgParser->recursiveTagParse($this->input);
		}		
		
		

		return $return;
	}

}

?>