<?php
/**
 * @version 0.1.3
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

class plgJshoppingAdminBa_fields_checkout extends \JPlugin
{
    private $_params;
    private $jshopConfig;

    public function __construct($subject, $config)
    {
        \JSFactory::loadExtLanguageFile('ba_custom_fields');
        parent::__construct($subject, $config);

        $addon = \JSFactory::getTable('addon', 'jshop');
        $addon->loadAlias('ba_fields_checkout');
        $this->_nameAddon = 'ba_fields_checkout';
        $this->_params = (object)$addon->getParams();

        $jshopConfig = \JSFactory::getConfig();
        $this->jshopConfig = $jshopConfig;
    }

    public function onBeforeSaveAddons(&$params, &$post, &$row)
    {
        if (empty($params['name_addon']) || $params['name_addon'] != 'ba_fields_checkout')
            return false;

        $db = \JFactory::getDbo();

        if ($post['dinamic_field']) {
            $dinamicDields = $post['dinamic_field'];

            $query = $db->getQuery(true);
            $query->select('`id`')
                ->from($db->quoteName('#__jshopping_fields_checkout_list'));
            $db->setQuery($query);
            $result = $db->loadColumn();

            $removeFields = array_diff($result, $dinamicDields['field_id']);

            if ($removeFields) {
                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('id') . ' IN (' . implode(',', $removeFields) . ')'
                );
                $query->delete($db->quoteName('#__jshopping_fields_checkout_list'))
                    ->where($conditions);
                $db->setQuery($query);
                $db->execute();

                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('id_field') . ' IN (' . implode(',', $removeFields) . ')'
                );
                $query->delete($db->quoteName('#__jshopping_fields_checkout_data'))
                    ->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }

            foreach ($dinamicDields as $fName => $field) {
                foreach ($field as $key => $value) {
                    $resultFields[$key][$fName] = $value;
                }
            }

            if (isset($resultFields)) {
                foreach ($resultFields as $field) {
                    if ($field['field_type'] == '0') {
                        continue;
                    }

                    $fieldData = new stdClass();
                    $fieldData->field_type = $field['field_type'];
                    $fieldData->title = $field['title'];

                    if (isset($field['ordering'])) {
                        $fieldData->ordering = $field['ordering'];
                    }

                    $fieldData->values_list = $field['values_list'];
                    $fieldData->required = isset($field['required']) ? $field['required'] : 0;

                    if ($field['field_id'] == 0) {
                        $db->insertObject('#__jshopping_fields_checkout_list', $fieldData);
                    } else {
                        $fieldData->id = $field['field_id'];
                        $db->updateObject('#__jshopping_fields_checkout_list', $fieldData, 'id');
                    }

                    $db->setQuery($query);
                    $db->execute();
                }
            }
        } else {
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__jshopping_fields_checkout_list'));
            $db->setQuery($query);
            $db->execute();

            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__jshopping_fields_checkout_data'));
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function onBeforeShowOrder(&$view)
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
            $fields_labels = $db->loadObjectList();

            $content = '
				<tr>
					<td style="vertical-align: top; width: 50%">
						<table style="width: 100%" class="table table-striped">
							<thead>
								<tr>
									<th colspan="2">Дополнительная информация</th>
								</tr>
							</thead>
							<tbody>
			';

            foreach ($fields_labels as $field) {
                $content .= '
					<tr>
						<td style="width: 40%"><b>' . $field->title . ':</b></td>
						<td style="width: 60%">' . $this->renderFieldByType($field, $arrayData[$field->id]) . '</td>
					</tr>
				';
            }

            $content .= '
							</tbody>
						</table>
					</td>
				</tr>
			';

            $view->_tmp_html_after_customer_info = $content;
        }
    }

    function onAfterRemoveOrder($cid)
    {
        $db = \JFactory::getDbo();
        foreach ($cid as $id) {
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('id_order') . ' = ' . $id
            );
            $query->delete($db->quoteName('#__jshopping_fields_checkout_data'))
                ->where($conditions);
            $db->setQuery($query);
            $db->execute();
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