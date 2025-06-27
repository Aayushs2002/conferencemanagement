<?php

namespace App\Services\File;

use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function fileUpload($file, $name, $location)
    {
        $logoName = rand(00, 99) . $name . '-' . time() . '.' . $file->extension();
        Storage::disk('public')->putFileAs($location, $file, $logoName);
        return $logoName;
    }

    public function deleteFile($file, $location)
    {
        Storage::disk('public')->delete($location . '/' . $file);
    }
}
