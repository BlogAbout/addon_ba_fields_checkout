<?php
/**
 * @version 0.1.0
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

class plgJshoppingorderBa_fields_checkout extends JPlugin
{
    private $_params;

    function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

        $addon = \JSFactory::getTable('addon', 'jshop');
        $addon->loadAlias('ba_fields_checkout');
        $this->_params = (object)$addon->getParams();
    }

    function onBeforeCreateTemplateOrderPartMail(&$view)
    {
        if (!$this->_params->enable) {
            return;
        }

        $this->fillContentMail($view);
    }

    function onBeforeCreateTemplateOrderMail(&$view)
    {
        if (!$this->_params->enable) {
            return;
        }

        $this->fillContentMail($view);
    }

    function fillContentMail(&$view)
    {
        $db = \JFactory::getDbo();
        $query = "
			SELECT *
			FROM `#__jshopping_fields_checkout_data`
			WHERE `id_order` = " . $view->order->order_id . "
		";
        $db->setQuery($query);
        $fields_data = $db->loadObjectList();

        if ($fields_data) {
            $array_data = array();
            foreach ($fields_data as $f) {
                $array_data[$f->id_field] = $f->content;
            }

            $query = "
				SELECT *
				FROM `#__jshopping_fields_checkout_list`
				ORDER BY `ordering`
			";
            $db->setQuery($query);
            $fields_labels = $db->loadObjectList();

            $content = '';
            if ($fields_labels) {
                $content .= '<tr>
					<td style="vertical-align: top; padding-top: 10px;" width="50%">
						<table cellspacing="0" cellpadding="0" style="line-height: 100%;">
							<tr>
								<td colspan="2"><b>Дополнительная информация</b></td>
							</tr>
				';

                foreach ($fields_labels as $field) {
                    $content .= '
						<tr>
							<td width="100">' . $field->title . ':</td>
							<td>' . $array_data[$field->id] . '</td>
						</tr>
					';
                }

                $content .= '
						</table>
					</td>
				';
            }

            $view->_tmp_ext_html_ordermail_after_customer_info .= $content;
        }
    }
}