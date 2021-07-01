<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Returns the next and previous links
     *
     * @param int $page
     * @return array
     */
    protected function getPageUrls(int $page): array
    {
        $url = preg_replace('/[&?]page=\d+/m', '', $_SERVER['REQUEST_URI']);
        if (Str::contains($url, '?')) {
            $url .= '&';
        } else {
            $url .= '?';
        }
        $next = $url . 'page=' . ($page + 1);
        $prev = $url . 'page=' . ($page - 1);

        return [
            'next' => $next,
            'prev' => $prev,
        ];
    }
}
