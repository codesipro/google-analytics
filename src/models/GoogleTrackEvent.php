<?php

namespace Silverstripers\GoogleAnalytics\models;

use Silverstripe\ORM\DataObject;
use Silverstripe\Forms\TreeMultiselectField;

/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 2/3/15
 * Time: 4:44 PM
 * To change this template use File | Settings | File Templates.
 */

class GoogleTrackEvent extends DataObject {

	private static $db = array(
		'Target'			=> 'Varchar(300)',
		'EventType'			=> 'Enum("Click,Hover", "Click")',
		'Category'			=> 'Varchar(100)',
		'Action'			=> 'Varchar(100)',
		'Label'				=> 'Varchar(100)'
	);

	private static $has_one = array(
		'SiteConfig'		=> 'SiteConfig'
	);

	private static $many_many = array(
		'Pages'				=> 'SiteTree'
	);

	private static $summary_fields = array(
		'Target',
		'EventType',
		'Category',
		'Action',
		'Label'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();

		$fields->removeByName('SiteConfigID');
		if($targetField = $fields->dataFieldByName('Target')){
			$targetField->setRightTitle('ID or CSS class to find the dom element');
		}

		$fields->removeByName('Pages');
		$fields->addFieldToTab('Root.Main', TreeMultiselectField::create('Pages', 'Select Pages (leave empty for all the pages)')->setSourceObject('SiteTree'));


		return $fields;
	}

}
