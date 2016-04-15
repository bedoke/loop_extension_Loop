<?php 
class Loop {
	public static function onExtensionLoad() {
		global $wgUser, $wgRequest;
		#var_dump($wgRequest);
		#wfDebug( 'LoopLoad'.print_r($wgUser)  );
	}
}