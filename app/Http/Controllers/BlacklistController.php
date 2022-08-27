<?php

namespace App\Http\Controllers;

use App\BlacklistedIdentifier;
use App\Http\Requests\BlacklistedIdentifierStoreRequest;
use App\Http\Resources\BlacklistedIdentifierResource;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlacklistController extends Controller
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
        $isOrdered = false;

        $query = BlacklistedIdentifier::query();

        // Filtering by creator.
        if ($creator = $request->input('creator')) {
            $query->where('creator_identifier', '=', $creator);
        }

        // Filtering by identifier.
        if ($identifier = $request->input('identifier')) {
            $query->where('identifier', '=', $identifier);
        }

        // Filtering by reason.
        if ($reason = $request->input('reason')) {
            if (Str::startsWith($reason, '=')) {
                $reason = Str::substr($reason, 1);
                $query->where('reason', '=', $reason);
            } else {
                $query->where('reason', 'like', "%{$reason}%");
            }
        }

        // Filtering by note.
        if ($note = $request->input('note')) {
            if (Str::startsWith($note, '=')) {
                $note = Str::substr($note, 1);
                $query->where('note', '=', $note);
            } else {
                $query->where('note', 'like', "%{$note}%");
            }
        }

        $query->select([
            'blacklist_id', 'identifier', 'creator_identifier', 'reason', 'note', 'timestamp',
        ]);

        $page = Paginator::resolveCurrentPage('page');
        $query->limit(15)->offset(($page - 1) * 15);

        $identifiers = $query->get();

        $end = round(microtime(true) * 1000);

        return Inertia::render('Blacklist/Index', [
            'identifiers' => BlacklistedIdentifierResource::collection($identifiers),
            'filters' => [
                'creator' => $request->input('creator'),
                'identifier' => $request->input('identifier'),
                'reason' => $request->input('reason'),
                'note' => $request->input('note'),
            ],
            'links' => $this->getPageUrls($page),
            'time' => $end - $start,
            'page' => $page,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BlacklistedIdentifierStoreRequest $request
     * @return RedirectResponse
     */
    public function store(BlacklistedIdentifierStoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        $identifier = $request->validated();
        $identifier['creator_identifier'] = $user->player->steam_identifier;
        $identifier['identifier'] = strtolower($identifier['identifier']);

        if (!Player::isValidIdentifier($identifier['identifier'])) {
            return back()->with('error', 'Invalid identifier.');
        }

        $found = BlacklistedIdentifier::query()->where('identifier', '=', $identifier['identifier'])->get()->first();
        if ($found) {
            return back()->with('error', 'Identifier is already blacklisted.');
        }

        BlacklistedIdentifier::query()->updateOrCreate($identifier);

        return back()->with('success', 'The identifier has successfully been blacklisted.');
    }

    public function import(Request $request): RedirectResponse
    {
        $user = $request->user();

        $text = str_replace("\r\n", "\n", $request->input('text', ''));

        $lines = explode("\n", $text);

        if (empty($lines) || $lines[0] !== "steam_identifier,reason") {
            return back()->with('error', 'Invalid file submitted.');
        }

        unset($lines[0]);

        $blacklist = BlacklistedIdentifier::query()->select(["identifier"])->get();
        $existing = [];

        foreach ($blacklist as $entry) {
            $existing[$entry->identifier] = true;
        }

        $insert = [];

        $imported = 0;
        $skipped = 0;
        $invalid = 0;

        $date = date('d.m.Y - H:i:s');

        foreach ($lines as $line) {
            $identifier = explode(",", $line)[0];

            if (isset($existing[$identifier]) && $existing[$identifier]) {
                $skipped++;
            } else {
                if (Str::startsWith($identifier, "steam:")) {
                    $insert[] = [
                        'identifier' => $identifier,
                        'creator_identifier' => $user->player->steam_identifier,
                        'reason' => 'Modding',
                        'note' => "IMPORTED-BANS (" . $date . ")"
                    ];

                    $imported++;
                } else {
                    $invalid++;
                }
            }
        }

        if (!empty($insert)) {
            BlacklistedIdentifier::query()->insert($insert);
        }

        return back()->with('success', 'Imported ' . $imported . ' identifiers, skipped ' . $skipped . ' existing and ' . $invalid . ' invalid ones.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BlacklistedIdentifier $identifier
     * @return RedirectResponse
     */
    public function destroy(BlacklistedIdentifier $identifier): RedirectResponse
    {
        $identifier->forceDelete();

        return back()->with('success', 'The identifier has successfully been removed.');
    }

}
