<?php
/**
* @version 0.0.5
* @author А.П.В.
* @package ba_fields_checkout for Jshopping
* @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
* @license GNU/GPL
**/
defined('_JEXEC') or die('Restricted access');

$db = JFactory::getDbo();

$type = 'plugin';
$element = 'ba_fields_checkout';
$folders = array('jshoppingadmin', 'jshoppingcheckout', 'jshoppingorder');

foreach($folders as $folder){
	$db->setQuery("
		DELETE FROM `#__extensions`
		WHERE `element` = '" . $element . "' AND `folder` = '" . $folder . "' AND `type` = '" . $type . "'");
	$db->query();
}

$db->setQuery("
	DROP TABLE `#__jshopping_fields_checkout_list`
");
$db->query();

$db->setQuery("
	DROP TABLE `#__jshopping_fields_checkout_data`
");
$db->query();

jimport('joomla.filesystem.folder');
JFolder::delete(JPATH_ROOT . '/components/com_jshopping/addons/ba_fields_checkout/');
JFolder::delete(JPATH_ROOT . '/components/com_jshopping/lang/ba_fields_checkout/');
JFolder::delete(JPATH_ROOT . '/plugins/jshoppingadmin/ba_fields_checkout/');
JFolder::delete(JPATH_ROOT . '/plugins/jshoppingcheckout/ba_fields_checkout/');
JFolder::delete(JPATH_ROOT . '/plugins/jshoppingorder/ba_fields_checkout/');
?>