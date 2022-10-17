<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    /**
     * @param bool $status
     * @param mixed|null $data
     * @param string $error
     * @return Response
     */
    protected static function json(bool $status, $data = null, string $error = ''): Response
    {
        if ($status) {
            $resp = [
                'status' => true,
                'data' => $data,
            ];
        } else {
            $resp = [
                'status' => false,
                'message' => $error,
            ];
        }

        return (new Response($resp, 200))->header('Content-Type', 'application/json');
    }

    /**
     * @param int $status
     * @param string $text
     * @return Response
     */
    protected static function text(int $status, string $text): Response
    {
        return (new Response($text, $status))->header('Content-Type', 'text/plain');
    }

    protected function isSeniorStaff(Request $request): bool
    {
        $user = $request->user();

        if ($user) {
            $player = $user->player ?? false;

            if ($player) {
                $seniorStaff = $player->is_senior_staff ?? false;
                $superAdmin = $player->is_super_admin ?? false;

                return $seniorStaff || $superAdmin || GeneralHelper::isUserRoot($player->steam_identifier);
            }
        }

        return false;
    }

    protected function isSuperAdmin(Request $request): bool
    {
        $user = $request->user();

        if ($user) {
            $player = $user->player ?? false;

            if ($player) {
                $superAdmin = $player->is_super_admin ?? false;

                return $superAdmin || GeneralHelper::isUserRoot($player->steam_identifier);
            }
        }

        return false;
    }
}
