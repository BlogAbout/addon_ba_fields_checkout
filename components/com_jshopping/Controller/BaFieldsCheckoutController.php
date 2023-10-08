<?php

namespace Joomla\Component\Jshopping\Site\Controller;

defined('_JEXEC') or die();

class BaFieldsCheckoutController extends BaseController
{
    public function uploadFile()
    {
        try {
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

            if ($fileSize > 5 * 1024 * 1024) {
                echo json_encode(['error' => _JSHOP_BAFO_ERROR_UPLOAD_FILE_NOT_SELECTED]);
                die;
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