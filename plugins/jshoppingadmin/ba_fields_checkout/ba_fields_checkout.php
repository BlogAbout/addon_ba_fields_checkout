<?php
/**
 * @version 0.1.1
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

class plgJshoppingAdminBa_fields_checkout extends JPlugin
{
    private $_params;

    public function __construct($subject, $config)
    {
        \JSFactory::loadExtLanguageFile('ba_custom_fields');
        parent::__construct($subject, $config);

        $addon = \JSFactory::getTable('addon', 'jshop');
        $addon->loadAlias('ba_fields_checkout');
        $this->_nameAddon = 'ba_fields_checkout';
        $this->_params = (object)$addon->getParams();
    }

    public function onBeforeSaveAddons(&$params, &$post, &$row)
    {
        if (empty($params['name_addon']) || $params['name_addon'] != 'ba_fields_checkout')
            return false;

        $db = \JFactory::getDbo();

        if ($post['dinamic_field']) {
            $dinamic_fields = $post['dinamic_field'];

            $query = $db->getQuery(true);
            $query->select('`id`')
                ->from($db->quoteName('#__jshopping_fields_checkout_list'));
            $db->setQuery($query);
            $result = $db->loadColumn();

            $remove_fields = array_diff($result, $dinamic_fields['field_id']);

            if ($remove_fields) {
                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('id') . ' IN (' . implode(',', $remove_fields) . ')'
                );
                $query->delete($db->quoteName('#__jshopping_fields_checkout_list'))
                    ->where($conditions);
                $db->setQuery($query);
                $db->execute();

                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('id_field') . ' IN (' . implode(',', $remove_fields) . ')'
                );
                $query->delete($db->quoteName('#__jshopping_fields_checkout_data'))
                    ->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }

            foreach ($dinamic_fields as $fname => $field) {
                foreach ($field as $key => $value) {
                    $result_fields[$key][$fname] = $value;
                }
            }

            foreach ($result_fields as $field) {
                if ($field['field_type'] == '0') {
                    continue;
                }

                $field_data = new stdClass();
                $field_data->field_type = $field['field_type'];
                $field_data->title = $field['title'];

                if (isset($field['ordering'])) {
                    $field_data->ordering = $field['ordering'];
                }

                $field_data->values_list = $field['values_list'];
                $field_data->required = isset($field['required']) ? $field['required'] : 0;

                if ($field['field_id'] == 0) {
                    $db->insertObject('#__jshopping_fields_checkout_list', $field_data);
                    $field_id = $db->insertid();
                    $alter_text = 'ADD';
                } else {
                    $field_data->id = $field_id = $field['field_id'];
                    $db->updateObject('#__jshopping_fields_checkout_list', $field_data, 'id');
                    $alter_text = 'MODIFY COLUMN';
                }

                $db->setQuery($query);
                $db->execute();
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

            $content = '
				<tr>
					<td width="50%" valign="top">
						<table width="100%" class="table table-striped">
							<thead>
								<tr>
									<th colspan="2" align="center">Дополнительная информация</th>
								</tr>
							</thead>
							<tbody>
			';
            foreach ($fields_labels as $field) {
                $content .= '
					<tr>
						<td width="40%"><b>' . $field->title . ':</b></td>
						<td width="60%">' . $array_data[$field->id] . '</td>
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
}