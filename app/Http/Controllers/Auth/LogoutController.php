<?php

namespace App\Http\Controllers\Auth;

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
        // Logout the user.
        session()->forget('user');
        session()->put('isLogout', true);

        // Redirect them to base path.
        return redirect('/');
    }

}
