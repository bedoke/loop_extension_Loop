<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

define( 'LOOP_HEADER', 'header' );
define( 'LOOP_LOCALTOC', 'looptoc' );
define( 'LOOP_BIBLIOGRAPHYPAGE', 'bibliographypage' );
define( 'LOOP_ABBREVIATIONPAGE', 'abbreviationpage' );
define( 'LOOP_GLOSSARYPAGE', 'glossarypage' );

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => "Loop",
	'description' => "Learning Object Online Platform",
	'descriptionmsg' => "loop_extension_description",
	'version' => 1.0,
	'author' => array("Marc Vorreiter"),
	'url' => "http://www.oncampus.de"
);

$dir = dirname(__FILE__) . '/';

$wgExtensionMessagesFiles['Loop'] = $dir . 'Loop.i18n.php';
$wgExtensionMessagesFiles['LoopMagic'] = dirname(__FILE__) . '/Loop.i18n.magic.php';

$wgHooks['ParserFirstCallInit'][] = 'loopInit';
$wgHooks['ArticleSaveComplete'][] = 'fnLoopStructureSaveHook';

$wgHooks['ParserBeforeInternalParse'][] = 'fnRenderLoopStructure';

$wgHooks['LanguageGetMagic'][] = 'fnLoopRegisterMagicWords';
$wgHooks['ParserGetVariableValueSwitch'][] = 'fnLoopAssignAValue';
$wgHooks['MagicWordwgVariableIDs'][] = 'fnLoopDeclareVarIds';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'fnLoopUpdateSchema';

$wgLoopStructureNumbering=true;
$wgLoopStructureUseTopLevel = false;
$wgLoopStructureUseMainpageForBreadcrumb=false;
$wgLoopStructureBreadcrumbLength=100;

$wgLoopAreaDefaultRenderOption = 'marked';
$wgLoopFigureDefaultRenderOption = 'marked';
$wgLoopMediaDefaultRenderOption = 'marked';
$wgLoopTableDefaultRenderOption = 'marked';
$wgLoopListingDefaultRenderOption = 'marked';
$wgLoopFormulaDefaultRenderOption = 'marked';
$wgLoopTaskDefaultRenderOption = 'icon';


$wgResourceModules['ext.Loop'] = array(
	'localBasePath' => dirname(__FILE__),
	'remoteExtPath' => 'Loop',
	'scripts' => array('libs/jstree/jquery.jstree.js','loop.js'),
  'messages' => array(
	'loop-show-left-sidebar',
	'loop-hide-left-sidebar',
	'loop-show-right-sidebar',
	'loop-hide-right-sidebar'
  )	
);


$wgHooks['ResourceLoaderRegisterModules'][]= 'LoopLoadModules';

function LoopLoadModules() {
	global $wgOut;
	$wgOut->addModules( 'ext.Loop' );
	return true;
}

$wgAutoloadClasses['LoopFigure'] = $dir . 'LoopFigure.php';
$wgAutoloadClasses['SpecialLoopFigures'] = $dir . 'SpecialLoopFigures.php';
$wgAutoloadClasses['LoopTable'] = $dir . 'LoopTable.php';
$wgAutoloadClasses['SpecialLoopTables'] = $dir . 'SpecialLoopTables.php';
$wgAutoloadClasses['LoopMedia'] = $dir . 'LoopMedia.php';
$wgAutoloadClasses['SpecialLoopMedia'] = $dir . 'SpecialLoopMedia.php';
$wgAutoloadClasses['LoopFormula'] = $dir . 'LoopFormula.php';
$wgAutoloadClasses['SpecialLoopFormulas'] = $dir . 'SpecialLoopFormulas.php';
$wgAutoloadClasses['LoopListing'] = $dir . 'LoopListing.php';
$wgAutoloadClasses['SpecialLoopListings'] = $dir . 'SpecialLoopListings.php';
$wgAutoloadClasses['LoopTask'] = $dir . 'LoopTask.php';
$wgAutoloadClasses['SpecialLoopTasks'] = $dir . 'SpecialLoopTasks.php';

