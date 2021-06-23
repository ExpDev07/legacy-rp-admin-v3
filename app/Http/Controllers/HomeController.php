<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{

    /**
     * Renders the home page.
     *
     * @return Response
     */
    public function render(): Response
    {
        if (Cache::store('file')->has('inspiring_quote')) {
            $quote = Cache::store('file')->get('inspiring_quote');
        } else {
            $json = json_decode(file_get_contents(__DIR__ . '/../../../helpers/quotes.json'), true);

            if ($json) {
                $quote = $json[array_rand($json)];
                Cache::store('file')->put('inspiring_quote', $quote, 12 * 60 * 60);
            } else {
                $quote = [
                    'quote' => 'Quote machine broke',
                    'author' => 'Twoot'
                ];
            }
        }

        return Inertia::render('Home', [
            'quote' => $quote
        ]);
    }

}
