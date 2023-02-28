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
		$servers = Server::getAllServers();

		$data = [];

		foreach($servers as $server) {
			$data[] = [
				'url'         => $server->url,
				'name'        => Server::getServerName($server->url),
				'information' => $server->fetchApi()
			];
		}

        return Inertia::render('Servers/Index', [
            'servers' => $data
        ]);
    }

}
