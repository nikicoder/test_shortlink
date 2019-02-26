<?php

namespace App\Helpers;

use App\Repositories\LinkStatsRepository;
 
class StatsHelper {

    /**
     * addLinkTostats
     *
     * @param  int $linkId
     * @param  string $clientId
     *
     * @return void
     */
    public static function addLinkTostats(int $linkId, string $clientId, int $advId = 0)
    {
        (new LinkStatsRepository)->addStats([
            'link_id'           => $linkId,
            'client_id'         => $clientId,
            'advertising_id'    => $advId > 0 ? $advId : null
        ]);
    }
}