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
     * The players.
     *
     * @var Player
     */
    private $players;

    /**
     * Constructs a new PlayerController.
     *
     * @param Player $players
     */
    public function __construct(Player $players)
    {
        $this->players = $players;
    }

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
        $result = $this->players->newQuery()->where(function (Builder $builder) use ($query) {
            $builder->whereRaw('lower(identifier) like (?)', ["%{$query}%"]);
            $builder->orWhereRaw('lower(name) like (?)', ["%{$query}%"]);
        });

        return view('players.index', [ 'players' => $result->simplePaginate(15) ]);
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
