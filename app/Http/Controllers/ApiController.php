<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ApiController extends Controller
{
    public function playerClassifierJSON(): Response
    {
        $file = __DIR__ . '/../../../helpers/classifier.json';

        $json = file_get_contents($file);

        $size = filesize($file);

        return (new Response($json, 200))
            ->header('Content-Type', 'application/json')
            ->header('Content-Length', $size)
            ->header('Cache-Control', 'max-age=2592000');
    }
}
