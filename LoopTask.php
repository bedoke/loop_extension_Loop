<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopTask {

	var $file=false;
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
	var $pageOrder=0;

	var $render_options=array('none','icon','marked','default','title');

	function LoopTask($input,$args,$parser) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser, $wgLoopTaskDefaultRenderOption;
		//$title=$parser->getTitle();
		$this->input=$input;
		$this->args=$args;

		//wfDebug( __METHOD__ . ': task : '.print_r($this,true));
		/*
		$parser = new Parser( $wgParserConf );
		$parserOptions = ParserOptions::newFromUser( $wgUser );
		$parser->Options($parserOptions);

		$title=Title::newFromText('task');
		
		$parseroutput = $parser->parse( $input, $title, $parserOptions);
		$output=$parseroutput->mText;
		*/

		if (array_key_exists('title', $args)) {
			if ($args["title"]!='') {
				$this->title=$args["title"];
			} else {
				$this->title='';
			}
		}

		if (array_key_exists('description', $args)) {
			$this->description=$args["description"];
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
					$this->render=$wgLoopTaskDefaultRenderOption;
				} else {
					$this->render=$args["render"];
				}
			} else  {
				$this->render=$wgLoopTaskDefaultRenderOption;
			}
		} else  {
			$this->render=$wgLoopTaskDefaultRenderOption;
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
	function setPageOrder($pageOrder)	{ $this->pageOrder=$pageOrder; return true; }

	public function render() {
		global $wgStylePath, $wgParser;

		$return='';
		if ($this->title!='') {
			$return.='<span id="'.htmlentities(str_replace( ' ', '_', trim($this->title) ),ENT_QUOTES, "UTF-8").'"></span>';
		}

		switch ($this->render) {
			case 'marked':
			case 'icon':
				$return.='<div class="mediabox_'.$this->render.'">';
				
				$return.='<div class="mediabox_task_content">';
				$return.='<div><img class="mediabox_taskicon" src="'.$wgStylePath .'/loop/images/media/type_task.png" width="32">';
				//$return.='<div class="mediabox_typeicon"><div class="mediabox_typeicon_task"></div></div>';
				if ($this->title!='') {$return.='<span class="mediabox_title">'.$this->title.'</span>';}
				$return.='</div>';
				$output = $wgParser->recursiveTagParse($this->input);
				$return.= $output;
				$return.='</div>';
				$return.='</div>';
				$return.='<div class="clearer"></div>';
				break;
			case 'title':
				$return.='<div class="mediabox_marked">';
				
				$return.='<div class="mediabox_task_content">';
				$return.='<div>';
				if ($this->title!='') {$return.='<span class="mediabox_title">'.$this->title.'</span>';}
				$return.='</div>';
				$output = $wgParser->recursiveTagParse($this->input);
				$return.= $output;
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