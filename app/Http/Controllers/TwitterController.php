<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Resources\LogResource;
use App\Http\Resources\TwitterPostResource;
use App\Log;
use App\Player;
use App\TwitterPost;
use App\TwitterUser;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TwitterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $query = TwitterPost::query()->orderByDesc('time');

        // Filtering by action.
        if ($message = $request->input('message')) {
            if (Str::startsWith($message, '=')) {
                $message = Str::substr($message, 1);
                $query->where('message', $message);
            } else {
                $query->where('message', 'like', "%{$message}%");
            }
        }

        $page = Paginator::resolveCurrentPage('page');

        $query->select(['id', 'authorId', 'realUser', 'message', 'time', 'likes']);
        $query->limit(15)->offset(($page - 1) * 15);

        $posts = TwitterPostResource::collection($query->get());

        $url = preg_replace('/[&?]page=\d+/m', '', $_SERVER['REQUEST_URI']);
        if (Str::contains($url, '?')) {
            $url .= '&';
        } else {
            $url .= '?';
        }
        $next = $url . 'page=' . ($page + 1);
        $prev = $url . 'page=' . ($page - 1);

        $end = round(microtime(true) * 1000);

        return Inertia::render('Twitter/Index', [
            'posts'        => $posts,
            'filters'      => $request->all(
                'message'
            ),
            'links'        => [
                'next' => $next,
                'prev' => $prev,
            ],
            'time'         => $end - $start,
            'characterMap' => Character::fetchIdNameMap($posts->toArray($request), 'realUser'),
            'userMap'      => TwitterUser::fetchIdMap($posts->toArray($request), 'authorId'),
            'page'         => $page,
        ]);
    }

}