$wgAutoloadClasses['LoopStructure'] = $dir . 'LoopStructure.php';
$wgAutoloadClasses['LoopStructureItem'] = $dir . 'LoopStructure.php';
$wgAutoloadClasses['LoopArea'] = $dir . 'LoopArea.php';
$wgAutoloadClasses['SpecialLoopToc'] = $dir . 'SpecialLoopToc.php';
$wgAutoloadClasses['LoopPrint'] = $dir . 'LoopPrint.php';
$wgAutoloadClasses['LoopNoprint'] = $dir . 'LoopNoprint.php';

$wgAutoloadClasses['SpecialLoopIndex'] = $dir . 'SpecialLoopIndex.php';

$wgAutoloadClasses['SpecialLoopPrintversion'] = $dir . 'SpecialLoopPrintversion.php';
$wgSpecialPages['LoopPrintversion'] = 'SpecialLoopPrintversion';

$wgAutoloadClasses['LoopParagraph'] = $dir . 'LoopParagraph.php';
$wgAutoloadClasses['LoopSidenote'] = $dir . 'LoopSidenote.php';



$wgSpecialPages['LoopFigures'] = 'SpecialLoopFigures';
$wgSpecialPageGroups['LoopFigures'] = 'media';
$wgSpecialPages['LoopTables'] = 'SpecialLoopTables';
$wgSpecialPageGroups['LoopTables'] = 'media';
$wgSpecialPages['LoopMedia'] = 'SpecialLoopMedia';
$wgSpecialPageGroups['LoopMedia'] = 'media';
$wgSpecialPages['LoopTasks'] = 'SpecialLoopTasks';
$wgSpecialPageGroups['LoopTasks'] = 'media';
$wgSpecialPages['LoopFormulas'] = 'SpecialLoopFormulas';
$wgSpecialPageGroups['LoopFormulas'] = 'media';
$wgSpecialPages['LoopListings'] = 'SpecialLoopListings';
$wgSpecialPageGroups['LoopListings'] = 'media';

$wgSpecialPages['LoopIndex'] = 'SpecialLoopIndex';
$wgSpecialPageGroups['LoopIndex'] = 'pages';
$wgSpecialPages['LoopToc'] = 'SpecialLoopToc';
$wgSpecialPageGroups['LoopToc'] = 'pages';


$wgAutoloadClasses['SpecialLoopImprint'] = $dir . 'SpecialLoopImprint.php';
$wgSpecialPages['LoopImprint'] = 'SpecialLoopImprint'; 
$wgSpecialPageGroups['LoopImprint'] = 'pages'; 

$wgAutoloadClasses['SpecialLoopChapteraudio'] = $dir . 'SpecialLoopChapteraudio.php';
$wgSpecialPages['LoopChapteraudio'] = 'SpecialLoopChapteraudio'; 
$wgSpecialPageGroups['LoopChapteraudio'] = 'pages'; 

$wgAutoloadClasses['SpecialLoopAudio'] = $dir . 'SpecialLoopAudio.php';
$wgSpecialPages['LoopAudio'] = 'SpecialLoopAudio'; 
$wgSpecialPageGroups['LoopAudio'] = 'pages'; 


$wgAutoloadClasses['SpecialLoopNoToc'] = $dir . 'SpecialLoopNoToc.php';
$wgSpecialPages['LoopNoToc'] = 'SpecialLoopNoToc'; 
$wgSpecialPageGroups['LoopNoToc'] = 'maintenance'; 


function loopInit( Parser &$parser ) {




	$parser->setHook( 'loop_figure', 'fnRenderLoopFigure' );
	$parser->setHook( 'loop_table', 'fnRenderLoopTable' );
	$parser->setHook( 'loop_media', 'fnRenderLoopMedia' );
	$parser->setHook( 'loop_listing', 'fnRenderLoopListing' );
	$parser->setHook( 'loop_formula', 'fnRenderLoopFormula' );
	$parser->setHook( 'loop_task', 'fnRenderLoopTask' );
	// $parser->setHook( 'loop_structure', 'fnRenderLoopStructureIndex' );
	$parser->setHook( 'loop_area', 'fnRenderLoopArea' );
	$parser->setHook( 'loop_toc', 'fnRenderLoopToc' );
	$parser->setHook( 'loop_print', 'fnRenderLoopPrint' );
	$parser->setHook( 'loop_noprint', 'fnRenderLoopNoprint' );

	$parser->setFunctionHook('header', 'fnLoopHeaderRender');

	$parser->setHook( 'loop_paragraph', 'fnRenderLoopParagraph' );
	$parser->setHook( 'loop_sidenote', 'fnRenderLoopSidenote' );
	$parser->setHook( 'loop_sidebar', 'fnRenderLoopSidebar' );

	return true;
}

