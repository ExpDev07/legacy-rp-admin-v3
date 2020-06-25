<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @package App\Http\Controllers\Auth
 */
class LogoutController extends Controller
{

    /**
     * Logs the user out.
     *
     * @return Response
     */
    public function logout()
    {
        // Logout the user.
        auth()->logout();

        // Redirect them to base path.
        return redirect('/');
    }

}
