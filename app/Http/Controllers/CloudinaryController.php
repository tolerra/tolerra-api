<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function upload(Request $request, $fileKey, $folder)
    {
        $file = $request->file($fileKey);
        $uploadedFileUrl = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder
        ])['secure_url'];

        return $uploadedFileUrl;
    }
}