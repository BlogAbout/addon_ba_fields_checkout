<?php
/**
 * @version 0.1.3
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

define('_JSHOP_BAFO_NAME', "Custom order fields");
define('_JSHOP_BAFO_ENABLE', "Enable");
define('_JSHOP_BAFO_ENABLE_DESC', "Enable or not the output of additional order fields.");

define('_JSHOP_BAFO_LIST_FIELDS', "List of fields");
define('_JSHOP_BAFO_FIELD_ADD', "Add");
define('_JSHOP_BAFO_FIELD_REMOVE', "Delete");
define('_JSHOP_BAFO_FIELD_REMOVE_ALERT', "This action will permanently delete both the field itself and all data associated with this field added to the order earlier.");

define('_JSHOP_BAFO_TYPE', "Type field");
define('_JSHOP_BAFO_TYPE_DESCRIPTION', "Select the type of field to display");
define('_JSHOP_BAFO_TITLE', "Title field");
define('_JSHOP_BAFO_TITLE_DESC', "Specify the title that will be displayed in the administrative panel and on order page.");
define('_JSHOP_BAFO_ORDER', "Order");
define('_JSHOP_BAFO_ORDER_DESC', "Specify the serial number to sort the field.");
define('_JSHOP_BAFO_VALUES', "Values");
define('_JSHOP_BAFO_VALUES_DESC', "Specify list values. Specify each new value on a new line.");
define('_JSHOP_BAFO_REQUIRED', "Required");
define('_JSHOP_BAFO_REQUIRED_DESC', "Specify, if need field as required.");

define('_JSHOP_BAFO_FIELD_TYPE', "Field type");
define('_JSHOP_BAFO_FIELD_TYPE_DESC', "Select the type of field to display.");
define('_JSHOP_BAFO_FIELD_TYPE_INPUT', "Input");
define('_JSHOP_BAFO_FIELD_TYPE_NUMBER', "Number");
define('_JSHOP_BAFO_FIELD_TYPE_TEL', "Phone");
define('_JSHOP_BAFO_FIELD_TYPE_EMAIL', "E-mail");
define('_JSHOP_BAFO_FIELD_TYPE_LINK', "Link");
define('_JSHOP_BAFO_FIELD_TYPE_AREA', "Textarea");
define('_JSHOP_BAFO_FIELD_TYPE_EDITOR', "Text Editor");
define('_JSHOP_BAFO_FIELD_TYPE_RADIO', "Radiobutton");
define('_JSHOP_BAFO_FIELD_TYPE_CHECKBOX', "Checkbox");
define('_JSHOP_BAFO_FIELD_TYPE_SELECT', "Select");
define('_JSHOP_BAFO_FIELD_TYPE_COMBOBOX', "Combobox");
define('_JSHOP_BAFO_FIELD_TYPE_IMAGE', "Image");
define('_JSHOP_BAFO_FIELD_TYPE_GALLERY', "Gallery");
define('_JSHOP_BAFO_FIELD_TYPE_YOUTUBE', "Video Youtube");
define('_JSHOP_BAFO_FIELD_TYPE_CALENDAR', "Calendar");
define('_JSHOP_BAFO_FIELD_TYPE_FILE', "File");

define('_JSHOP_BAFO_NO_TYPE_FIELD', "Field type not specified.");
define('_JSHOP_BAFO_NO_VALUES_FIELD', "No field values specified.");
define('_JSHOP_BAFO_NEED_SELECT', "- Select -");

define('_JSHOP_BAFO_BROWSE', "Browse");

define('_JSHOP_BAFO_ERROR_FIELD_NOT_FOUND', "The file upload field was not found in the addon settings.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_NOT_SELECTED', "Upload file not selected.");
define('_JSHOP_BAFO_ERROR_DISABLED_UPLOAD_FILE', "The installer can't continue until file uploads are enabled for the server.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_SERVER', "There was an error uploading this file to the server.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_MAX_SIZE', "The uploaded file exceeds the allowed size.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_MIME_TYPE', "The uploaded file has an invalid type");