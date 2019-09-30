<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarningRequest;
use App\Player;
use App\Warning;
use Illuminate\Http\Request;

class WarningController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param StoreWarningRequest $request
     * @return void
     */
    public function store(Player $player, StoreWarningRequest $request)
    {
        // Create the warning and persist it to the database.
        $player->warnings()->create(array_merge($request->validated(), [
            'issuer_id' => auth()->user()->player->id,
        ]));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Warning $warning
     * @return void
     */
    public function destroy(Warning $warning)
    {
        //
    }

}
