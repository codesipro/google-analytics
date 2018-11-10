<?php

namespace SilverStripers\GoogleAnalytics;

use DataExtension;
use FieldList;
use TextField;
use DropdownField;
use GridField;
use GridFieldConfig_RelationEditor;
use Controller;
use SiteConfig;
use Director;



class GoogleAnalyticsConfigExtension extends DataExtension {

	private static $db = array(
		'GoogleAnalyticsTrackingID' 		=> 'Varchar(50)',
		'GoogleAnalyticsPosition'			=> 'Enum("Head,Body", "Head")',
		'GoogleAnalyticsTrackDomain'		=> 'Varchar(200)'
	);

	private static $has_many = array(
		'GoogleTrackEvents'					=> 'GoogleTrackEvent'
	);

	function updateCMSFields(FieldList $fields){
		$fields->addFieldsToTab('Root.Integrations.GoogleAnalytics', array(
			TextField::create('GoogleAnalyticsTrackingID'),
			DropdownField::create('GoogleAnalyticsPosition')->setSource(array(
				'Head'		=> 'Head',
				'Body'		=> 'Before the closing body tag'
			)),
			TextField::create('GoogleAnalyticsTrackDomain'),
			new GridField('GoogleTrackEvents', 'GoogleTrackEvents', $this->owner->GoogleTrackEvents(), new GridFieldConfig_RelationEditor(50))
		));

	}


	public static function CanTrackEvents(Controller $controller){
		$bIsContentController = is_a($controller, 'ContentController');

		if($bIsContentController && SiteConfig::current_site_config()->GoogleAnalyticsTrackingID){
			$strCurrentDomain = str_replace(Director::protocol(), '', Director::protocolAndHost());
			$arrDomains = explode(',', SiteConfig::current_site_config()->GoogleAnalyticsTrackDomain);
			return in_array($strCurrentDomain, $arrDomains);

		}
	}

} 