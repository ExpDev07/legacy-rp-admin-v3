<?php

namespace App\Http\Controllers\Player;

use App\Ban;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBanRequest;
use App\Player;
use foo\bar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class BanController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param StoreBanRequest $request
     * @return Response
     */
    public function store(Player $player, StoreBanRequest $request)
    {
        // Create the warning and persist it to the database.
        $player->bans()->create(array_merge($request->validated(), [
            'ban-id'    => Str::uuid()->toString(),
            'banner-id' => $request->user()->player->staff,
        ]));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Ban $ban
     * @return Response
     */
    public function destroy(Player $player, Ban $ban)
    {
        $ban->forceDelete();
        return back();
    }
}
