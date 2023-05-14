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
use Illuminate\Support\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const GraphColors = [
        "blue"   => '5383c6',
        "green"  => '66c653',
        "yellow" => 'c6b353',
        "red"    => 'c65353',
        "purple" => '9f53c6'
    ];

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
		$style = 'html,body{width:100%;background:#1C1B22;color:#fbfbfe;font-family:monospace;font-size:13px;white-space:pre-wrap;margin:0;box-sizing:border-box}body{padding:8px}a{text-decoration:none;color:#909bff}';

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

	protected function formatTimestamp($timestamp)
	{
		if ($timestamp instanceof Carbon) {
			$timestamp = $timestamp->getTimestamp();
		}

		$seconds = time() - $timestamp;

		return $this->formatSeconds($seconds) . " ago";
	}

	protected function formatSeconds($seconds)
	{
		$string = [
			'year' => 60*60*24*365,
			'month' => 60*60*24*30,
			'week' => 60*60*24*7,
			'day' => 60*60*24,
			'hour' => 60*60,
			'minute' => 60
		];

		foreach ($string as $label => $divisor) {
			$value = floor($seconds / $divisor);

			if ($value > 0) {
				$label = $value > 1 ? $label . 's' : $label;

				return $value . ' ' . $label;
			}
		}

		return $seconds . ' second' . ($seconds > 1 ? 's' : '');
	}

    protected function formatSecondsMinimal($seconds)
    {
        $seconds = floor($seconds);

        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;

        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $time = "";

        if ($hours > 0) {
            $time .= $hours . "h ";
        }

        if ($minutes > 0) {
            $time .= $minutes . "m ";
        }

        if ($seconds > 0) {
            $time .= $seconds . "s";
        }

        return "~" . $time;
    }

	private function brighten($rgb, $amount) {
        foreach ($rgb as &$color) {
            $color = max(0, min(255, $color + $amount));
        }

        return $rgb;
	}

	protected function renderGraph(array $entries, string $title, array $colors = ["blue"])
	{
        $entries = array_map(function ($entry) {
            if (!is_array($entry)) {
                $entry = [$entry];
            }

            return $entry;
        }, $entries);

		$size = max(1024, sizeof($entries));
		$entryWidth = floor($size / sizeof($entries));

        $size = $entryWidth * sizeof($entries);
		$height = floor($size * 0.6);

		$max = ceil(max(array_map(function ($entry) {
            return array_sum($entry);
        }, $entries)) * 1.1);

		$image = imagecreatetruecolor($size, $height);

		$black = imagecolorallocate($image, 28, 27, 34);
		imagefill($image, 0, 0, $black);

        if ($max > 0) {
            for ($g = 0; $g < sizeof($entries[0]); $g++) {
                $key = $colors[$g] ?? 'blue';

                $hex = self::GraphColors[$key];

                $colors[$g] = array_map('hexdec', str_split($hex, 2));
            }

            for ($i = 0; $i < $size; $i++) {
                $entry = $entries[$i] ?? [];

                $y = $height;

                foreach($entry as $index => $value) {
                    if ($value === 0) continue;

                    $percentage = $value / $max;

                    $x = $i * $entryWidth;

                    $x2 = $x + $entryWidth - 1;
                    $y2 = $y - ($height * $percentage);

                    $color = $colors[$index];

                    if ($i % 2 === 0) {
                        $color = $this->brighten($color, 8);
                    } else {
                        $color = $this->brighten($color, -4);
                    }

                    $color = imagecolorallocate($image, $color[0], $color[1], $color[2]);

                    imagefilledrectangle($image, $x, $y, $x2, $y2, $color);

                    $y = $y2;
                }
            }
        } else {
            $noDataText = imagecolorallocate($image, 231, 177, 177);

		    imagestring($image, 4, floor($size / 2) - 26, floor($height / 2), "No Data", $noDataText);
        }

		$text = imagecolorallocate($image, 177, 198, 231);
		imagestring($image, 2, 4, 2, $title, $text);

		$text = imagecolorallocate($image, 255, 200, 200);
		imagestring($image, 2, 0, 2, "0", $text);

		ob_start();

		imagepng($image);

		$image_data = ob_get_contents();
		ob_end_clean();

		$image_data_base64 = base64_encode($image_data);

		imagedestroy($image);

		return "data:image/png;base64,{$image_data_base64}";
	}
}