function fnRenderLoopSidebar($input, array $args, Parser $parser, PPFrame $frame) {
	return '';
}

function fnRenderLoopSidenote($input, array $args, Parser $parser, PPFrame $frame) {
	$sidenote = new LoopSidenote($input,$args);
	return $sidenote->render();
}


function fnRenderLoopParagraph($input, array $args, Parser $parser, PPFrame $frame) {
	$paragraph = new LoopParagraph($input,$args);
	return $paragraph->render();
}


function fnRenderLoopFigure($input, array $args, Parser $parser, PPFrame $frame) {
	$title=$parser->getTitle();
	$figure = new LoopFigure($input,$args,$title);
	return $figure->render();
}

function fnRenderLoopTable($input, array $args, Parser $parser, PPFrame $frame) {
	$table = new LoopTable($input,$args);
	return $table->render();
}

function fnRenderLoopMedia($input, array $args, Parser $parser, PPFrame $frame) {
	$media = new LoopMedia($input,$args);
	return $media->render();
}

function fnRenderLoopListing($input, array $args, Parser $parser, PPFrame $frame) {
	$listing = new LoopListing($input,$args);
	return $listing->render();
}

function fnRenderLoopFormula($input, array $args, Parser $parser, PPFrame $frame) {
	$formula = new LoopFormula($input,$args);
	return $formula->render();
}

function fnRenderLoopTask($input, array $args, Parser $parser, PPFrame $frame) {
	$task = new LoopTask($input,$args,$parser);
	return $task->render();
}

function fnRenderLoopArea($input, array $args, Parser $parser, PPFrame $frame) {
	$area = new LoopArea($input,$args);
	return $area->render();
}

function fnRenderLoopPrint($input, array $args, Parser $parser, PPFrame $frame) {
	$printarea = new LoopPrint($input,$args);
	return $printarea->render();
}

function fnRenderLoopNoprint($input, array $args, Parser $parser, PPFrame $frame) {
	$noprintarea = new LoopNoprint($input,$args);
	return $noprintarea->render();
}


function fnRenderLoopToc($input, array $args, Parser $parser, PPFrame $frame) {
	$loopstructure = new LoopStructure();
	return $loopstructure->renderToc();
}


function fnRenderLoopStructureIndex( $input, $argv, $parser ) {
	if ($argv['order']){
		$indexorder=$argv['order'];
	} else {
		$indexorder=0;
	}
	$loopstructure = new LoopStructure();
	$return = $loopstructure->Render($input, $parser->mTitle, $parser->mOptions, $indexorder);
	$return='<div id="LoopIndex"><br/><br/>'.$return.'<br/><br/></div>';
	return $return;
}

