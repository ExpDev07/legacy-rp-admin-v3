<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
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
    public function render()
    {
        return Inertia::render('Login');
    }

}
