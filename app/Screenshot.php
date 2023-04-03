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
        'license_identifier',
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

    public static function getAllScreenshotsForPlayer(string $license): array
    {
        $characters = array_map(
            function ($character) {
                return $character['character_id'];
            },
            Character::query()
                ->where('license_identifier', '=', $license)
                ->select(['character_id'])
                ->get()->toArray()
        );

        $attached = self::query()
            ->where('license_identifier', '=', $license)
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
