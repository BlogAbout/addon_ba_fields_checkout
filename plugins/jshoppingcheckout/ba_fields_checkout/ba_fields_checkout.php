<?php
/**
* @version 0.0.5
* @author А.П.В.
* @package ba_fields_checkout for Jshopping
* @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
* @license GNU/GPL
**/
defined('_JEXEC') or die('Restricted access');

class plgJshoppingcheckoutBa_fields_checkout extends JPlugin {
	private $_params;
	
	function __construct($subject, $config) {
		parent::__construct($subject, $config);
		$addon = JTable::getInstance('addon', 'jshop');
		$addon->loadAlias('ba_fields_checkout');
		$this->_params = (object)$addon->getParams();
	}

	function onBeforeDisplayCheckoutStep5View(&$view) {
		if (!$this->_params->enable) {
			return;
		}
		
		$db = JFactory::getDbo();
		$query = "
			SELECT *
			FROM `#__jshopping_fields_checkout_list`
			ORDER BY `ordering`
		";
		$db->setQuery($query);
		$order_fields = $db->loadObjectList();
		
		$content = '';
		$tmpContent = '';
		if ($order_fields) {
			$content .= '<div class="ba_order_fields_wrapper">';
				foreach($order_fields as $f) {
					$content .= '
						<div class="field">
							<label>' . $f->title . '</label>
							<input type="text" name="ba_order_field_' . $f->id . '" value="" onchange="jQuery(\'#ba_order_field_' . $f->id . '\').val(this.value)" />
						</div>
					';
					$tmpContent .= '<input type="hidden" name="ba_order_field_' . $f->id . '" id="ba_order_field_' . $f->id . '" value="" />';
				}
			$content .= '</div>';
		}
		
		$view->_tmp_ext_html_previewfinish_start .= $content;
		$view->_tmp_ext_html_previewfinish_end .= $tmpContent;
	}
	
	function onAfterCreateOrder(&$order) {
		if (!$this->_params->enable) {
			return;
		}

		$db = JFactory::getDbo();
		$query = "
			SELECT *
			FROM `#__jshopping_fields_checkout_list`
			ORDER BY `ordering`
		";
		$db->setQuery($query);
		$order_fields = $db->loadObjectList();
		
		if ($order_fields) {
			foreach($order_fields as $f) {
				$app = JFactory::getApplication();
				$field_value = $app->input->getString('ba_order_field_' . $f->id);
				
				$field_data = new stdClass();
				$field_data->id_order = $order->order_id;
				$field_data->id_field = $f->id;
				$field_data->content = $field_value;
				
				$result = $db->insertObject('#__jshopping_fields_checkout_data', $field_data);
			}
		}
	}
}
?>