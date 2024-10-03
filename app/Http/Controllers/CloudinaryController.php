<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cloudinary\Cloudinary;

class CloudinaryController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    public function upload(UploadedFile $file, string $folder)
    {
        $uploadedFileUrl = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder
        ])['secure_url'];

        return $uploadedFileUrl;
    }
}