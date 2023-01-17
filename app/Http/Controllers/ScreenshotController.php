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

        $system = DB::table('system_screenshots')
            ->where('details', 'LIKE', 'Anti-Cheat:%')
            ->orderByDesc('created_at')
            ->select(['character_id', 'url', 'details', 'created_at'])
            ->limit(20)->offset(($page - 1) * 20)
            ->get()->toArray();

        $characterIds = [];

        foreach ($system as $entry) {
            $characterId = $entry->character_id;

            if (!in_array($characterId, $characterIds)) {
                $characterIds[] = $characterId;
            }
        }

        $players = !empty($characterIds) ? Character::query()->select(['character_id', 'license_identifier'])->whereIn('character_id', $characterIds)->groupBy('license_identifier')->get()->toArray() : [];

        $characterLicenseNames = [];

        foreach ($players as $character) {
            $characterLicenseNames[$character['character_id']] = $character['license_identifier'];
        }

        $identifiers = array_values(array_map(function ($player) {
            return $player['license_identifier'];
        }, $players));

        return Inertia::render('Screenshots/AntiCheat', [
            'screenshots' => $system,
            'links' => $this->getPageUrls($page),
            'playerMap' => Player::fetchLicensePlayerNameMap($players, ['license_identifier']),
            'banMap'  => Ban::getAllBans(false, $identifiers, true),
            'characterLicenseNames' => $characterLicenseNames,
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
