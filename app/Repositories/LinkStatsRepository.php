<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Repositories\LinksRepository;
use Illuminate\Support\Facades\DB;

class LinkStatsRepository extends BaseRepository
{
    const TABLE_NAME = 'stats';
    const PRIMARY_KEY_FIELD = null;

    const DATA_STRUCTURE = [
        'link_id'           => 'int',
        'client_id'         => 'string',
        'advertising_id'    => 'int',
        'viewed_at'         => 'date',
    ];

    const NULLABLE_FIELDS = [
        'advertising_id'
    ];
    
    public function addStats($data)
    {
        $data['viewed_at'] = new \DateTime;

        return $this->insertRData($data);
    }

    public function getFullStatByLinkID(int $linkID)
    {
        return DB::table(self::TABLE_NAME)
            ->where('link_id', '=', $linkID)
            ->orderBy('viewed_at', 'desc')
            ->get();
    }

    public function getUniqVisitorsStatByPeriod(\DateTime $start, \DateTime $end)
    {
        $rawSelect = LinksRepository::TABLE_NAME . '.*, ';
        $rawSelect .= 'COUNT(DISTINCT client_id) as clients';

        return DB::table(self::TABLE_NAME)
            ->selectRaw($rawSelect)
            ->join(
                LinksRepository::TABLE_NAME,
                LinksRepository::TABLE_NAME . '.id',
                '=',
                self::TABLE_NAME . '.link_id')
            ->where('viewed_at', '>', $start)
            ->where('viewed_at', '<', $end)
            ->groupBy(LinksRepository::TABLE_NAME . '.id')
            ->get();
    }
}