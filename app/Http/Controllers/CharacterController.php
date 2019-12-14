<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Requests\UpdateCharacterRequest;
use Illuminate\Http\Response;

class CharacterController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param Character $character
     * @return Response
     */
    public function show(Character $character)
    {
        return view('characters.show', [ 'character' => $character ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCharacterRequest $request
     * @param Character $character
     * @return Response
     */
    public function update(UpdateCharacterRequest $request, Character $character)
    {
        $character->update($request->validated());
        return back();
    }

}
