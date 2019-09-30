<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Player;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlayerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $query = strtolower($request->get('q'));

        // Search for the players by their identifier or name.
        $players = Player::query()->where(function (Builder $builder) use ($query) {
            $builder->whereRaw('lower(identifier) like (?)', ["%{$query}%"]);
            $builder->orWhereRaw('lower(name) like (?)', ["%{$query}%"]);
        });

        return view('players.index', [ 'players' => $players->paginate(10) ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Player $player
     * @return Response
     */
    public function show(Player $player)
    {
        return view('players.show', [ 'player' => $player ]);
    }

}
