<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
 
class CommerceImagesHelper {

    public static function getBase64ImageByID(int $id): string
    {
        $contents = Storage::get('public/commerce_images/' . $id . '.png');

        return base64_encode($contents);
    }
}