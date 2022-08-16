<?php

namespace App;

use App\Helpers\OPFWHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_vehicles';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'vehicle_id';

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
        'owner_cid',
        'garage_identifier',
        'garage_state',
        'garage_impound',
        'model_name',
        'plate',
        'vehicle_deleted',
        'deprecated_damage',
        'deprecated_modifications',
        'deprecated_fuel',
        'emergency_type',
    ];

    /**
     * Get the character that owns this vehicle.
     *
     * @return BelongsTo
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'owner_cid');
    }

    /**
     * Returns the garage name
     *
     * @return string
     */
    public function garage(): string
    {
        if (intval($this->garage_impound) === 1) {
            return 'Impound Lot';
        } else if (intval($this->garage_state) === 0) {
            return 'Out';
        }

        $this->garage_identifier = trim($this->garage_identifier);

        if (is_numeric($this->garage_identifier)) {
            if (intval($this->emergency_type) === 1) {
                return 'PD Garage';
            } else if (intval($this->emergency_type) === 2) {
                return 'EMS Garage';
            }

            switch (intval($this->garage_identifier)) {
                case 1:
                case 2:
                case 3:
                    return 'Impound Lot';
                case 4:
                    return 'Garage A (near court house)';
                case 5:
                    return 'Garage B (near exclusive dealership)';
                case 6:
                    return 'Garage C (the big red building)';
                case 7:
                    return 'Garage D (southside garage)';
                case 8:
                    return 'Garage E (mirror park garage)';
                case 9:
                    return 'Garage F (vinewood garage)';
                case 10:
                    return 'Garage G (near great ocean highway)';
                case 11:
                    return 'Garage H (sandy shores garage)';
                case 12:
                    return 'Garage I (paleto garage)';
                case 14:
                    return 'Garage J (cayo compound)';
                case 15:
                    return 'Garage K (cayo airfield)';
                case 17:
                    return 'LSIA';
                case 18:
                    return 'MRPD';
                case 19:
                    return 'Mount Zonah Medical Center';
                case 21:
                    return 'Luxury Autos';
            }
        }

        return $this->garage_identifier;
    }

    /**
     * Get deprecated_modifications as a formatted array for the frontend
     *
     * @return array
     */
    public function getModifications(): array
    {
        $color = function (int $r, int $g, int $b): string {
            return sprintf("#%02x%02x%02x", $r, $g, $b);
        };
        $isColor = function (array $json, string $key): bool {
            return isset($json[$key]) && is_array($json[$key]) && !empty($json[$key]) && isset($json[$key]['r']) && isset($json[$key]['g']) && isset($json[$key]['b']);
        };

        $json = json_decode($this->deprecated_modifications, true) ?? [];
        $default = '#ffffff';

        return [
            'xenon_headlights' => isset($json['modXenon']) && intval($json['modXenon']) === 1,
            'tire_smoke'       => $isColor($json, 'tireSmokeColor')
                ? $color($json['tireSmokeColor']['r'], $json['tireSmokeColor']['g'], $json['tireSmokeColor']['b'])
                : $default,
            'neon_enabled'     => isset($json['neonEnabled']) && sizeof($json['neonEnabled']) === 4 && $json['neonEnabled'][0] && $json['neonEnabled'][1] && $json['neonEnabled'][2] && $json['neonEnabled'][3],
            'engine'           => isset($json['modEngine']) && is_numeric($json['modEngine']) ? intval($json['modEngine']) + 1 : 0,
            'transmission'     => isset($json['modTransmission']) && is_numeric($json['modTransmission']) ? intval($json['modTransmission']) + 1 : 0,
            'breaks'           => isset($json['modBrakes']) && is_numeric($json['modBrakes']) ? intval($json['modBrakes']) + 1 : 0,
            'neon'             => $isColor($json, 'neonColor')
                ? $color($json['neonColor']['r'], $json['neonColor']['g'], $json['neonColor']['b'])
                : $default,
            'turbo'            => isset($json['modTurbo']) && intval($json['modTurbo']) === 1,
            'suspension'       => isset($json['modSuspension']) && is_numeric($json['modSuspension']) ? intval($json['modSuspension']) + 1 : 0,
            'armor'            => isset($json['modArmor']) && is_numeric($json['modArmor']) ? intval($json['modArmor']) + 1 : 0,
            'tint'             => isset($json['windowTint']) && is_numeric($json['windowTint']) ? intval($json['windowTint']) : 0,
            'plate_type'       => isset($json['plateIndex']) && is_numeric($json['plateIndex']) ? intval($json['plateIndex']) : 0,
            'horn'             => isset($json['modHorns']) && is_numeric($json['modHorns']) ? intval($json['modHorns']) : -1,
        ];
    }

    /**
     * Sets the vehicles deprecated_modifications based on an array from the frontend.
     * Returns the invalid key if there is one.
     *
     * @param array $mods
     * @return string|null
     */
    public function parseModifications(array $mods): ?string
    {
        $hornMap = self::getHornMap(true);
        $mods = array_map(function ($m) {
            return is_numeric($m) ? intval($m) : $m;
        }, $mods);

        $validate = [
            'tire_smoke'       => !isset($mods['tire_smoke']) || !preg_match('/^#[0-9a-f]{6}$/mi', $mods['tire_smoke']),
            'neon'             => !isset($mods['neon']) || !preg_match('/^#[0-9a-f]{6}$/mi', $mods['neon']),
            'xenon_headlights' => !isset($mods['xenon_headlights']) || !is_bool($mods['xenon_headlights']),
            'neon_enabled'     => !isset($mods['neon_enabled']) || !is_bool($mods['neon_enabled']),
            'turbo'            => !isset($mods['turbo']) || !is_bool($mods['turbo']),
            'engine'           => !isset($mods['engine']) || !is_integer($mods['engine']) || $mods['engine'] < 0 || $mods['engine'] > 5,
            'transmission'     => !isset($mods['transmission']) || !is_integer($mods['transmission']) || $mods['transmission'] < 0 || $mods['transmission'] > 3,
            'breaks'           => !isset($mods['breaks']) || !is_integer($mods['breaks']) || $mods['breaks'] < 0 || $mods['breaks'] > 3,
            'suspension'       => !isset($mods['suspension']) || !is_integer($mods['suspension']) || $mods['suspension'] < 0 || $mods['suspension'] > 4,
            'armor'            => !isset($mods['armor']) || !is_integer($mods['armor']) || $mods['armor'] < 0 || $mods['armor'] > 5,
            'tint'             => !isset($mods['tint']) || !is_integer($mods['tint']) || $mods['tint'] < 0 || $mods['tint'] > 5,
            'plate_type'       => !isset($mods['plate_type']) || !is_integer($mods['plate_type']) || $mods['plate_type'] < 0 || $mods['plate_type'] > 5,
            'horn'             => !isset($mods['horn']) || !is_integer($mods['horn']) || !isset($hornMap[$mods['horn']]),
        ];

        foreach ($validate as $key => $invalid) {
            if ($invalid) {
                return $key;
            }
        }

        $color = function (string $hex): array {
            return [
                'r' => hexdec(substr($hex, 1, 2)),
                'g' => hexdec(substr($hex, 3, 2)),
                'b' => hexdec(substr($hex, 5, 2)),
            ];
        };
        $json = json_decode($this->deprecated_modifications, true) ?? [];

        $json['modXenon'] = $mods['xenon_headlights'] ? 1 : false;
        $json['tireSmokeColor'] = $color($mods['tire_smoke']);
        $json['neonEnabled'] = $mods['neon_enabled'] ? [1, 1, 1, 1] : [false, false, false, false];
        $json['modEngine'] = $mods['engine'] - 1;
        $json['modTransmission'] = $mods['transmission'] - 1;
        $json['modBrakes'] = $mods['breaks'] - 1;
        $json['neonColor'] = $color($mods['neon']);
        $json['modTurbo'] = $mods['turbo'] ? 1 : false;
        $json['modSuspension'] = $mods['suspension'] - 1;
        $json['modArmor'] = $mods['armor'] - 1;
        $json['windowTint'] = $mods['tint'];
        $json['plateIndex'] = $mods['plate_type'];
        $json['modHorns'] = $mods['horn'];

        $this->deprecated_modifications = json_encode($json);

        return null;
    }

    /**
     * Returns a map of all available vehicle horns
     *
     * @param bool $validationMap
     * @return array
     */
    public static function getHornMap(bool $validationMap = false): array
    {
        $hornMaps = json_decode(file_get_contents(__DIR__ . '/../helpers/vehicle-horns.json'), true);
        $horns = [];

        if ($validationMap) {
            foreach ($hornMaps as $map) {
                foreach ($map as $key => $horn) {
                    $horns[$key] = $horn;
                }
            }
        } else {
            foreach ($hornMaps as $group => $map) {
                $horns[$group] = [];

                foreach ($map as $key => $horn) {
                    $horns[$group][] = [
                        'index' => $key,
                        'label' => $horn,
                    ];
                }
            }
        }

        return $horns;
    }

    public static function getVehiclePrices(): array
    {
        $vehicles = OPFWHelper::getVehiclesJSON(Server::getFirstServer() ?? '');

        $prices = [];

        if (isset($vehicles['pdm'])) {
            foreach ($vehicles['pdm'] as $vehicle) {
                $prices[$vehicle['modelName']] = intval($vehicle['price']);
            }
        }

        if (isset($vehicles['edm'])) {
            foreach ($vehicles['edm'] as $vehicle) {
                $prices[$vehicle['modelName']] = intval($vehicle['price']);
            }
        }

        return $prices;
    }

    public static function getTotalVehicleValue(?int $characterId = null): int
    {
        $prices = Vehicle::getVehiclePrices();

        $query = Vehicle::query()
            ->where('vehicle_deleted', '=', '0');

        if ($characterId) {
            $query->where('owner_cid', '=', $characterId);
        }

        $vehicles = $query->selectRaw('model_name, COUNT(vehicle_id) as `amount`')
            ->groupBy('model_name')
            ->get()->toArray();

        $total = 0;

        foreach ($vehicles as $vehicle) {
            $model = $vehicle['model_name'];

            if (isset($prices[$model])) {
                $total += $prices[$model] * intval($vehicle['amount']);
            }
        }

        return $total;
    }

}
