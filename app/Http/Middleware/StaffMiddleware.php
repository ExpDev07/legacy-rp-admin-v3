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
            LoggingHelper::log($session->getSessionKey(), 'StaffMiddleware check failed');
            LoggingHelper::log($session->getSessionKey(), 'session.user -> ' . json_encode($this->cleanupUserDump($session->get('user'))));

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !in_array($requestPath, self::IgnoreGETRoutes)) {
                return $this->render();
            } else {
                LoggingHelper::log($session->getSessionKey(), 'Dropping session');
                SessionHelper::drop();

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
        $detail = [
            'ua' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'ip' => $_SERVER['REMOTE_ADDR'],
        ];

        $session = SessionHelper::getInstance();
        if ($session->exists('session_lock')) {
            $lock = $session->get('session_lock');
            $print = $this->getFingerprint();

            $valid = $lock === $print;
            if (!$valid) {
                LoggingHelper::log($session->getSessionKey(), 'StaffMiddleware session-lock is invalid');
                LoggingHelper::log($session->getSessionKey(), $lock . ' != ' . $print);
                LoggingHelper::log($session->getSessionKey(), 'current.detail -> ' . json_encode($detail));
                LoggingHelper::log($session->getSessionKey(), 'session.detail -> ' . json_encode($session->get('session_detail')));

                $this->error = 'Your session is invalid, please log in again.';
            }

            $session->put('session_detail', $detail);

            return $valid;
        } else {
            $session->put('session_lock', $this->getFingerprint());
            $session->put('session_detail', $detail);
            return true;
        }
    }

    private function getFingerprint(): string
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $ip = $_SERVER['REMOTE_ADDR'];

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

    private function cleanupUserDump($user)
    {
        if (is_array($user)) {
            if (isset($user['avatar'])) {
                unset($user['avatar']);
            }

            if (isset($user['player']) && is_array($user['player'])) {
                if (isset($user['player']['identifiers'])) {
                    unset($user['player']['identifiers']);
                }
                if (isset($user['player']['user_settings'])) {
                    unset($user['player']['user_settings']);
                }
                if (isset($user['player']['user_data'])) {
                    unset($user['player']['user_data']);
                }
                if (isset($user['player']['activity_points'])) {
                    unset($user['player']['activity_points']);
                }
                if (isset($user['player']['staff_points'])) {
                    unset($user['player']['staff_points']);
                }
                if (isset($user['player']['avatar'])) {
                    unset($user['player']['avatar']);
                }
            }
        }

        return $user;
    }

}
