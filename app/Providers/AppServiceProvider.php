<?php

namespace App\Providers;

use App\Helpers\PermissionHelper;
use App\Helpers\SessionHelper;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\UserResource;
use App\Player;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register inertia.
        $this->registerInertia();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Disable resource wrapping.
        JsonResource::withoutWrapping();
    }

    /**
     * Registers inertia.
     */
    protected function registerInertia()
    {
        // Shared inertia data.
        Inertia::share([
            // Current and previous url.
            'url'   => Str::start(str_replace(url('/'), '', URL::current()), '/'),
            'back'  => Str::start(str_replace(url('/'), '', URL::previous('/')), '/'),

            // Flash messages.
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error'   => session('error'),
                ];
            },

            // Authentication.
            'auth'  => function () {
                $session = SessionHelper::getInstance();

                $user = $session->get('user') ?: null;
                $player = $user['player'] ?? null;

                return [
                    'user'        => $user ? new UserResource(new User($user)) : null,
                    'player'      => $player ? new PlayerResource(new Player($player)) : null,
                    'permissions' => PermissionHelper::getFrontendPermissions(),
                ];
            },

            'lang' => env('VUE_APP_LOCALE', 'en-us'),
        ]);
    }

}
