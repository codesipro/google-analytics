<?php

namespace SilverStripers\GoogleAnalytics;

use SilverStripers\GoogleAnalytics\GoogleTrackEvent;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Control\Controller;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataExtension;

class GoogleAnalyticsConfigExtension extends DataExtension {

	private static $db = array(
		'GoogleAnalyticsTrackingID' => 'Varchar(50)',
		'GoogleAnalyticsPosition' => 'Enum("Head,Body", "Head")',
		'GoogleAnalyticsTrackDomain' => 'Varchar(200)'
	);

	private static $has_many = array(
		'GoogleTrackEvents'	=> GoogleTrackEvent::class
	);

	function updateCMSFields(FieldList $fields){
		$fields->addFieldsToTab('Root.Integrations.GoogleAnalytics', array(
			TextField::create('GoogleAnalyticsTrackingID'),
			DropdownField::create('GoogleAnalyticsPosition')->setSource(array(
				'Head' => 'Head',
				'Body' => 'Before the closing body tag'
			)),
			TextField::create('GoogleAnalyticsTrackDomain'),
			GridField::create('GoogleTrackEvents', 'GoogleTrackEvents', $this->owner->GoogleTrackEvents(), GridFieldConfig_RelationEditor::create(50))
		));

	}

	public static function CanTrackEvents(Controller $controller){
		$bIsContentController = is_a($controller, ContentController::class);

		if($bIsContentController && SiteConfig::current_site_config()->GoogleAnalyticsTrackingID){
			$strCurrentDomain = str_replace(Director::protocol(), '', Director::protocolAndHost());
			$arrDomains = explode(',', SiteConfig::current_site_config()->GoogleAnalyticsTrackDomain);
			return in_array($strCurrentDomain, $arrDomains);

		}
	}

}
