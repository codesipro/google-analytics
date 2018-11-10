<?php

namespace SilverStripers\GoogleAnalytics;

use Extension;
use Director;
use SiteConfig;
use Requirements;

/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 2/3/15
 * Time: 4:31 PM
 * To change this template use File | Settings | File Templates.
 */

class GoogleAnalyticsControllerExtension extends Extension {


	function onAfterInit(){

		if(GoogleAnalyticsConfigExtension::CanTrackEvents($this->owner)){

			$this->IncludeGATrackingCode();
			$this->IncludeTrackingEvents();


		}


	}


	function IncludeGATrackingCode(){
		$strCurrentDomain = str_replace(Director::protocol(), '', Director::protocolAndHost());
		$strID = SiteConfig::current_site_config()->GoogleAnalyticsTrackingID;
		$strCode = <<<JS
	var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '{$strID}']);
    _gaq.push(['_setDomainName', '{$strCurrentDomain}']);
    _gaq.push(['_setAllowLinker', true]);
    _gaq.push(['_trackPageview']);
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

JS;

		if(SiteConfig::current_site_config()->GoogleAnalyticsPosition == 'Head'){
			Requirements::insertHeadTags('<script>' . $strCode . '</script>', 'GA_TRACKING_CODE');
		}
		else{
			Requirements::customScript($strCode, 'GA_TRACKING_CODE');
		}

	}

	function IncludeTrackingEvents(){

		$page = $this->owner->data();
		$events = SiteConfig::current_site_config()->GoogleTrackEvents()->where('EXISTS ( SELECT 1
			FROM GoogleTrackEvent_Pages gep
			WHERE gep.GoogleTrackEventID = GoogleTrackEvent.ID
				AND gep.SiteTreeID = ' . $page->ID . '
			LIMIT 1 )');


		if($events->count()){
			$strCode = "";
			Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
			Requirements::javascript(THIRDPARTY_DIR . '/jquery-livequery/jquery.livequery.js');

			foreach($events as $event){



				$strEvent = 'click';
				if($event->EventType == 'Hover'){
					$strEvent = 'mouseenter';
				}

				$strCategory = addslashes($event->Category);
				$strAction = addslashes($event->Action);
				$strLabel = addslashes($event->Label);

				$strCode.= <<<JS
(function(){
	$("{$event->Target}").livequery('{$strEvent}', function(){
		if(typeof _gaq !== 'undefined'){
			_gaq.push(['_trackEvent', '{$strCategory}', '{$strAction}', '{$strLabel}']);
		}
	});
})(jQuery);
JS;



			}

			if(SiteConfig::current_site_config()->GoogleAnalyticsPosition == 'Head'){
				Requirements::insertHeadTags('<script>' . $strCode . '</script>', 'GA_TRACKERS');
			}
			else{
				Requirements::customScript($strCode, 'GA_TRACKERS');
			}

		}
	}

} 