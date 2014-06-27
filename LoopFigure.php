<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopFigure {

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
	var $posOnPage=0;

	var $render_options=array('none','icon','marked','default');

	function LoopFigure($input,$args,$title) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser, $wgLoopFigureDefaultRenderOption;




		$parser = new Parser( $wgParserConf );
		$parserOptions = ParserOptions::newFromUser( $wgUser );
		$parser->Options($parserOptions);

		//var_dump($title);
		//exit;

		$matches=array();
		$pattern = '@(?<=<loop_figure_title>)(.*?)(?=<\/loop_figure_title>)@isu';
		if (preg_match($pattern, $input, $matches)==1) {
				# $this->title = $wgParser->recursiveTagParse($matches[1]);
				$this->title = $matches[1];
		}

		$matches=array();
		$pattern = '@(?<=<loop_figure_description>)(.*?)(?=<\/loop_figure_description>)@isu';
		if (preg_match($pattern, $input, $matches)==1) {
				# $this->description = $wgParser->recursiveTagParse($matches[1]);
				$this->description = $matches[1];
		}
		





		$pattern = "/(\r\n|\r|\n)/";
		$replacement = PHP_EOL;
		$string = preg_replace($pattern, $replacement, $input);
			
		$pattern = '@(<loop_figure_title>)(.*?)(<\/loop_figure_title>)@isu';
		$replace = '';
		$input = preg_replace($pattern, $replace, $input);

		$pattern = '@(<loop_figure_description>)(.*?)(<\/loop_figure_description>)@isu';
		$replace = '';
		$input = preg_replace($pattern, $replace, $input);

		$this->input=$input;
		$this->args=$args;

		if ($this->description == '') {
			if (array_key_exists('description', $args)) {
				$this->description=$args["description"];
			}
		}		
		

		$parseroutput = $parser->parse( $input, $title, $parserOptions);
		$output=$parseroutput->mText;
		//$output = $wgParser->recursiveTagParse($input);

		//wfDebug( __METHOD__ . ': output : '.print_r($output,true)."\n");

		$pattern='@src="(.*?)"@';
		$file_found=preg_match($pattern,$output,$matches);


		$tmp_src=$matches[1];
		$tmp_src_array=explode('/', $tmp_src);

		if (isset($tmp_src_array[7])) {
			$filename=$tmp_src_array[7];
		} else {
			$filename=$tmp_src_array[6];
		}

		$filename=urldecode($filename);
		/*
		 wfDebug( __METHOD__ . ': tmp_src_array : '.print_r($tmp_src_array,true)."\n");

		 $altpattern='@alt="(.*?)"@';
		 $altfile_found=preg_match($pattern,$output,$altmatches);

		 $altmatch=str_replace(' ', '_', $altmatches[1]);

		 if (basename($matches[1])!=$altmatch) {
			$filename=$altmatch;
			} else {
			$filename=basename($matches[1]);
			}
			*/
		//wfDebug( __METHOD__ . ': filename : '.print_r($filename,true)."\n");

		if($file_found) {
			//$this->file=basename($matches[1]);
			$this->file=$filename;
			$img = wfFindFile($this->file);
			
			// wfDebug( __METHOD__ . ': img : '.print_r($img,true)."\n");
			
			if ($img) {
				$meta=$img->formatMetadata();

				if ($meta) {
					

				foreach ($meta["visible"] as $meta_item) {
					if ($meta_item["id"]=="exif-imagedescription") {
						if ($this->description == '') {
							$this->description=$meta_item["value"];
						}
					}
					if ($meta_item["id"]=="exif-copyright") {
						$this->copyright.=$meta_item["value"].' ';
					}
				}
				foreach ($meta["collapsed"] as $meta_item) {
					if ($meta_item["id"]=="exif-headline") {
						$this->title=$meta_item["value"];
					}
					/*
					 if ($meta_item["id"]=="exif-copyrighted") {
					 $this->copyright.=$meta_item["value"].' ';
					 }
					 if ($meta_item["id"]=="exif-webstatement") {
					 $this->copyright.=$meta_item["value"];
					 }
					 */
				}
				}
			} else {
				// ToDO
			}
		} else {
			$this->file=false;
		}

		if ($this->title == '') {
			if (array_key_exists('title', $args)) {
				if ($args["title"]!='') {
					$this->title=$args["title"];
				} else {
					$this->title='';
				}
			}
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
					$this->render=$wgLoopFigureDefaultRenderOption;
				} else {
					$this->render=$args["render"];
				}
			} else  {
				$this->render=$wgLoopFigureDefaultRenderOption;
			}
		} else  {
			$this->render=$wgLoopFigureDefaultRenderOption;
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
	function setPosOnPage($posOnPage)	{ $this->posOnPage=$posOnPage; return true; }

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
				/*
				 if ($this->file) {
					$image='[[Image:'.$this->file.']]';
					$output = $wgParser->recursiveTagParse($image);
					$return.= trim($output);
					}
					*/
				$image=$this->input;
				$output = $wgParser->recursiveTagParse($image);
				$return.= trim($output);

				$return.='</div>';
				$return.='<div class="mediabox_footer">';
				//$return.='<div class="mediabox_typeicon"><img src="'.$wgStylePath .'/loop/images/media/type_image.png"></div>';
				$return.='<div class="mediabox_typeicon"><div class="mediabox_typeicon_image"></div></div>';
				$return.='<div class="mediabox_footertext">';
				if ($this->title!='') {$return.='<span class="mediabox_title">'.$wgParser->recursiveTagParse($this->title).'</span><br/>';}
				if ($this->description!='') {$return.='<span class="mediabox_description">'.$wgParser->recursiveTagParse($this->description).'</span><br/>';}
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