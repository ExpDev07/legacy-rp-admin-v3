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

		$url = str_replace("empty=true", "", $url);

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

    /**
     * @param int $status
     * @param string $text
     * @return Response
     */
    protected static function fakeText(int $status, string $text): Response
    {
		$style = 'html,body{width:100%;height:100%;background:#1C1B22;color:#fbfbfe;font-family:monospace;font-size:13px;white-space:pre-wrap;margin:0;box-sizing:border-box}body{padding:8px}a{text-decoration:none;color:#909bff}';

		$text = '<style>' . $style . '</style>' . $text;

        return (new Response($text, $status))->header('Content-Type', 'text/html');
    }

    protected function isSeniorStaff(Request $request): bool
    {
        $user = $request->user();

        if ($user) {
            $player = $user->player ?? false;

            if ($player) {
                $seniorStaff = $player->is_senior_staff ?? false;
                $superAdmin = $player->is_super_admin ?? false;

                return $seniorStaff || $superAdmin || GeneralHelper::isUserRoot($player->license_identifier);
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

                return $superAdmin || GeneralHelper::isUserRoot($player->license_identifier);
            }
        }

        return false;
    }

	protected function time_elapsed_string($datetime, $full = false)
	{
		$now = new \DateTime;
		$ago = new \DateTime($datetime);

		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);

		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);

		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
}
