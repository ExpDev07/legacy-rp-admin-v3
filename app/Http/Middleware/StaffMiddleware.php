<?php

namespace App\Http\Middleware;

use App\Helpers\GeneralHelper;
use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Middleware to check if user is a staff member on our game-servers.
 *
 * @package App\Http\Middleware
 */
class StaffMiddleware
{
    const IgnoreGETRoutes = [
        '/map/data',
    ];

    /**
     * @var string
     */
    private $error = '';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestPath = strtok($_SERVER["REQUEST_URI"], '?');

        // Check for staff status.
        if (!$this->isStaff($request) || !$this->checkSessionLock()) {
            $session = SessionHelper::getInstance();
            LoggingHelper::log($session->getSessionKey(), 'StaffMiddleware user is not staff, dropping session');
            LoggingHelper::log($session->getSessionKey(), 'session.user->' . json_encode($session->get('user')));

            SessionHelper::drop();

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !in_array($requestPath, self::IgnoreGETRoutes)) {
                return $this->render();
            } else {
                return redirect('/login')->with('error',
                    'You must be a staff member to access the dashboard! If you believe this is a mistake, contact a developer.'
                );
            }
        } else {
            GeneralHelper::updateSocketSession();
        }

        return $next($request);
    }

    /**
     * Checks if the user that sent request is staff.
     *
     * @param Request $request
     * @return bool
     */
    protected function isStaff(Request $request): bool
    {
        $session = SessionHelper::getInstance();

        if ($session->exists('user')) {
            $user = $session->get('user');

            $request->setUserResolver(function () use ($user) {
                return json_decode(json_encode($user), FALSE);
            });

            if (!empty($user['player']) && $user['player']['is_staff']) {
                return true;
            }

            $this->error = 'You have to be a staff member to access this page.';
            return false;
        } else {
            $this->error = 'You are not logged in.';
            LoggingHelper::log($session->getSessionKey(), 'StaffMiddleware "user" is not set in session');
        }
        return false;
    }

    protected function checkSessionLock(): bool
    {
        $session = SessionHelper::getInstance();
        if ($session->exists('session_lock')) {
            $lock = $session->get('session_lock');
            $print = $this->getFingerprint();

            $valid = $lock === $print;
            if (!$valid) {
                LoggingHelper::log($session->getSessionKey(), 'StaffMiddleware "checkSessionLock" is invalid');
                LoggingHelper::log($session->getSessionKey(), $lock . ' != ' . $print);

                $this->error = 'Your session is invalid, please log in again.';
            }

            return $valid;
        } else {
            $session->put('session_lock', $this->getFingerprint());
            return true;
        }
    }

    private function getFingerprint(): string
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'];

        return sha1($ua . '_' . $ip);
    }

    /**
     * @return Response
     */
    public function render(): Response
    {
        $session = SessionHelper::getInstance();

        if (session()->get('isLogout')) {
            LoggingHelper::log($session->getSessionKey(), 'Rendering login view while coming from logout');

            session()->forget('isLogout');
            session()->forget('error');
            $this->error = '';
        }

        $session->put('returnTo', $_SERVER["REQUEST_URI"]);

        return Inertia::render('Login', [
            'error' => $this->error ? $this->error . ' If you believe this is a mistake, please contact a developer.' : '',
        ]);
    }

}
