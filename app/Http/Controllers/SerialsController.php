<?php

namespace App\Http\Controllers;

use App\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SerialsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $serial = intval($request->input('serial'));

        $result = null;
        if ($serial) {
            $item = DB::table('inventories')
                ->where('id', '=', $serial)
                ->select(['id', 'item_name', 'inventory_slot', 'inventory_name', 'item_metadata'])
                ->get()->first();

            if ($item && Str::startsWith($item->item_name, 'weapon_')) {
                $result = [
                    'item'       => $item->item_name,
                    'inventory'  => $item->inventory_name,
                    'character'  => null,
                    'registered' => false,
                ];

                $character = null;

                $json = json_decode($item->item_metadata, true);
                if ($json && isset($json['characterId']) && is_numeric($json['characterId'])) {
                    $character = Character::query()
                        ->where('character_id', '=', $json['characterId'])
                        ->get()->first();
                    $result['registered'] = true;
                } else if (Str::startsWith($item->inventory_name, 'character-')) {
                    $character = Character::query()
                        ->where('character_id', '=', str_replace('character-', '', $item->inventory_name))
                        ->get()->first();
                }

                if ($character) {
                    $result['character'] = [
                        'name'  => $character->first_name . ' ' . $character->last_name,
                        'id'    => $character->character_id,
                        'steam' => $character->steam_identifier,
                    ];
                }
            }
        }

        return Inertia::render('Serials/Index', [
            'result'  => $result,
            'filters' => [
                'serial' => $request->input('serial'),
            ],
        ]);
    }

}
