<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers\Auth
 */
class LogoutController extends Controller
{

    /**
     * Logs the user out.
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $session = SessionHelper::getInstance();
        LoggingHelper::log($session->getSessionKey(), 'Logout triggered, dropping session');

        // Logout the user.
        SessionHelper::drop();
        session()->put('isLogout', true);

        // Redirect them to base path.
        return redirect('/');
    }

}
