<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialLoopReviewAllPages extends SpecialPage {
	function __construct() {
		parent::__construct( 'SpecialLoopReviewAllPages' );
	}

	function execute( $par ) {
		$this->setHeaders();
		$this->outputHeader();

		set_time_limit(0);
		$user = $this->getUser();
		if ( !$user->isAllowedAny( 'review' ) ) {
			throw new PermissionsError( 'review' );
		}

		$request = $this->getRequest();
		if ( $request->wasPosted() && $request->getVal( 'action' ) == 'submit' ) {
			$this->doReview();
		} else {
			$this->showForm();
		}
	}


	private function showForm() {
		global $wgImportSources, $wgExportMaxLinkDepth;

		$action = $this->getTitle()->getLocalURL( array( 'action' => 'submit' ) );
		$user = $this->getUser();
		$out = $this->getOutput();

		if ( $user->isAllowed( 'review' ) ) {
			$out->addHTML(
				
					Xml::openElement(
						'form',
						array(
							'enctype' => 'multipart/form-data',
							'method' => 'post',
							'action' => $action,
							'id' => 'mw-review-all-pages-form'
						)
					) .
					
					Html::hidden( 'action', 'submit' ) .
					Xml::submitButton( $this->msg( 'loop-review-all-pages-button' )->text() ) .
					Html::hidden( 'editToken', $user->getEditToken() ) .
					Xml::closeElement( 'form' ) 
					
			);
		}


	}	
	
	private function doReview() {
		global $wgServer;
		$request = $this->getRequest();
		$user = $this->getUser();
		if ( !$user->matchEditToken( $request->getVal( 'editToken' ) ) ) {
			throw new PermissionsError( 'editoken' );
		}
		if ( !$user->isAllowedAny( 'review' ) ) {
			throw new PermissionsError( 'review' );
		}
		$out = $this->getOutput();
		$loop = mb_substr($wgServer,7);
		$cmd='php /opt/www/loop.oncampus.de/mediawiki/reviewAllPages.php '.$loop;
		$result = shell_exec($cmd);
		$result = preg_replace('/\n/', '<br/>', $result);
		$out->addHTML($result.'<br/>');
	}
	
}

?>