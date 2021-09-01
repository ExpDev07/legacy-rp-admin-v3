<?php

namespace App\Http\Middleware;

use App\Helpers\GeneralHelper;
use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
use App\Player;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        '/api/players',
        '/api/characters',
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
        $session = SessionHelper::getInstance();

        // Check for staff status.
        if (!$this->isStaff($request) || !$this->checkSessionLock()) {
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
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' || time() - $this->lastUpdated() > 15) {
                $user = $session->get('user');

                $player = Player::query()->where('steam_identifier', '=', $user['player']['steam_identifier'])->select([
                    'player_name', 'is_super_admin', 'is_staff',
                ])->first();

                $user['player']['player_name'] = $player->player_name;
                $user['player']['is_super_admin'] = $player->is_super_admin;
                $user['player']['is_staff'] = $player->is_staff;

                $session->put('user', $user);
                $session->put('last_updated', time());

                if (!$player->is_staff) {
                    return redirect('/login')->with('error',
                        'Your staff status has changed, please log in again!'
                    );
                }
            }

            GeneralHelper::updateSocketSession();
        }

        return $next($request);
    }

    protected function lastUpdated(): int
    {
        $session = SessionHelper::getInstance();

        return $session->exists('last_updated') ? intval($session->get('last_updated')) : 0;
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

    public static function getSessionDetail(): array
    {
        return [
            'ua'   => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'db'   => DB::connection()->getDatabaseName(),
            'host' => $_SERVER['HTTP_HOST'],
        ];
    }

    protected function checkSessionLock(): bool
    {
        $detail = self::getSessionDetail();

        $session = SessionHelper::getInstance();
        if ($session->exists('session_lock')) {
            $lock = $session->get('session_lock');
            $print = self::getFingerprint();

            $valid = $lock === $print;
            if (!$valid) {
                $sDetail = $session->get('session_detail');

                LoggingHelper::log($session->getSessionKey(), 'StaffMiddleware session-lock is invalid');
                LoggingHelper::log($session->getSessionKey(), $lock . ' != ' . $print);
                LoggingHelper::log($session->getSessionKey(), 'current.detail -> ' . json_encode($detail));
                LoggingHelper::log($session->getSessionKey(), 'session.detail -> ' . json_encode($sDetail));

                $this->error = 'Your session is invalid, please refresh this page or log in again.';
            }

            $session->put('session_detail', $detail);

            return $valid;
        } else {
            self::updateSessionLock();
            return true;
        }
    }

    public static function updateSessionLock()
    {
        $detail = [
            'ua' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        ];

        $session = SessionHelper::getInstance();

        $session->put('session_lock', self::getFingerprint());
        $session->put('session_detail', $detail);
    }

    public static function getFingerprint(): string
    {
        return md5(json_encode(self::getSessionDetail()));
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
