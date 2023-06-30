<?php
/**
 * @version 0.1.1
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

\JSFactory::loadExtLanguageFile('ba_fields_checkout');

$params = (object)$this->params;
$yes_no_options = array();
$yes_no_options[] = \JHtml::_('select.option', '1', \JText::_('JYES'));
$yes_no_options[] = \JHtml::_('select.option', '0', \JText::_('JNO'));

$field_type = array(
    0 => _JSHOP_BACF_FIELD_TYPE,
    'input' => _JSHOP_BACF_FIELD_TYPE_INPUT,
    'area' => _JSHOP_BACF_FIELD_TYPE_AREA,
    'radio' => _JSHOP_BACF_FIELD_TYPE_RADIO,
    'checkbox' => _JSHOP_BACF_FIELD_TYPE_CHECKBOX,
    'select' => _JSHOP_BACF_FIELD_TYPE_SELECT
);

$db = \JFactory::getDbo();
$query = "
	SELECT *
	FROM `#__jshopping_fields_checkout_list`
	ORDER BY `ordering`
";
$db->setQuery($query);
$fields_list = $db->loadObjectList();

$standart_fields = '
	<tr class="control-dinamic">
		<td>
			<input type="hidden" name="dinamic_field[field_id][]" value="0" />
			<div class="btn-group">
				<a class="remove btn button btn-danger" aria-label="' . _JSHOP_BAFO_FIELD_REMOVE . '"><span class="icon-minus" aria-hidden="true"></span></a>
			</div>
		</td>
		<td>' . \JHtml::_('select.genericlist', $field_type, 'dinamic_field[field_type][]', 'class = "inputbox form-control form-select"', 'value', 'text', 0) . '</td>
        <td><input type="text" name="dinamic_field[title][]" value="" placeholder="' . _JSHOP_BAFO_TITLE . '" title="' . _JSHOP_BAFO_TITLE_DESC . '" class="inputbox form-control form-input" style="width: 210px;" /></td>
	    <td><textarea name="dinamic_field[values_list][]" placeholder="' . _JSHOP_BAFO_VALUES . '" title="' . _JSHOP_BAFO_VALUES_DESC . '" class="inputbox form-control form-textarea"></textarea></td>
		<td>' . \JHtml::_('select.genericlist', $yes_no_options, 'dinamic_field[required[][]', 'class="inputbox form-control form-select form-select-color-state" style="width: 120px;"', 'value', 'text', 0) . '</td>
        <td><input type="number" min="0" step="1" name="dinamic_field[ordering][]" value="" placeholder="' . _JSHOP_BAFO_ORDER . '" title="' . _JSHOP_BAFO_ORDER_DESC . '" class="inputbox form-control form-input" style="width: 100px;" /></td>
    </tr>
';

$style = "
	.jshop_edit .controls {
		display: block;
	}
";

$script = "
	jQuery(function($) {
		$(document)
			.on('click', 'div.ba_custom_fields a.add', function(e) {
				e.preventDefault();
				var new_elem = '" . str_replace("\n", '', $standart_fields) . "';
				$('table.ba_list_fields tbody').append(new_elem);
				$('.control-dinamic select').each(function() {
					$(this).chosen(\"updated\")
				});
			})
			.on('click', '.control-dinamic a.remove', function(e) {
				e.preventDefault();
				var elem = $(this).parents('.control-dinamic');
				elem.remove();
			});
	});
";

\JFactory::getDocument()->addStyleDeclaration($style);
\JFactory::getDocument()->addScriptDeclaration($script);
?>
<fieldset class="form-horizontal">
    <legend><?php echo _JSHOP_BAFO_NAME; ?></legend>

    <div class="control-group">
        <div class="control-label">
            <label class="hasTooltip"
                   title="<?php echo _JSHOP_BAFO_ENABLE_DESC; ?>"><?php echo _JSHOP_BAFO_ENABLE; ?></label>
        </div>

        <div class="controls">
            <?php echo \JHtml::_('select.genericlist', $yes_no_options, 'params[enable]', 'class="inputbox form-control form-select form-select-color-state"', 'value', 'text', (isset($params->enable) ? $params->enable : 1)); ?>
        </div>
    </div>

    <legend><?php echo _JSHOP_BAFO_LIST_FIELDS; ?></legend>

    <table class="admintable ba_list_fields">
        <thead>
        <th></th>
        <th title="<?php echo _JSHOP_BAFO_TYPE_DESC; ?>"><?php echo _JSHOP_BAFO_TYPE; ?></th>
        <th title="<?php echo _JSHOP_BAFO_TITLE_DESC; ?>"><?php echo _JSHOP_BAFO_TITLE; ?></th>
        <th title="<?php echo _JSHOP_BAFO_VALUES_DESC; ?>"><?php echo _JSHOP_BAFO_VALUES; ?></th>
        <th title="<?php echo _JSHOP_BAFO_REQUIRED_DESC; ?>"><?php echo _JSHOP_BAFO_REQUIRED; ?></th>
        <th title="<?php echo _JSHOP_BAFO_ORDER_DESC; ?>"><?php echo _JSHOP_BAFO_ORDER; ?></th>
        </thead>
        <tbody>
        <?php
        if ($fields_list) {
            foreach ($fields_list as $field) {
                ?>
                <tr class="control-dinamic">
                    <td>
                        <input type="hidden" name="dinamic_field[field_id][]"
                               value="<?php echo(isset($field->id) ? $field->id : 0); ?>"
                        />

                        <div class="btn-group">
                            <a class="remove btn button btn-danger"
                               aria-label="<?php echo _JSHOP_BAFO_FIELD_REMOVE; ?>"
                            ><span class="icon-minus" aria-hidden="true"></span></a>
                        </div>
                    </td>

                    <td>
                        <?php echo \JHtml::_('select.genericlist', $field_type, 'dinamic_field[field_type][]', 'class = "inputbox form-control form-select"', 'value', 'text', (isset($field->field_type) ? $field->field_type : 0)); ?>
                    </td>

                    <td>
                        <input type="text"
                               name="dinamic_field[title][]"
                               value="<?php echo(isset($field->title) ? $field->title : ''); ?>"
                               placeholder="<?php echo _JSHOP_BAFO_TITLE; ?>"
                               title="<?php echo _JSHOP_BAFO_TITLE_DESC; ?>"
                               class="inputbox form-control form-input"
                               style="width: 210px;"
                        />
                    </td>

                    <td>
                        <textarea name="dinamic_field[values_list][]"
                                  placeholder="<?php echo _JSHOP_BACF_VALUES; ?>"
                                  title="<?php echo _JSHOP_BACF_VALUES_DESC; ?>"
                                  class="inputbox form-control form-textarea"
                        ><?php echo(isset($field->values_list) ? $field->values_list : ''); ?></textarea>
                    </td>

                    <td>
                        <?php echo \JHtml::_('select.genericlist', $yes_no_options, 'dinamic_field[required][]', 'class="inputbox form-control form-select form-select-color-state" style="width: 120px;"', 'value', 'text', (isset($field->required) ? $field->required : 0)); ?>
                    </td>

                    <td>
                        <input type="number"
                               min="0"
                               step="1"
                               name="dinamic_field[ordering][]"
                               value="<?php echo(isset($field->ordering) ? $field->ordering : 0); ?>"
                               placeholder="<?php echo _JSHOP_BAFO_ORDER; ?>"
                               title="<?php echo _JSHOP_BAFO_ORDER_DESC; ?>"
                               class="inputbox form-control form-input"
                               style="width: 100px;"
                        />
                    </td>
                </tr>
                <?php
            }
        } else {
            echo $standart_fields;
        }
        ?>
        </tbody>
    </table>

    <div class="btn-group ba_custom_fields">
        <a class="add btn button btn-success" aria-label="<?php echo _JSHOP_BAFO_FIELD_ADD; ?>">
            <span class="icon-plus" aria-hidden="true"></span>
        </a>
    </div>

    <input type="hidden" name="params[name_addon]" value="ba_fields_checkout">
</fieldset>