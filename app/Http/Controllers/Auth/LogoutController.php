<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

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
        // Logout the user.
        auth()->logout();

        // Redirect them to base path.
        return redirect('/');
    }

}