function fnLoopStructureSaveHook(&$article, &$user, $text, $summary, $minoredit, $watchthis, $sectionanchor, $flags) {

	$pagetitle=$article->mTitle;
	if (($pagetitle->mNamespace==8) && (($pagetitle->mTextform=='Loop toc')||($pagetitle->mTextform=='Loop_toc')||($pagetitle->mTextform=='loop_toc')||($pagetitle->mTextform=='loop toc')))  {
		/*
		 $pattern = '@<loop_structure(.*?)>(.*?)</loop_structure>@is';
		 if (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
			if ($matches[1][0]!='') {
			$argtext= $matches[1][0];
			$pattern2 = '@order="(.*?)"@is';
			if (preg_match($pattern2, $argtext, $matches2, PREG_OFFSET_CAPTURE)) {
			$indexorder=$matches2[1][0];
			} else {
			$indexorder=0;
			}
			} else {
			$indexorder=0;
			}
			$index_text = $matches[2][0];
			$loopstructure = new LoopStructure();
			$loopstructure->Save($index_text, $article, $user, $indexorder);
			}
			*/
		$loopstructure = new LoopStructure();
		$loopstructure->Save($text, $article, $user, 0);
	}
	return true;
}
function fnRenderLoopStructure ( &$parser, &$text, &$strip_state) {
	global $wgTitle;
	static $loopTocAlreadyCalled = false;
	if ( $loopTocAlreadyCalled ) {
		return true;
	}
	$loopTocAlreadyCalled = true;	

	if (($wgTitle->mNamespace==8) && (($wgTitle->mTextform=='Loop toc')||($wgTitle->mTextform=='Loop_toc')||($wgTitle->mTextform=='loop toc')||($wgTitle->mTextform=='loop_toc')))  {

		if (stripos($text,'=')!=false) {
			$loopstructure = new LoopStructure();
			$return = $loopstructure->Render($text, $parser->mTitle, $parser->mOptions, 0);
			$return='<div id="LoopIndex"><h1>'.wfMsg('looptoc').'</h1>'.$return.'<br/><br/></div>';
			$text=$return;
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}

}


function fnLoopRegisterMagicWords(&$magicWords, $langCode) {
	$magicWords[LOOP_HEADER] = array(0, 'header');
	$magicWords[LOOP_LOCALTOC] = array(0, 'looptoc');
	$magicWords[LOOP_BIBLIOGRAPHYPAGE] = array(0, 'bibliographypage');
	$magicWords[LOOP_ABBREVIATIONPAGE] = array(0, 'abbreviationpage');	
	$magicWords[LOOP_GLOSSARYPAGE] = array(0, 'glossarypage');	
	return true;
}

function fnLoopHeaderRender($parser, $fileparam = '', $titlecolor='') {

	$output='';
	if ($fileparam!='') {
		$file = wfLocalFile($fileparam);
		if ($file) {
			$fileurl = $file->getUrl();

			$output.= '<script language="JavaScript">';
			$output.= '$("#headerLogo").css("backgroundImage","url('.$fileurl.')");';
			if ($titlecolor!='') {
				$output.= '$("#headerTitle").css("color","'.$titlecolor.'");';
			}
			//			if ($imageheight!='') {
			//				$output.= '$("#headerLogo").height('.intval($imageheight).');';
			//			}
			$output.= '</script>';
		}
	}

	return $parser->insertStripItem( $output, $parser->mStripState );


}


function fnLoopAssignAValue( &$parser, &$cache, &$magicWordId, &$ret ) {
	switch($magicWordId) {
		case LOOP_LOCALTOC:
			$output='';
			$loopstructure = new LoopStructure();
			$output=$loopstructure->renderToc();
			$ret = $parser->insertStripItem( $output, $parser->mStripState );
			break;

		case LOOP_BIBLIOGRAPHYPAGE:
			$output=wfMsg('bibliographypage');
			$ret = $parser->insertStripItem( $output, $parser->mStripState );

			break;

		case LOOP_ABBREVIATIONPAGE:
			$output= wfMsg('lingo-terminologypagename');
			$ret =  $parser->insertStripItem( $output, $parser->mStripState );
			break;

		case LOOP_GLOSSARYPAGE:
			$output= wfMsg('glossarypage');
			$ret =  $parser->insertStripItem( $output, $parser->mStripState );
			break;

		default:
			break;
	}

	return true;
}

function fnLoopDeclareVarIds( &$customVariableIds ) {
	$customVariableIds[] = LOOP_LOCALTOC;
	$customVariableIds[] = LOOP_BIBLIOGRAPHYPAGE;
	$customVariableIds[] = LOOP_ABBREVIATIONPAGE;	
	$customVariableIds[] = LOOP_GLOSSARYPAGE;	
	return true;
}


function fnLoopUpdateSchema( $updater = null ) {
	if ( $updater === null ) {
		global $wgExtNewTables;
		$wgExtNewTables[] = array( 'loopstructure', dirname( __FILE__ ) . '/LoopStructure.sql' );
	} else {
		$updater->addExtensionUpdate( array( 'addTable', 'loopstructure',
		dirname( __FILE__ ) . '/LoopStructure.sql', true ) );
	}
	return true;
}


?>