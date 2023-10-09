<?php
/**
 * @version 0.1.3
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

class plgJshoppingorderBa_fields_checkout extends \JPlugin
{
    private $_params;
    private $jshopConfig;

    function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

        $addon = \JSFactory::getTable('addon', 'jshop');
        $addon->loadAlias('ba_fields_checkout');
        $this->_params = (object)$addon->getParams();

        $jshopConfig = \JSFactory::getConfig();
        $this->jshopConfig = $jshopConfig;
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
        $fieldsData = $db->loadObjectList();

        if ($fieldsData) {
            $arrayData = array();
            foreach ($fieldsData as $f) {
                $arrayData[$f->id_field] = $f->content;
            }

            $query = "
				SELECT *
				FROM `#__jshopping_fields_checkout_list`
				ORDER BY `ordering`
			";
            $db->setQuery($query);
            $fieldsLabels = $db->loadObjectList();

            $content = '';
            if ($fieldsLabels) {
                $content .= '
                    <tr>
                        <td style="vertical-align: top; padding-top: 10px; width: 50%">
                            <table style="line-height: 100%; border-spacing: 0; padding: 0; border-collapse: separate;">
                                <tr>
                                    <td colspan="2"><b>Дополнительная информация</b></td>
                                </tr>
				';

                foreach ($fieldsLabels as $field) {
                    $content .= '
						<tr>
							<td style="width: 100%">' . $field->title . ':</td>
							<td>' . $this->renderFieldByType($field, $arrayData[$field->id]) . '</td>
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

    function renderFieldByType($field, $value) {
        if (!$value) {
            return '';
        }

        switch($field->field_type) {
            case 'input':
            case 'area':
            case 'radio':
            case 'checkbox':
            case 'select':
                return $value;
            case 'file':
                return '<a href="' . $this->jshopConfig->live_path . 'files/client_upload/' . $value . '" target="_blank" rel="nofollow noopener noreferrer">' . $value . '</a>';
            default:
                return '';
        }
    }
}