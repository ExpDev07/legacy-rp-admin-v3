<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\PermissionHelper;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\TwitterPostResource;
use App\Player;
use App\TwitterPost;
use App\TwitterUser;
use Illuminate\Http\RedirectResponse;
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

        $query = TwitterPost::query()->orderByDesc('time')->where('is_deleted', '=', '0');

        // Filtering by username.
        if ($username = $request->input('username')) {
            $subQuery = TwitterUser::query();

            if (Str::startsWith($username, '=')) {
                $username = Str::substr($username, 1);
                $subQuery->where('username', $username);
            } else {
                $subQuery->where('username', 'like', "%{$username}%");
            }

            $users = $subQuery->select(['id'])->get()->toArray();
            $ids = !empty($users) ? array_values(array_map(function ($user) {
                return $user['id'];
            }, $users)) : [];

            $query->whereIn('authorId', $ids);
        }

        // Filtering by message.
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

        $end = round(microtime(true) * 1000);

        return Inertia::render('Twitter/Index', [
            'posts'        => $posts,
            'filters'      => $request->all(
                'message',
                'username'
            ),
            'links'        => $this->getPageUrls($page),
            'time'         => $end - $start,
            'userMap'      => TwitterUser::fetchIdMap($posts->toArray($request), 'authorId'),
            'page'         => $page,
        ]);
    }

    /**
     * Shows a certain user and their tweets
     *
     * @param TwitterUser $user
     * @return Response
     */
    public function user(TwitterUser $user): Response
    {
        $page = Paginator::resolveCurrentPage('page');

        $tweets = TwitterPost::query()
            ->where('authorId', '=', $user->id)
            ->where('is_deleted', '=', '0')
            ->select(['id', 'authorId', 'realUser', 'message', 'time', 'likes'])
            ->orderByDesc('time')
            ->limit(15)->offset(($page - 1) * 15)
            ->get();

        $tweet = $tweets->first();

        if (!$tweet) {
            abort(404);
        }

        /**
         * @var $character Character|null
         */
        $character = Character::query()
            ->where('character_id', '=', $user->creator_cid ?? $tweet->realUser)
            ->get()->first();

        if (!$character) {
            abort(404);
        }

        return Inertia::render('Twitter/User', [
            'tweets'    => TwitterPostResource::collection($tweets),
            'character' => new CharacterResource($character),
            'player'    => new PlayerIndexResource($character->player()->get()->first()),
            'user'      => $user->toArray(),
            'links'     => $this->getPageUrls($page),
            'page'      => $page,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param TwitterPost $post
     * @return RedirectResponse
     */
    public function deleteTweet(Request $request, TwitterPost $post): RedirectResponse
    {
        $user = $request->user();
		if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_TWITTER)) {
            abort(401);
        }

        $post->update([
            'is_deleted' => '1',
        ]);

        return back()->with('success', 'Successfully deleted tweet');
    }

}
