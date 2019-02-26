<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

use App\Models\LinkModel;
use App\Models\LinkStatsModel;

class LinkStatController extends Controller
{
    public function stats(Request $request)
    {
        $uri = (string)$request->route('path');
        $secret = (string)$request->route('secret');

        $lm = new LinkModel;
        $linkData = $lm->getLinkByURI($uri);

        if($secret !== $linkData->secret) {
            return response('Forbidden', 403);
        }
        
        $statData = (new LinkStatsModel)->getLinkStatByID($linkData->linkId);
        $data = [
            'link'  => $linkData,
            'stats' => $statData
        ];

        return view('link_stats', $data);
    }
}