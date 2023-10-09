<?php
/**
 * @version 0.1.2
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

        \JSFactory::loadExtLanguageFile('ba_fields_checkout');
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
        $orderFields = $db->loadObjectList();

        $content = '';
        $tmpContent = '';
        $hasFieldFile = false;
        if ($orderFields) {
            foreach ($orderFields as $f) {
                if ($f->field_type === 'file') {
                    $hasFieldFile = true;
                    break;
                }
            }

            $content .= '<div class="ba_order_fields_wrapper">';
            foreach ($orderFields as $f) {
                $content .= '
                    <div class="field">
                        <label>' . $f->title . '</label>
                        ' . $this->renderFieldByType($f) . '
                    </div>
                ';
                $tmpContent .= '<input type="hidden" '. ($f->required === 1 ? 'data-required' : '') . ' name="ba_order_field_' . $f->id . '" id="ba_order_field_' . $f->id . '" value="" />';
            }
            $content .= '</div>';
        }

        if ($hasFieldFile) {
            $ajaxUrl = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=bafieldscheckout&task=uploadFile&ajax=1');

            $script = <<<EOF
<style>
.ba-fc-input-file {
    display: flex;
    width: 100%;
    flex-wrap: wrap;
}
.ba-fc-input-file .input-button-upload,
.ba-fc-input-file .input-button-delete {
    margin-bottom: 3px;
    margin-right: 5px;
    border: 1px solid #000;
    padding: 0 5px;
    height: 24px;
    font-size: 14px;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s ease-in;
    display: flex;
    align-items: center;
}
.ba-fc-input-file .input-button-upload:hover,
.ba-fc-input-file .input-button-delete:hover {
    opacity: 0.7;
}
.ba-fc-input-file .input-button-upload {
    background-color: #112855;
}
.ba-fc-input-file .button-file-name {
    margin-left: 5px;
    font-size: 11px;
}
.ba-fc-input-file .input-button-delete {
    background-color: #a51f18;
}
.ba-fc-input-file .input-button-delete span {
    font-size: 11px;
    line-height: 11px;
    display: block;
}
</style>
<script>
jQuery(function($) {
    $(document)
        .on('click', '.ba-fc-input-file .input-button-delete', function() {
            const fieldId = $(this).parent().data('id')
            
            $('input#ba_order_field_' + fieldId).val('')
            $(this).css('display', 'none')
            $(this).siblings('.input-button-upload').children('.button-file-name').css('display', 'none').text('')
            $(this).siblings('input[type="file"]').val('')
        })
        .on('click', '.ba-fc-input-file .input-button-upload', function() {
            $(this).siblings('input[type="file"]').trigger('click')
        })
        .on('change', '.ba-fc-input-file input[type="file"]', function() {
            const files = this.files
            
            if (typeof files == 'undefined') {
                return
            }
            
            const button = $(this).siblings('.input-button-upload')
            const fieldId = $(button).parent().data('id')
            const data = new FormData()
            $.each(files, function(key, value) {
                data.append('file', value);
            })
            
            data.append('fieldId', fieldId)
            
            $.ajax({
                type: 'POST',
                url: '$ajaxUrl',
                dataType: 'json',
                cache: false,
                data: data,
                processData: false,
                contentType: false,
                success: function(data, statusText, xhr) {
                    if (data.hasOwnProperty('success')) {
                        $(button).siblings('.input-button-delete').css('display', 'flex')
                        $(button).children('.button-file-name').css('display', 'block').text('(' + data.success + ')')
                        $('input#ba_order_field_' + fieldId).val(data.success)
                    } else if (data.hasOwnProperty('error')) {
                        alert(data.error)
                        $(button).siblings('.input-button-delete').trigger('click')
                    }
                },
                error: function(jqXHR, status, errorThrown){
                    console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
                    alert('Ошибка загрузки файла. Попробуйте другой файл')
                    $(button).siblings('.input-button-delete').trigger('click')
                }
            });
        })
})
</script>
EOF;
            $content = $script . $content;
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
        $orderFields = $db->loadObjectList();

        if ($orderFields) {
            foreach ($orderFields as $f) {
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

    function renderFieldByType($field)
    {
        $fieldHtml = '';
        $fieldName = 'ba_order_field_' . $field->id;

        switch ($field->field_type) {
            case 'input':
            {
                $fieldHtml = '
                    <div class="ba-fc-input">
                        <input type="text" class="inputbox w100" name="' . $fieldName . '" value="" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)" />
                    </div>
                ';
                break;
            }
            case 'area':
            {
                $fieldHtml = '
                    <div class="ba-fc-input">
                        <textarea name="' . $fieldName . '" class="wide" rows="5" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)"></textarea>
                    </div>
                ';
                break;
            }
            case 'radio':
            {
                if ($field->values_list != '') {
                    $values_list = explode("\n", $field->values_list);

                    $fieldHtml .= '<div class="ba-fc-input">';
                    foreach ($values_list as $f_v) {
                        $fieldHtml .= '
                            <label>
                                <input type="radio" name="' . $fieldName . '" value="' . trim($f_v) . '" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)"/> ' . trim($f_v) . '
                            </label>
                            <br />
                        ';
                    }
                    $fieldHtml .= '</div>';
                } else {
                    $fieldHtml = _JSHOP_BAFO_NO_VALUES_FIELD;
                }
                break;
            }
            case 'checkbox':
            {
                if ($field->values_list != '') {
                    $values_list = explode("\n", $field->values_list);

                    $fieldHtml .= '<div class="ba-fc-input">';
                    foreach ($values_list as $f_v) {
                        $fieldHtml .= '
                            <label>
                                <input type="checkbox"
                                       name="' . $fieldName . '[]"
                                       value="' . trim($f_v) . '"
                                       data-id="' . $fieldName . '"
                                       onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val($(\'input[data-id=' . $fieldName . ']:checked\').map(function(){return $(this).val()}).get().join(\', \'))"
                                /> ' . trim($f_v) . '
                            </label>
                            <br />
                        ';
                    }
                    $fieldHtml .= '</div>';
                } else {
                    $fieldHtml = _JSHOP_BAFO_NO_VALUES_FIELD;
                }
                break;
            }
            case 'select':
            {
                if ($field->values_list != '') {
                    $values_list = explode("\n", $field->values_list);
                    $fieldHtml .= '<div class="ba-fc-input">';
                    $fieldHtml .= '<select name="' . $fieldName . '" class="inputbox" onchange="jQuery(\'#ba_order_field_' . $field->id . '\').val(this.value)">';
                    $fieldHtml .= '<option value="">' . _JSHOP_BAFO_NO_VALUES_FIELD . '</option>';
                    foreach ($values_list as $f_v) {
                        $fieldHtml .= '<option value="' . trim($f_v) . '">' . trim($f_v) . '</option>';
                    }
                    $fieldHtml .= '</select>';
                    $fieldHtml .= '</div>';
                } else {
                    $fieldHtml = _JSHOP_BAFO_NO_VALUES_FIELD;
                }
                break;
            }
            case 'file':
            {
                $accept = null;
                if ($field->values_list) {
                    $fieldParams = explode("\n", $field->values_list);
                    if (isset($fieldParams) && count($fieldParams)) {
                        foreach ($fieldParams as $param) {
                            $paramItem = explode('=', $param);

                            if ($paramItem[0] === 'accept') {
                                $accept = $paramItem[1];
                            }
                        }
                    }
                }

                $fieldHtml = '
                    <div class="ba-fc-input-file" data-id="' . $field->id . '">
                        <input type="file" style="display: none;" name="' . $fieldName . '" value="" ' . ($accept ? 'accept=' . $accept : '') . ' />
                        <div class="input-button-upload">
                            <span>' . _JSHOP_BAFO_BROWSE . '</span>
                            <span class="button-file-name" style="display: none;"></span>
                        </div>
                        <div class="input-button-delete" style="display: none;"><span>X</span></div>
                    </div>
                ';
                break;
            }
            default:
            {
                $fieldHtml = _JSHOP_BAFO_NO_TYPE_FIELD;
                break;
            }
        }

        return $fieldHtml;
    }
}