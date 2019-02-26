<?php

namespace App\Helpers;
 
use \Illuminate\Http\Request;
use App\Entity\LinkRawEntity;
 
class RequestsParseHelper {

    /**
     * parseAddLinkPayload 
     *
     * @param  mixed $request
     *
     * @return LinkRawEntity
     */
    public static function parseAddLinkPayload(Request $request): LinkRawEntity
    {
        $result = new LinkRawEntity;

        $result->destinationLink = (string)$request->input('link');
        $result->uriSegment = (string)$request->input('self_uri');
        $result->isCommerce = (bool)($request->input('is_commerce') === 'yes');

        // тут могут быть отрицательные числа и прочие float-ы
        $days = (int)$request->input('expire_days');
        if($days > 1) {
            $result->expireDate = $days;
        }

        return $result;
    }
}