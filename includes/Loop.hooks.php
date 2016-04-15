<?php 
class LoopHooks {

	public static function onParserBeforeStrip() {
		global $wgUser, $wgRequest;
		#var_dump($wgRequest);
		#wfDebug( 'LoopLoad'.print_r($wgUser)  );
		return true;
	}


	


	
	
	
}