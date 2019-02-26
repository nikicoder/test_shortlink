<?php

namespace App\Models;

use App\Repositories\LinkStatsRepository;
use App\Entity\LinkEntity;

class LinkStatsModel
{
    public function getLinkStatByID(int $linkId): array
    {
        $result = [];

        $data = (new LinkStatsRepository)->getFullStatByLinkID($linkId);
        foreach($data as $d) {
            $result[] = [
                'client'    => $d->client_id,
                'adv'       => $d->advertising_id,
                'date'      => $d->viewed_at,
            ];
        }

        return $result;
    }

    public function getUniqVisitorsByPeriod(\DateTime $start, \DateTime $end)
    {
        $result = [];

        $data = (new LinkStatsRepository)->getUniqVisitorsStatByPeriod($start, $end);
        foreach($data as $d) {
            settype($d, 'array');
            $result[] = [
                'link'      => new LinkEntity($d),
                'clients'   => $d['clients']
            ];
        }

        return $result;
    }
}