<?php
/**
* @version 0.1.0
* @author А.П.В.
* @package ba_fields_checkout for Jshopping
* @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
* @license GNU/GPL
**/
defined('_JEXEC') or die('Restricted access');

$name = 'JoomShopping addon - Fields Checkout';
$type = 'plugin';
$element = 'ba_fields_checkout';
$folders = array('jshoppingadmin', 'jshoppingcheckout', 'jshoppingorder');
$version = '0.1.0';
$cache = '{"creationDate":"30.04.2021","author":"Blog About","authorEmail":"info@blog-about.ru","authorUrl":"https://blog-about.ru","version":"' . $version.'"}';
$params = '{}';

$db = \JFactory::getDbo();

foreach($folders as $folder) {
	$db->setQuery("
		SELECT `extension_id`
		FROM `#__extensions`
		WHERE `element` = '$element' AND `folder` = '$folder'
	");

	$id = $db->loadResult();

	if (!$id) {
		$query = "
			INSERT
				INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`)
				VALUES ('$name', '$type', '$element', '$folder', 0, 1, 1, 0, '" . addslashes($cache) . "', '" . addslashes($params) . "')
		";
	} else {
		$query = "
			UPDATE `#__extensions`
			SET
				`name` = '$name',
				`manifest_cache` = '" . addslashes($cache) . "',
				`params` = '" . addslashes($params) . "'
			WHERE `extension_id` = $id
		";
	}

	$db->setQuery($query);
	$db->execute();
}

$query = "
	CREATE TABLE IF NOT EXISTS `#__jshopping_fields_checkout_list` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`ordering` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
";
$db->setQuery($query);
$db->execute();

$query = "
	CREATE TABLE IF NOT EXISTS `#__jshopping_fields_checkout_data` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`id_order` int(11) unsigned NOT NULL,
		`id_field` int(11) unsigned NOT NULL,
		`content` varchar(300) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
";
$db->setQuery($query);
$db->execute();

$addon = \JSFactory::getTable('addon', 'jshop');
$addon->loadAlias($element);
$addon->set('name', $name);
$addon->set('version', $version);
$addon->set('uninstall', '/components/com_jshopping/addons/' . $element . '/uninstall.php');
$addon->store();