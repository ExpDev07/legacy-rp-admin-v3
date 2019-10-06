<?php

namespace App\Http\Controllers;

use App\Character;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @param Character $character
     * @return Response
     */
    public function update(Request $request, Character $character)
    {
        //
    }

}
