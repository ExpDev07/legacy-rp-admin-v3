<?php

namespace App;

use Illuminate\Http\RedirectResponse;

class OPFWResponse
{
    /**
     * Success or not
     * @var bool
     */
    public bool $status = false;

    /**
     * The error or success message
     * @var string
     */
    public string $message = '';

    /**
     * OPFWResponse constructor.
     * @param bool $status
     * @param string $message
     */
    public function __construct(bool $status, string $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        if ($this->status) {
            return back()->with('success', $this->message);
        }

        return back()->with('error', $this->message);
    }
}
