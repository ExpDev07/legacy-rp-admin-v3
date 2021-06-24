<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Logs a character edit from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $characterId
     * @param array $changedFields
     */
    public static function logCharacterEdit(string $fromIdentifier, string $toIdentifier, string $characterId, array $changedFields)
    {
        if (empty($changedFields)) {
            return;
        }

        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerLogName($toIdentifier);

        $log = $from . ' edited character ' . $characterId . ' of ' . $to . '. Fields changed: `' . implode(', ', $changedFields) . '`';
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Character Edit');
    }

    /**
     * Logs tattoo removals from the panel
     *
     * @param string $fromIdentifier
     * @param string $toIdentifier
     * @param string $characterId
     */
    public static function logTattooRemoval(string $fromIdentifier, string $toIdentifier, string $characterId)
    {
        $from = self::resolvePlayerLogName($fromIdentifier);
        $to = self::resolvePlayerLogName($toIdentifier);

        $log = $from . ' removed all tattoos character ' . $characterId . ' of ' . $to;
        self::createLog($fromIdentifier, $toIdentifier, $log, 'Tattoo removal');
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

    private static function resolvePlayerLogName(string $identifier): string
    {
        $player = Player::query()->where('steam_identifier', $identifier)->first();
        $playerName = $player ? $player->player_name : 'Unknown';

        return $playerName . ' (' . $identifier . ')';
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
        PanelLog::query()->create([
            'source_identifier' => $source,
            'target_identifier' => $target,
            'log'               => $log,
            'action'            => $action,
        ]);
    }

}
