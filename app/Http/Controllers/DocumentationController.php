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

class DocumentationController extends Controller
{
	const Documentations = [
		'damage_modifier' => 'DamageModifier',
		'disconnect_reasons' => 'DisconnectReasons'
	];

    /**
     * Random documentations.
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

        return Inertia::render('Documentation/' . $page);
    }

}
