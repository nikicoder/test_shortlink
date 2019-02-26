<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
 
class CommerceImagesHelper {

    public static function getBase64ImageByID(int $id): string
    {
        $path = 'public' . DIRECTORY_SEPARATOR . 'commerce_images' . DIRECTORY_SEPARATOR;
        $contents = Storage::get($path . $id . '.png');

        return base64_encode($contents);
    }
}