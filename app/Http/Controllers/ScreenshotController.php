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
	const Documentations = [
		'damage_modifier' => 'DamageModifier'
	];

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

		$system = DB::select(DB::raw("SELECT player_name, users.license_identifier, url, details, timestamp FROM (SELECT url, details, character_id, created_at AS timestamp FROM system_screenshots UNION SELECT identifier, reason, ban_hash, timestamp FROM user_bans) data LEFT JOIN characters ON data.character_id = characters.character_id LEFT JOIN users ON url = users.license_identifier OR characters.license_identifier = users.license_identifier WHERE (details LIKE 'Anti-Cheat: %' OR details LIKE 'MODDING-%') AND (url LIKE 'license:%' OR url LIKE 'https://%') ORDER BY timestamp DESC LIMIT 20 OFFSET " . (($page - 1) * 20)));

        $identifiers = array_values(array_map(function ($entry) {
            return $entry->license_identifier;
        }, $system));

        return Inertia::render('Screenshots/AntiCheat', [
            'screenshots' => $system,
            'links' => $this->getPageUrls($page),
            'banMap'  => Ban::getAllBans(false, $identifiers, true),
            'page' => $page,
        ]);
    }

    /**
     * Anti-Cheat documentation.
     *
     * @param Request $request
     * @return Response
     */
    public function docs(Request $request, string $type): Response
    {
		$page = self::Documentations[$type];

		if (empty($page)) {
			abort(404);
		}

        return Inertia::render('AntiCheat/' . $page);
    }

}
