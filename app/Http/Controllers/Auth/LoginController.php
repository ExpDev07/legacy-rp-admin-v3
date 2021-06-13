<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\SessionHelper;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Renders the login view.
     *
     * @return Response
     */
    public function render(): Response
    {
        $session = SessionHelper::getInstance();

        if ($session->get('isLogout')) {
            $session->forget('isLogout');
            session()->forget('error');
        }

        return Inertia::render('Login');
    }

}
