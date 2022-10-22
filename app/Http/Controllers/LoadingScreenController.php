<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class LoadingScreenController extends Controller
{
    /**
     * Renders the loading screen pictures.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_LOADING_SCREEN)) {
            abort(401);
        }

        $pictures = DB::table('loading_screen_images')->orderByDesc('id')->get();

        return Inertia::render('LoadingScreen/Index', [
            'pictures' => $pictures
        ]);
    }

    /**
     * Delete a loading screen picture.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request, int $id): RedirectResponse
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_LOADING_SCREEN)) {
            abort(401);
        }

        DB::table('loading_screen_images')->where('id', $id)->delete();

        return back()->with('success', 'The picture has successfully been deleted.');
    }

    /**
     * Add a loading screen picture.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function add(Request $request): RedirectResponse
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_LOADING_SCREEN)) {
            abort(401);
        }

        $url = trim($request->input('image_url'));

        if (!$url || !Str::startsWith($url, "https://")) {
            return back()->with('error', 'Invalid url.');
        }

        DB::table('loading_screen_images')-> insert([
            'image_url' => $url
        ]);

        return back()->with('success', 'The picture has successfully been added.');
    }
}
