<?php

namespace App\Helpers;

use App\Session;
use Illuminate\Support\Str;

class SessionHelper
{
    const Cookie   = '_op_fw_session_store_i2';
    const Alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
    const Lifetime = 60 * 60 * 24 * 30;

    /**
     * Singleton instance
     *
     * @var SessionHelper|null
     */
    private static ?SessionHelper $instance = null;

    /**
     * The current sessions key
     *
     * @var string|null
     */
    private ?string $sessionKey = null;

    /**
     * Where the sessions are stored
     * @var string|null
     */
    private ?string $storage = null;

    /**
     * The value of the current session
     *
     * @var array
     */
    private array $value = [];

    /**
     * Last retrieved session.
     */
    private ?Session $session = null;

    private function __construct()
    {
    }

    /**
     * Returns the current sessions key
     *
     * @return string
     */
    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    /**
     * Checks if a given key is set in the session
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Gets a value from the session
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->value)) {
            return $this->value[$key];
        }
        return null;
    }

    /**
     * Sets a value in the session
     *
     * @param string $key
     * @param mixed $value
     */
    public function put(string $key, $value)
    {
        $this->value[$key] = $value;
        $this->store();
    }

    /**
     * Forgets a value in the session
     *
     * @param string $key
     */
    public function forget(string $key)
    {
        if (array_key_exists($key, $this->value)) {
            unset($this->value[$key]);
            $this->store();
        }
    }

    /**
     * Drops the current session
     */
    public static function drop()
    {
        $helper = self::getInstance();
        LoggingHelper::log($helper->sessionKey, 'Dropping session');

        $session = $helper->getSession();

        if ($session) {
            $session->delete();
        } else {
            LoggingHelper::log($helper->sessionKey, 'Session not found');
        }

        $helper->session = null;

        self::$instance = null;
    }

    /**
     * Retrieves the session from the database
     */
    private function getSession(): ?Session
    {
        if (!$this->session) {
            $this->session = Session::query()->where('key', $this->sessionKey)->first();
        }

        return $this->session;
    }

    /**
     * Loads the sessions data
     */
    private function load()
    {
        $session = $this->getSession();

        if (!$session) {
            LoggingHelper::log($this->sessionKey, 'Session did not exist in DB while loading data');
            $this->value = [];

            return;
        }

        $data = json_decode($session->data, true);

        if (!$data) {
            LoggingHelper::log($this->sessionKey, 'Failed to decode session data');
            $this->value = [];

            return;
        }

        $this->value = $data;
    }

    /**
     * Saves the session's data to the database
     */
    private function store()
    {
        $session = $this->getSession();

        if ($session) {
            $session->update([
                'data' => json_encode($this->value),
                'last_accessed' => time()
            ]);
        } else {
            Session::query()->create([
                'key' => $this->sessionKey,
                'data' => json_encode($this->value),
                'last_accessed' => time()
            ]);
        }

        self::updateCookie($this->sessionKey);
    }

    /**
     * Overrides the session cookie
     *
     * @param string $sessionKey
     */
    public static function updateCookie(string $sessionKey)
    {
        $cookie = CLUSTER . self::Cookie;

        setcookie($cookie, $sessionKey, [
            'expires'  => time() + self::Lifetime,
            'secure'   => false,
            'httponly' => true,
            'path'     => '/',
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Cleans up old sessions
     */
    public static function cleanup()
    {
        $lifetime = time() - self::Lifetime;

        Session::query()->where('last_accessed', '<', $lifetime)->delete();
    }

    /**
     * Returns an instance of the session helper
     *
     * @return SessionHelper
     */
    public static function getInstance(): SessionHelper
    {
        $cookie = CLUSTER . self::Cookie;

        if (self::$instance === null) {
            $helper = new SessionHelper();

            $helper->storage = rtrim(storage_path('framework/session_storage'), '/\\') . '/';
            $helper->sessionKey = !empty($_COOKIE[$cookie]) && is_string($_COOKIE[$cookie]) ? $_COOKIE[$cookie] : null;

            if ($helper->sessionKey === null || !$helper->getSession()) {
                $log = 'Creating new session key';
                if ($helper->sessionKey === null) {
                    $log = 'Session key is null, creating new session key';
                } else if (!$helper->getSession()) {
                    $log = 'Session (' . $helper->sessionKey . ') was not found in DB, creating new session key';
                }

                $helper->sessionKey = self::uniqueId();

                LoggingHelper::log($helper->sessionKey, $log);
            }

            setcookie($cookie, $helper->sessionKey, [
                'expires' => time() + self::Lifetime,
                'secure'  => true,
                'path'    => '/',
            ]);

            $helper->load();
            $helper->store();

            self::$instance = $helper;
        }

        return self::$instance;
    }

    /**
     * Generated a 20 character unique session key
     *
     * @return string
     */
    private static function uniqueId(): string
    {
        $characters = str_split(self::Alphabet);

        $str = '';
        for ($x = 0; $x < 30; $x++) {
            $str .= $characters[array_rand($characters)];
        }

        return $str;
    }
}
