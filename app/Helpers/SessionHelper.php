<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SessionHelper
{
    const Alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
    const Lifetime = 60 * 60 * 24 * 365;

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

        unlink($helper->getSessionFile());
        self::$instance = null;
    }

    /**
     * Returns the file the session is stored in.
     *
     * @return string
     */
    private function getSessionFile(): string
    {
        return $this->storage . $this->sessionKey . '.session';
    }

    /**
     * Loads the sessions data
     */
    private function load()
    {
        if (file_exists($this->getSessionFile())) {
            $json = json_decode(file_get_contents($this->getSessionFile()), true) ?: [];
            $this->value = $json;
        } else {
            $this->value = [];
        }
    }

    /**
     * Saves the sessions data to its file
     */
    private function store()
    {
        file_put_contents($this->getSessionFile(), json_encode($this->value));
    }

    /**
     * Returns an instance of the session helper
     *
     * @return SessionHelper
     */
    public static function getInstance(): SessionHelper
    {
        if (self::$instance === null) {
            $key = Str::slug(env('APP_NAME', 'laravel'), '_') . '_session_store';
            $helper = new SessionHelper();

            $helper->storage = rtrim(storage_path('framework/session_storage'), '/\\') . '/';
            $helper->sessionKey = !empty($_COOKIE[$key]) && is_string($_COOKIE[$key]) ? $_COOKIE[$key] : null;

            if ($helper->sessionKey === null || !file_exists($helper->getSessionFile())) {
                $helper->sessionKey = self::uniqueId();
            }

            setcookie($key, $helper->sessionKey, [
                'expires' => time() + self::Lifetime,
                'secure'  => true,
                'path'    => '/',
            ]);

            $helper->load();
            $helper->store();

            self::$instance = $helper;
        }

        self::cleanup(self::$instance->storage);

        return self::$instance;
    }

    /**
     * Deletes all outdated sessions
     *
     * @param string $storage
     */
    private static function cleanup(string $storage)
    {
        $files = scandir($storage);

        foreach ($files as $file) {
            $path = $storage . $file;

            if (is_file($path) && Str::endsWith($file, '.session') && time() - filemtime($path) > self::Lifetime) {
                unlink($path);
            }
        }
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
