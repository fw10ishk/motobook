<?php

namespace App\Helpers;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class FileUpload
{
    public static function upload($file, $path)
    {
        if (!$file instanceof UploadedFileInterface) {
            throw new RuntimeException('Invalid file upload object provided');
        }

        // Check if there was an upload error
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        // Verify file size (max 10MB)
        if ($file->getSize() > 10485760) {
            throw new RuntimeException('Uploaded file exceeds maximum size limit');
        }

        // Create directory if it doesn't exist
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Generate unique filename
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;

        try {
            $file->moveTo($path . DIRECTORY_SEPARATOR . $filename);
            return $filename;
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to move uploaded file: ' . $e->getMessage());
        }
    }

    public static function delete($path, $filename)
    {
        if (empty($filename)) {
            return false;
        }

        $filePath = $path . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    public static function isValidUpload($file)
    {
        return $file instanceof UploadedFileInterface
            && $file->getError() === UPLOAD_ERR_OK
            && $file->getSize() > 0;
    }
}
