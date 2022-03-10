<?php

namespace App;

use App\Helpers\GeneralHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A panel action that has been logged.
 *
 * @package App
 */
class PanelLog extends Model
{
    use HasFactory;

    /**
     * Column name for when the model was created.
     */
    const CREATED_AT = 'timestamp';

    /**
     * Column name for when the model was last updated.
     */
    const UPDATED_AT = 'timestamp';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'panel_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_identifier',
        'target_identifier',
        'timestamp',
        'log',
        'action',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Returns all related identifiers
     *
     * @return array
     */
    public function identifiers(): array
    {
        return array_unique([$this->source_identifier, $this->target_identifier]);
    }

    /**
     * Removes all panel logs older than 1 month
     */
    private static function doCleanup()
    {
        self::query()->where('timestamp', '<=', Carbon::now()->subMonths(1))->delete();
    }

    /**
     * Logs a character edit from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $character
     * @param array $changedFields
     */
    public static function logCharacterEdit(string $fromIdentifier, string $toIdentifier, string $character, array $changedFields)
    {
        if (empty($changedFields)) {
            return;
        }

        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerCharacterLogName($toIdentifier, $character);

        $log = $from . ' edited ' . $to . '. Fields changed: `' . implode(', ', $changedFields) . '`';
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Character Edit');
    }

    /**
     * Logs tattoo removals from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $character
     * @param string $zone
     */
    public static function logTattooRemoval(string $fromIdentifier, string $toIdentifier, string $character, string $zone)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerCharacterLogName($toIdentifier, $character);

        $log = $from . ' removed all tattoos of ' . $to . ' in the zone `' . $zone . '`';
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Tattoo Removal');
    }

    /**
     * Logs spawn resets from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $character
     * @param string $spawn
     */
    public static function logSpawnReset(string $fromIdentifier, string $toIdentifier, string $character, string $spawn)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerCharacterLogName($toIdentifier, $character);

        $log = $from . ' set the spawn point of ' . $to . ' to `' . $spawn . '`';
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Spawn Reset');
    }

    /**
     * Logs a staffPM sent from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $message
     */
    public static function logStaffPM(string $fromIdentifier, string $toIdentifier, string $message)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerLogName($toIdentifier);

        $log = $from . ' sent the following message to ' . $to . ': `' . $message . '`';
        self::createLog($fromIdentifier, $toIdentifier, $log, 'StaffPM');
    }

    /**
     * Logs a kick from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $reason
     */
    public static function logKick(string $fromIdentifier, string $toIdentifier, string $reason)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerLogName($toIdentifier);

        $log = $from . ' kicked ' . $to . ' with the reason: `' . $reason . '`';
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Kicked Player');
    }

    /**
     * Logs a revive from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     */
    public static function logRevive(string $fromIdentifier, string $toIdentifier)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerLogName($toIdentifier);

        $log = $from . ' revived ' . $to;
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Revived Player');
    }

    /**
     * Logs a license add from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $character
     * @param string $license
     */
    public static function logLicenseAdd(string $fromIdentifier, string $toIdentifier, string $character, string $license)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerCharacterLogName($toIdentifier, $character);

        $log = $from . ' added the license `' . $license . '` to ' . $to;
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Added License');
    }

    /**
     * Logs a license remove from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $character
     */
    public static function logLicenseRemove(string $fromIdentifier, string $toIdentifier, string $character)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerCharacterLogName($toIdentifier, $character);

        $log = $from . ' removed all licenses from ' . $to;
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Removed Licenses');
    }

    /**
     * Logs a character unload from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $character
     * @param string $reason
     */
    public static function logUnload(string $fromIdentifier, string $toIdentifier, string $character, string $reason)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerCharacterLogName($toIdentifier, $character);

        $reason = $reason ? ' with the reason `' . $reason . '`' : ' with no reason';

        $log = $from . ' unloaded ' . $to . $reason;
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Unloaded Character');
    }

    /**
     * Returns "Twoot (steam:11000010d322da9)"
     *
     * @param string $identifier
     * @return string
     */
    private static function resolvePlayerLogName(string $identifier): string
    {
        $player = Player::query()->where('steam_identifier', $identifier)->first();
        $playerName = $player ? $player->player_name : 'Unknown';

        return $playerName . ' (' . $identifier . ')';
    }

    /**
     * Returns "Twoot (steam:11000010d322da9)'s character (#739)"
     *
     * @param string $identifier
     * @param string $character
     * @return string
     */
    private static function resolvePlayerCharacterLogName(string $identifier, string $character): string
    {
        return self::resolvePlayerLogName($identifier) . '\'s character (#' . $character . ')';
    }

    /**
     * Creates a log entry
     *
     * @param string $source
     * @param string $target
     * @param string $log
     * @param string $action
     */
    private static function createLog(string $source, string $target, string $log, string $action)
    {
        if (!GeneralHelper::isUserRoot($source)) {
            self::query()->create([
                'source_identifier' => $source,
                'target_identifier' => $target,
                'log' => $log,
                'action' => $action,
            ]);
        }

        self::doCleanup();
    }

}
