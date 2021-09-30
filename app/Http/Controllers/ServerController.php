<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServerStoreRequest;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\ServerResource;
use App\Player;
use App\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Servers/Index', [
            'servers' => ServerResource::collection(Server::query()->paginate(100)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServerStoreRequest $request
     * @return RedirectResponse
     */
    public function store(ServerStoreRequest $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can add servers.');
        }

        Server::query()->create($request->validated());
        return back()->with('success', 'The server was successfully added. The server panel will now track it!');
    }

    /**
     * Updates the specified resource.
     *
     * @param Server $server
     * @param ServerStoreRequest $request
     * @return RedirectResponse
     */
    public function update(Server $server, ServerStoreRequest $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can edit servers.');
        }

        $server->update($request->validated());

        return back()->with('success', 'The server was successfully edited.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Server $server
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Server $server, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can delete servers.');
        }

        $server->forceDelete();
        return redirect('/servers')->with('success', 'The server was successfully removed from tracking.');
    }

    /**
     * Display the specified resource.
     *
     * @param Server $server
     * @return Response
     */
    public function show(Server $server): Response
    {
        $players = [];
        try {
            $steamIdentifiers = array_keys(Server::fetchSteamIdentifiers($server->url, true));

            $query = Player::query()->orderBy('last_connection');
            $query->whereIn('steam_identifier', $steamIdentifiers);

            $players = PlayerResource::collection($query->get());
        } catch (\Throwable $e) {}

        return Inertia::render('Servers/Show', [
            'server' => new ServerResource($server),
            'players' => $players
        ]);
    }

}
