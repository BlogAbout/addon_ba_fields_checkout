<?php
/**
 * @version 0.1.1
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

class plgJshoppingcheckoutBa_fields_checkout extends JPlugin
{
    private $_params;

    function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

        $addon = \JSFactory::getTable('addon', 'jshop');
        $addon->loadAlias('ba_fields_checkout');
        $this->_params = (object)$addon->getParams();
    }

    function onBeforeDisplayCheckoutStep5View(&$view)
    {
        if (!$this->_params->enable) {
            return;
        }

        $db = \JFactory::getDbo();
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
            foreach ($order_fields as $f) {
                $content .= '
						<div class="field">
							<label>' . $f->title . '</label>
							' . $this->generate_html_field($f) . '
						</div>
					';
                $tmpContent .= '<input type="hidden" name="ba_order_field_' . $f->id . '" id="ba_order_field_' . $f->id . '" value="" />';
            }
            $content .= '</div>';
        }

        $view->_tmp_ext_html_previewfinish_start .= $content;
        $view->_tmp_ext_html_previewfinish_end .= $tmpContent;
    }

    function onAfterCreateOrder(&$order)
    {
        if (!$this->_params->enable) {
            return;
        }

        $db = \JFactory::getDbo();
        $query = "
			SELECT *
			FROM `#__jshopping_fields_checkout_list`
			ORDER BY `ordering`
		";
        $db->setQuery($query);
        $order_fields = $db->loadObjectList();

        if ($order_fields) {
            foreach ($order_fields as $f) {
                $app = \JFactory::getApplication();
                $field_value = $app->input->getString('ba_order_field_' . $f->id);

                $field_data = new stdClass();
                $field_data->id_order = $order->order_id;
                $field_data->id_field = $f->id;
                $field_data->content = $field_value;

                $db->insertObject('#__jshopping_fields_checkout_data', $field_data);
            }
        }
    }

    function generate_html_field($field)
    {
        $jshopConfig = \JSFactory::getConfig();
        $field_html = '';
        $field_name = 'ba_order_field_' . $field->id;

        //							<input type="text" name="ba_order_field_' . $f->id . '" value="" onchange="jQuery(\'#ba_order_field_' . $f->id . '\').val(this.value)" />
        switch ($field->field_type) {
            case 'input':
            {
                $field_html = '<input type="text" class="inputbox w100" name="' . $field_name . '" value="" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)" />';
                break;
            }
            case 'area':
            {
                $field_html = '<textarea name="' . $field_name . '" class="wide" rows="5" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)"></textarea>';
                break;
            }
            case 'radio':
            {
                if ($field->values_list != '') {
                    $values_list = explode("\n", $field->values_list);
                    foreach ($values_list as $f_v) {
                        $field_html .= '
                            <label>
                                <input type="radio" name="' . $field_name . '" value="' . trim($f_v) . '" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)"/> ' . trim($f_v) . '
                            </label>
                            <br />
                        ';
                    }
                } else {
                    $field_html = _JSHOP_BAFO_NO_VALUES_FIELD;
                }
                break;
            }
            case 'checkbox':
            {
                if ($field->values_list != '') {
                    $values_list = explode("\n", $field->values_list);
                    foreach ($values_list as $f_v) {
                        $field_html .= '
                            <label>
                                <input type="checkbox"
                                       name="' . $field_name . '[]"
                                       value="' . trim($f_v) . '"
                                       data-id="' . $field_name . '"
                                       onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val($(\'input[data-id=' . $field_name . ']:checked\').map(function(){return $(this).val()}).get().join(\', \'))"
                                /> ' . trim($f_v) . '
                            </label>
                            <br />
                        ';
                    }
                } else {
                    $field_html = _JSHOP_BAFO_NO_VALUES_FIELD;
                }
                break;
            }
            case 'select':
            {
                if ($field->values_list != '') {
                    $values_list = explode("\n", $field->values_list);
                    $field_html = '<select name="' . $field_name . '" class="inputbox" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)">';
                    $field_html .= '<option value="">' . _JSHOP_BAFO_NO_VALUES_FIELD . '</option>';
                    foreach ($values_list as $f_v) {
                        $field_html .= '<option value="' . trim($f_v) . '">' . trim($f_v) . '</option>';
                    }
                    $field_html .= '</select>';
                } else {
                    $field_html = _JSHOP_BAFO_NO_VALUES_FIELD;
                }
                break;
            }
            default:
            {
                $field_html = _JSHOP_BAFO_NO_TYPE_FIELD;
                break;
            }
        }

        return $field_html;
    }
}