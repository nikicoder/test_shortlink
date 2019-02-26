<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

use App\Models\LinkModel;
use App\Models\LinkStatsModel;

class DashboardController extends Controller
{
    const DEFAULT_STATS_PERIOD = '14';

    public function linkstats(Request $request)
    {
        $start = new \DateTime;
        $start->sub(new \DateInterval('P' . self::DEFAULT_STATS_PERIOD . 'D'));

        $end = new \DateTime;

        $statData = (new LinkStatsModel)->getUniqVisitorsByPeriod($start, $end);
        
        return view('dashboard_stats', ['stats' => $statData]);
    }
}