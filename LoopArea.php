<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file cannot be run standalone.\n" );
}


class LoopArea {

	var $type='';
	var $input='';
	var $args=array();
	var $render='default';
	var $index=true;
	var $title='';

	var $icon='';
	var $icontext='';

	var $structureTitle='';

	var $structureURL='';
	var $pageTitle='';
	var $pageURL='';
	var $structureIndex=0;
	var $structureIndexOrder=0;
	var $structureSequence=0;
	var $pageTocNumber=0;

	var $render_options=array('none','icon','marked','default','frame');




	function LoopArea($input,$args) {
		global $wgParser, $wgTitle, $wgParserConf, $wgUser, $wgLoopAreaDefaultRenderOption;

		$areatypes=array('task','timerequirement','learningobjectives','arrangement','example','reflection','notice','sourcecode','summary','important','markedsentence','annotation','definition','formula','indentation','area','norm','law','question','practice','exercise','websource','experiment');

		$this->input=trim($input);
		$this->args=$args;

		if (array_key_exists('type', $args)) {
			$areatype=$args["type"];
			if (in_array($areatype,$areatypes)) {
				$this->type=$args["type"];
			} else {
				$this->type="area";
			}
		} else {
			$this->type="area";
		}

		if (array_key_exists('render', $args)) {
			if (in_array($args["render"],$this->render_options)) {
				if ($args["render"]=='default') {
					$this->render=$wgLoopAreaDefaultRenderOption;
				} else {
					$this->render=$args["render"];
				}
			} else  {
				$this->render=$wgLoopAreaDefaultRenderOption;
			}
		} else  {
			$this->render=$wgLoopAreaDefaultRenderOption;
		}

		if ($this->render == 'frame') {
			$this->render = 'marked';
		}

		if (array_key_exists('index', $args)) {
			if ($args["index"]=='false') {
				$this->index=false;
			}
		}
		if (array_key_exists('title', $args)) {
			if ($args["title"]!='') {
				$this->title=$args["title"];
			} else {
				$this->title='';
			}
		} 	 else {
			$this->title='';
		}

		if (array_key_exists('icon', $args)) {
			if ($args["icon"]!='') {
				$this->icon=$args["icon"];
			} else {
				$this->icon='';
			}
		}
		if (array_key_exists('icontext', $args)) {
			if ($args["icontext"]!='') {
				$this->icontext=$args["icontext"];
			} else {
				$this->icontext='';
			}
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

		$return='<div class="area" ';
		if ($this->title!='') {
			$return.='id="'.htmlentities(str_replace( ' ', '_', trim($this->title) ),ENT_QUOTES, "UTF-8").'"';
		}
		$return.='>';
		switch ($this->render) {
			case 'marked':
			case 'icon':
			case 'frame':
				$return.='<div class="areamark_'.$this->render.'">';
				if ($this->type=='area') {
					if ($this->icon) {
						$image='[[Image:'.$this->icon.']]';
						$output = $wgParser->recursiveTagParse($image);
            			$pattern = '$<img(.*)(?=</a>)$';
						preg_match($pattern, $output, $matches);
						
						//$return.= '<!-- '.print_r($matches,true).' -->';
			            if (isset($matches[0])) {
			            	$return.= $matches[0];
						} else {
							$return.= $output;
						}
						$return.='<br/><span class="areatext">'.$this->icontext.'</span>';
						$return.='<div class="areafix"></div>';
					}
				} else {
					
					$return.='<div class="loop_areamark_'.$this->type.'"></div><div class="areatext">'.wfMsg('looparea-icontext-'.$this->type).'</div>';

					
					$return.='<div class="areafix"></div>';
				}
				$return.='</div><div class="areacontent_'.$this->render.'">';
				$output = $wgParser->recursiveTagParse( $this->input);
				$return.= $output;
				$return.= '</div></div><div class="clearer">';

				break;
			case 'none':
			default:
				$return.= $wgParser->recursiveTagParse($this->input);
		}

		$return.='</div>';

		return $return;
	}

}
?>