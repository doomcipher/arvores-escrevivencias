<?php

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

class CloudinaryHelper
{
    private static function config()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => CLOUDINARY_CLOUD_NAME,
                'api_key'    => CLOUDINARY_API_KEY,
                'api_secret' => CLOUDINARY_API_SECRET,
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    public static function upload($file_path, $folder = 'posts')
    {
        try {
            self::config();

            $result = (new UploadApi())->upload($file_path, [
                'folder'        => $folder,
                'resource_type' => 'auto'
            ]);

            return $result['secure_url'] ?? null;
        } catch (Exception $e) {
            throw new Exception('Erro ao fazer upload: ' . $e->getMessage());
        }
    }

    public static function delete($public_id)
    {
        try {
            self::config();
            (new UploadApi())->destroy($public_id);
            return true;
        } catch (Exception $e) {
            throw new Exception('Erro ao deletar arquivo: ' . $e->getMessage());
        }
    }
}
