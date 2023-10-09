<?php

namespace Joomla\Component\Jshopping\Site\Controller;

defined('_JEXEC') or die();

class BafieldscheckoutController extends BaseController
{
    function __construct($config = array())
    {
        parent::__construct($config);

        \JSFactory::loadExtLanguageFile('ba_fields_checkout');
    }

    public function uploadFile()
    {
        try {
            $fieldId = \JFactory::getApplication()->input->get('fieldId');

            if (!$fieldId || !(int)$fieldId) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_FIELD_NOT_FOUND]);
                die;
            }

            $files = \JFactory::getApplication()->input->files->get('file', null, 'raw');

            if (!(bool)ini_get('file_uploads')) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_DISABLED_UPLOAD_FILE]);
                die;
            }

            if (!is_array($files)) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_UPLOAD_FILE_NOT_SELECTED]);
                die;
            }
            if ($files['error'] || $files['size'] < 1) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_UPLOAD_FILE_SERVER]);
                die;
            }

            $fileNames = \JFile::makeSafe($files['name']);
            $extension = \JFile::getExt($fileNames);
            $fileName = md5(\JFile::stripExt($fileNames) . microtime()) . '.' . $extension;
            $fileSize = $files['size'];
            $mimeType = $files['type'];

            $allowAccept = null;
            $allowSize = 5;

            $db = \JFactory::getDbo();
            $query = "
                SELECT *
                FROM `#__jshopping_fields_checkout_list`
                WHERE `id` = " . (int)$fieldId . " AND `field_type` = 'file'
            ";
            $db->setQuery($query);
            $field = $db->loadObject();

            if (!$field) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_FIELD_NOT_FOUND]);
                die;
            }

            if ($field->values_list) {
                $fieldParams = explode("\n", $field->values_list);

                if (isset($fieldParams) && count($fieldParams)) {
                    foreach ($fieldParams as $param) {
                        $paramItem = explode('=', $param);
                        if (!$paramItem[1]) {
                            continue;
                        }

                        $value = str_replace('"', '', trim($paramItem[1]));

                        if ($paramItem[0] === 'accept' && $value !== '') {
                            $allowAccept = $value;
                        } else if ($paramItem[0] === 'size' && (int)$value) {
                            $allowSize = (int)$value;
                        }
                    }
                }
            }

            if ($fileSize > $allowSize * 1024 * 1024) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_UPLOAD_FILE_MAX_SIZE]);
                die;
            }

            if ($allowAccept) {
                $isAllow = false;
                $types = explode(',', $allowAccept);

                foreach ($types as $type) {
                    $groups = explode('/', trim($type));

                    if ($groups[1]) {
                        if ($groups[1] === '*') {
                            if (explode('/', $mimeType)[0] === $groups[0]) {
                                $isAllow = true;
                                break;
                            }
                        } else {
                            if (trim($type) === $mimeType) {
                                $isAllow = true;
                                break;
                            }
                        }
                    }
                }

                if (!$isAllow) {
                    echo json_encode(['error' => _JSHOP_BAFO_ERROR_UPLOAD_FILE_MIME_TYPE]);
                    die;
                }
            }

            $tmpSrc = $files['tmp_name'];

            $config = \JSFactory::getConfig();
            $directoryDest = $config->path . 'files/client_upload/';

            if (!\JFolder::exists($directoryDest)) {
                \JFolder::create($directoryDest);
            }

            $uploaded = \JFile::upload($tmpSrc, $directoryDest . $fileName, false, true);

            if (!$uploaded) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_UPLOAD_FILE_SERVER]);
                die;
            }

            echo json_encode([
                'success' => $fileName
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

        die;
    }
}