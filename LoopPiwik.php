<?php 
class Piwik {
	var $piwik_url;
	var $piwik_token_auth;
	
	function Piwik($piwik_url, $piwik_token_auth) {
		$this->piwik_url = $piwik_url;
		$this->piwik_token_auth = $piwik_token_auth;
	}
	
	function getSiteId($url) {
		
		if (!(substr($url,0,7) === 'http://')) {
			$url = 'http://'.$url;			
		}
		
		$piwikrest = $this->piwik_url."?module=API";
		$piwikrest .= "&method=SitesManager.getSitesIdFromSiteUrl&url=".urlencode($url);
		$piwikrest .= "&format=php&token_auth=".$this->piwik_token_auth;
		
		$fetched = file_get_contents($piwikrest);
		$content = unserialize($fetched);

		if (isset($content[0]['idsite'])) {
			return $content[0]['idsite'];
		} else {
			return false;
		}
	}
	
	function getTrackingCode($idsite) {
		/*
		$piwikrest = $this->piwik_url."?module=API";
		$piwikrest .= "&method=SitesManager.getJavascriptTag";
		$piwikrest .= "&idSite=".$idsite;
		$piwikrest .= "&piwikUrl=http://noc.oncampus.de/piwik";
		$piwikrest .= "&format=php&token_auth=".$this->piwik_token_auth;		

		$fetched = file_get_contents($piwikrest);
		#$trackingcode = unserialize($fetched);
		$trackingcode = html_entity_decode(urldecode(unserialize($fetched)), ENT_QUOTES);
		*/
		$trackingcode='<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["setCookieDomain", "*.'.$_SERVER["SERVER_NAME"].'"]);
  _paq.push(["setDoNotTrack", true]);
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u="'.$this->piwik_url.'";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "'.$idsite.'"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="'.$this->piwik_url.'piwik.php?idsite='.$idsite.'" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Code -->';
		
		
		return $trackingcode;
		
	}
	
	
	function addSite($url, $group='LOOPs') {
		if (!(substr($url,0,7) === 'http://')) {
			$url = 'http://'.$url;			
		}
		$url_parts=explode('.',substr($url,7));
		$siteName='LOOP '.$url_parts[0];

		$piwikrest = $this->piwik_url."?module=API";
		$piwikrest .= "&method=SitesManager.addSite";
		$piwikrest .= "&siteName=".urlencode($siteName);
		$piwikrest .= "&urls=".urlencode($url);
		$piwikrest .= "&ecommerce=0";
		$piwikrest .= "&siteSearch=1";
		$piwikrest .= "&searchKeywordParameters=q,query,s,search,searchword,k,keyword";
		$piwikrest .= "&timezone=".urlencode("Europe/Berlin");
		$piwikrest .= "&currency=EUR";
		$piwikrest .= "&group=".urlencode($group);
		$piwikrest .= "&format=php&token_auth=".$this->piwik_token_auth;		

		$fetched = file_get_contents($piwikrest);
		$idsite = unserialize($fetched);
		
		return $idsite;
	}
	
	
}
?>