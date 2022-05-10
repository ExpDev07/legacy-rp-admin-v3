<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Helpers\GeneralHelper;
use App\Helpers\OPFWHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Server;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffChatController extends Controller
{

    /**
     * Renders the staff chat.
     *
     * @param Request $request
     * @return Response
     */
    public function staff(Request $request): Response
    {
        return Inertia::render('StaffChat', []);
    }

    /**
     * Add external staff messages
     *
     * @param string $message
     * @param Request $request
     * @return RedirectResponse
     */
    public function externalStaffChat(string $message, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return back()->with('error', 'Something went wrong.');
        }

        $message = trim($message);

        if (!$message || strlen($message) > 250) {
            return back()->with('error', 'Invalid message.');
        }

        $serverIp = Server::getFirstServer();

        $status = OPFWHelper::staffChat($serverIp, $user->player->steam_identifier, $message);

        return $status->redirect();
    }

}
