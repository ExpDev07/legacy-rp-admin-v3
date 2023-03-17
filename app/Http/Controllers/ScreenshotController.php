<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Resources\LogResource;
use App\Log;
use App\Ban;
use App\PanelLog;
use App\Player;
use App\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ScreenshotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        $query = Screenshot::query()->orderByDesc('created_at');

        $page = Paginator::resolveCurrentPage('page');

        $query->select(['id', 'license_identifier', 'filename', 'note', 'created_at']);
        $query->limit(20)->offset(($page - 1) * 20);

        $screenshots = $query->get()->toArray();

        return Inertia::render('Screenshots/Index', [
            'screenshots' => $screenshots,
            'links' => $this->getPageUrls($page),
            'playerMap' => Player::fetchLicensePlayerNameMap($screenshots, ['license_identifier']),
            'page' => $page,
        ]);
    }

    /**
     * All Anti-Cheat screenshots.
     *
     * @param Request $request
     * @return Response
     */
    public function antiCheat(Request $request): Response
    {
        $page = Paginator::resolveCurrentPage('page');

		$query = "SELECT player_name, users.license_identifier, url, details, timestamp FROM (" .
			"SELECT license_identifier, url, details, created_at AS timestamp FROM system_screenshots LEFT JOIN characters ON system_screenshots.character_id = characters.character_id WHERE IF(SUBSTRING_INDEX(details, ' ', 1) = 'Anti-Cheat:', 1, 0) = 1 " .
			"UNION " .
			"SELECT identifier, ban_hash, reason, timestamp FROM user_bans WHERE SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND IF(SUBSTRING_INDEX(reason, '-', 1) = 'MODDING', 1, 0) = 1" .
			") data LEFT JOIN users ON data.license_identifier = users.license_identifier ORDER BY timestamp DESC LIMIT 20 OFFSET " . (($page - 1) * 20);

		$system = DB::select(DB::raw($query));

        $identifiers = array_values(array_map(function ($entry) {
            return $entry->license_identifier;
        }, $system));

		$system = array_map(function ($entry) {
			$entry->reason = Str::startsWith($entry->details, 'Anti-Cheat:') ? $entry->details : Ban::resolveAutomatedReason($entry->details);

			return $entry;
		}, $system);

        return Inertia::render('Screenshots/AntiCheat', [
            'screenshots' => $system,
            'links' => $this->getPageUrls($page),
            'banMap'  => Ban::getAllBans(false, $identifiers, true),
            'page' => $page,
        ]);
    }

}
