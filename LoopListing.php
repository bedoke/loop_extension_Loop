<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopListing {

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

	var $render_options=array('none','icon','marked','default');

	function LoopListing($input,$args) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser, $wgLoopListingDefaultRenderOption;

		$this->input=$input;
		$this->args=$args;

		$parser = new Parser( $wgParserConf );
		$parserOptions = ParserOptions::newFromUser( $wgUser );
		$parser->Options($parserOptions);

		$parseroutput = $parser->parse( $input, $wgTitle, $parserOptions);
		$output=$parseroutput->mText;
		//$output = $wgParser->recursiveTagParse($input);

		$pattern='@src="(.*?)"@';
		$file_found=preg_match($pattern,$output,$matches);
		if($file_found) {
			$this->file=basename($matches[1]);
			$img = wfFindFile($this->file);
			$meta=$img->formatMetadata();


			foreach ($meta["visible"] as $meta_item) {
				if ($meta_item["id"]=="exif-imagedescription") {
					$this->description=$meta_item["value"];
				}
			}
			foreach ($meta["collapsed"] as $meta_item) {
				if ($meta_item["id"]=="exif-headline") {
					$this->title=$meta_item["value"];
				}
				if ($meta_item["id"]=="exif-copyrighted") {
					$this->copyright.=$meta_item["value"].' ';
				}
				if ($meta_item["id"]=="exif-webstatement") {
					$this->copyright.=$meta_item["value"];
				}
			}
		} else {
			$this->file=false;
		}
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
					$this->render=$wgLoopListingDefaultRenderOption;
				} else {
					$this->render=$args["render"];
				}
			} else  {
				$this->render=$wgLoopListingDefaultRenderOption;
			}
		} else  {
			$this->render=$wgLoopListingDefaultRenderOption;
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
				$return.='<div class="mediabox_content">';
				$output = $wgParser->recursiveTagParse($this->input);
				$return.= $output;
				$return.='</div>';
				$return.='<div class="mediabox_footer">';
				//$return.='<div class="mediabox_typeicon"><img src="'.$wgStylePath .'/loop/images/media/type_listing.png"></div>';
				$return.='<div class="mediabox_typeicon"><div class="mediabox_typeicon_listing"></div></div>';
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