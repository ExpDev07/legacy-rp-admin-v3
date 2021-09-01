<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Helpers\GeneralHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Server;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ChangelogController extends Controller
{

    /**
     * Renders the home page.
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        return Inertia::render('Changelog', [
            'updates' => $this->getRecentUpdates(),
        ]);
    }

    private function getRecentUpdates(): array
    {
        $key = CLUSTER . 'recent_updates';
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $pulls = json_decode(GeneralHelper::get('https://api.github.com/repos/ExpDev07/legacy-rp-admin-v3/pulls?state=closed'), true);
        if (!$pulls || !is_array($pulls)) {
            return [];
        }

        $working = getcwd();

        chdir(__DIR__ . '/../../../');
        $git = shell_exec('git show :/^pull');
        preg_match('/(?<=Merge pull request #)\d+(?= from)/mi', $git, $match);
        $pr = !empty($match) ? intval($match[0]) : null;

        chdir($working);

        $current = null;
        $updates = [];
        for ($x = 0; sizeof($updates) < 8 && $x < sizeof($pulls); $x++) {
            $pull = $pulls[$x];

            $re = '/^\s*-\s+(.+?)$/m';
            preg_match_all($re, $pull['body'], $matches, PREG_SET_ORDER, 0);

            $matches = array_map(function ($match) {
                return trim($match[1]);
            }, $matches);

            if (!$pull['merged_at'] || empty($matches)) {
                continue;
            }

            if ($pr && !$current && intval($pull['number']) <= $pr) {
                $current = intval($pull['number']);
            }

            $updates[] = [
                'id'    => intval($pull['number']),
                'title' => 'pr' . $pull['number'],
                'body'  => array_values($matches),
                'time'  => $pull['merged_at'],
            ];
        }

        $updates = [
            'current' => $current,
            'updates' => $updates,
        ];

        Cache::put($key, $updates, 10 * 60);

        return $updates;
    }

}
