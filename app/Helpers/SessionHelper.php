<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SessionHelper
{
    const Cookie   = '_op_fw_session_store_i2';
    const Alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
    const Lifetime = 60 * 60 * 24 * 2;

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

        if (!unlink($helper->getSessionFile())) {
            LoggingHelper::log($helper->sessionKey, 'Failed to unlink session file for drop');
        }
        self::$instance = null;
    }

    /**
     * Returns the file the session is stored in.
     *
     * @return string
     */
    private function getSessionFile(): string
    {
        return $this->storage . CLUSTER . $this->sessionKey . '.session';
    }

    /**
     * Loads the sessions data
     */
    private function load()
    {
        if (file_exists($this->getSessionFile())) {
            $json = json_decode(file_get_contents($this->getSessionFile()), true) ?: [];
            $this->value = $json;

            touch($this->getSessionFile());
        } else {
            LoggingHelper::log($this->sessionKey, 'Session file did not exist while loading data');
            $this->value = [];
        }
    }

    /**
     * Saves the session's data to its file
     */
    private function store()
    {
        if (!file_put_contents($this->getSessionFile(), json_encode($this->value))) {
            LoggingHelper::log($this->sessionKey, 'Failed to write session file while storing data');
        }
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
            'expires' => time() + self::Lifetime,
            'secure'  => true,
            'path'    => '/',
        ]);
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

            if ($helper->sessionKey === null || !file_exists($helper->getSessionFile())) {
                $log = 'Creating new session key';
                if ($helper->sessionKey === null) {
                    $log = 'Session key is null, creating new session key';
                } else if (!file_exists($helper->getSessionFile())) {
                    $log = 'Session file (' . $helper->sessionKey . ') was not found, creating new session key';
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
