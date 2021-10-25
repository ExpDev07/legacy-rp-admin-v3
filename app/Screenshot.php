<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Screenshot extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webpanel_screenshots';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Whether to use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'steam_identifier',
        'filename',
        'note',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    public static function getAllScreenshotsForPlayer(string $steam): array
    {
        $characters = array_map(
            function ($character) {
                return $character['character_id'];
            },
            Character::query()
                ->where('steam_identifier', '=', $steam)
                ->select(['character_id'])
                ->get()->toArray()
        );

        $attached = self::query()
            ->where('steam_identifier', '=', $steam)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();

        if (!empty($characters)) {
            $system = DB::table('system_screenshots')
                ->whereIn('character_id', $characters)
                ->orderByDesc('id')
                ->select(['url', 'details', 'created_at'])
                ->get()->toArray();

            foreach ($system as &$entry) {
                $entry->system = true;
                $entry->note = $entry->details;

                unset($entry->details);

                if (!isset($entry->created_at) || !$entry->created_at) {
                    $entry->created_at = null;
                }
            }

            $attached = array_merge($attached, $system);

            usort($attached, function($a, $b) {
                return ($b->created_at ?? 0) - ($a->created_at ?? 0);
            });
        }

        return $attached;
    }

}
