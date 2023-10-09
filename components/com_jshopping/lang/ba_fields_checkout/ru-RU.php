<?php
/**
 * @version 0.1.3
 * @author А.П.В.
 * @package ba_fields_checkout for Jshopping
 * @copyright Copyright (C) 2010 blog-about.ru. All rights reserved.
 * @license GNU/GPL
 **/
defined('_JEXEC') or die('Restricted access');

define('_JSHOP_BAFO_NAME', "Дополнительные поля в заказе");
define('_JSHOP_BAFO_ENABLE', "Включить");
define('_JSHOP_BAFO_ENABLE_DESC', "Включить или нет вывод дополнительных полей на странице оформления заказа.");

define('_JSHOP_BAFO_LIST_FIELDS', "Список полей");
define('_JSHOP_BAFO_FIELD_ADD', "Добавить");
define('_JSHOP_BAFO_FIELD_REMOVE', "Удалить");
define('_JSHOP_BAFO_FIELD_REMOVE_ALERT', "Данное действие безвозвратно удалит как само поле, так и все данные, связанные с этим полем, добавленные к заказам ранее.");

define('_JSHOP_BAFO_TYPE', "Тип поля");
define('_JSHOP_BAFO_TYPE_DESC', "Укажите тип поля");
define('_JSHOP_BAFO_TITLE', "Заголовок поля");
define('_JSHOP_BAFO_TITLE_DESC', "Укажите заголовок, который будет отображаться в административной панели и на странице оформления заказа.");
define('_JSHOP_BAFO_ORDER', "Порядок");
define('_JSHOP_BAFO_ORDER_DESC', "Укажите порядковый номер для сортировки поля.");
define('_JSHOP_BAFO_VALUES', "Значения");
define('_JSHOP_BAFO_VALUES_DESC', "Укажите значения списка. Каждое новое значение укажите с новой строки.");
define('_JSHOP_BAFO_REQUIRED', "Обязательно");
define('_JSHOP_BAFO_REQUIRED_DESCR', "Укажите, если необходимо сделать поле обязательным для заполнения");

define('_JSHOP_BAFO_FIELD_TYPE', "Тип поля");
define('_JSHOP_BAFO_FIELD_TYPE_DESC', "Выберите тип поля для отображения");
define('_JSHOP_BAFO_FIELD_TYPE_INPUT', "Поле ввода");
define('_JSHOP_BAFO_FIELD_TYPE_NUMBER', "Число");
define('_JSHOP_BAFO_FIELD_TYPE_TEL', "Телефон");
define('_JSHOP_BAFO_FIELD_TYPE_EMAIL', "E-mail");
define('_JSHOP_BAFO_FIELD_TYPE_LINK', "Ссылка");
define('_JSHOP_BAFO_FIELD_TYPE_AREA', "Область ввода");
define('_JSHOP_BAFO_FIELD_TYPE_EDITOR', "Текстовый редактор");
define('_JSHOP_BAFO_FIELD_TYPE_RADIO', "Кнопки выбора");
define('_JSHOP_BAFO_FIELD_TYPE_CHECKBOX', "Флажки");
define('_JSHOP_BAFO_FIELD_TYPE_SELECT', "Список выбора");
define('_JSHOP_BAFO_FIELD_TYPE_COMBOBOX', "Множественный список выбора");
define('_JSHOP_BAFO_FIELD_TYPE_IMAGE', "Изображение");
define('_JSHOP_BAFO_FIELD_TYPE_GALLERY', "Галерея");
define('_JSHOP_BAFO_FIELD_TYPE_YOUTUBE', "Видео Youtube");
define('_JSHOP_BAFO_FIELD_TYPE_CALENDAR', "Календарь");
define('_JSHOP_BAFO_FIELD_TYPE_FILE', "Файл");

define('_JSHOP_BAFO_NO_TYPE_FIELD', "Не указан тип поля.");
define('_JSHOP_BAFO_NO_VALUES_FIELD', "Не указаны значения поля.");
define('_JSHOP_BAFO_NEED_SELECT', "- Выберите -");

define('_JSHOP_BAFO_BROWSE', "Обзор");

define('_JSHOP_BAFO_ERROR_FIELD_NOT_FOUND', "Не найдено поле загрузки файла в настройках аддона.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_NOT_SELECTED', "Не выбран файл для загрузки.");
define('_JSHOP_BAFO_ERROR_DISABLED_UPLOAD_FILE', "На сервере отключена возможность загрузки файлов.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_SERVER', "Произошла ошибка при загрузке файла на сервер.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_MAX_SIZE', "Загружаемый файл превышает допустимый размер.");
define('_JSHOP_BAFO_ERROR_UPLOAD_FILE_MIME_TYPE', "Загружаемый файл имеет недопустимый тип.");