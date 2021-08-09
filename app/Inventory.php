<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\In;

/**
 * @package App
 */
class Inventory
{
    const InventoryTypes = [
        'character',
        'trunk',
        'glovebox',
        'ground',
        'property',
        'locker-\w+?',
        'motel-\w+?',
        'evidence',
    ];

    /**
     * The Inventory title (e.g. "trunk-15214")
     *
     * @var string
     */
    public string $title = '';

    /**
     * The Inventory descriptor (e.g. "trunk-4-15214:31")
     *
     * @var string
     */
    public string $descriptor = '';

    /**
     * The type of inventory. Can be ("ground", "character", "glovebox", "trunk", etc.)
     *
     * @var string
     */
    public string $type = '';

    /**
     * The id of the inventory
     *
     * @var string
     */
    public string $id = '';

    /**
     * Contains additional information
     *
     * @var array
     */
    public array $more_info = [];

    /**
     * The Character associated with this inventory
     *
     * @var ?Character
     */
    public ?Character $character;

    /**
     * The Vehicle associated with this inventory
     *
     * @var ?Vehicle
     */
    public ?Vehicle $vehicle;

    /**
     * The Property associated with this inventory
     *
     * @var ?Property
     */
    public ?Property $property;

    /**
     * The JSON stored in helpers/op-fw_vehicles.json
     *
     * @var array
     */
    private static array $vehicleJSON = [];

    /**
     * Inventory constructor.
     *
     * @param string $descriptor
     */
    public function __construct(string $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * Parses log details
     *
     * @param string $details
     * @param string $movement
     * @return Inventory
     */
    public static function parseLogDetails(string $details, string $movement): Inventory
    {
        $rgx = '/' . preg_quote($movement) . ' (inventory )?((' . implode('|', self::InventoryTypes) . ')-(\d+-)?([0-9A-Z]+):(\d+))/mi';
        preg_match($rgx, $details, $matches);

        if (sizeof($matches) !== 7) {
            return new Inventory('unknown');
        }

        $type = $matches[3];
        $server = $matches[4];
        $id = $matches[5];
        $slot = $matches[6];

        $descriptor = $type . '-' . $server . $id . ':' . $slot;

        if (!is_numeric($slot)) {
            return new Inventory('unknown');
        }

        $inventory = new Inventory($descriptor);
        $inventory->type = $type;
        $inventory->title = $type . '-' . $server . $id;
        $inventory->id = $id;

        if (Str::contains($inventory->type, 'motel')) {
            $inventory->type = explode('-', $inventory->type)[0];
        }

        switch ($type) {
            case 'ground':
            case 'character':
            case 'locker-police':
            case 'locker-mechanic':
            case 'locker-ems':
                if (!is_numeric($id)) {
                    return new Inventory($descriptor);
                }
                break;
            case 'property':
                $inventory->id = str_replace('-', '', $server);
                if (!is_numeric($inventory->id)) {
                    return new Inventory($descriptor);
                }
                break;
            case 'trunk':
            case 'glovebox':
                if (!is_numeric($id) && !preg_match('/^\d{2}[a-z]{3}\d{3}$/mi', $id)) {
                    return new Inventory($descriptor);
                }

                $inventory->more_info = [
                    'type' => 'Unknown',
                ];

                $type = intval($server);

                $json = self::getOPFWVehicleJSON();
                if ($json && $type >= 0 && $type <= 22) {
                    $inventory->more_info['type'] = $json['labels'][$type];
                }

                break;
            default:
                return $inventory;
        }

        return $inventory;
    }

    /**
     * Returns the json stored at /helpers/op-fw_vehicles.json
     *
     * @return array
     */
    public static function getOPFWVehicleJSON(): array
    {
        if (!self::$vehicleJSON) {
            $json = json_decode(file_get_contents(__DIR__ . '/../helpers/op-fw_vehicles.json'), true);

            if ($json && !empty($json['map']) && !empty($json['labels'])) {
                self::$vehicleJSON = $json;
            }
        }

        return self::$vehicleJSON;
    }

    /**
     * Parses an inventory descriptor
     *
     * @param string $descriptor
     * @return Inventory
     */
    public static function parseDescriptor(string $descriptor): Inventory
    {
        if (!Str::contains($descriptor, ':')) {
            $descriptor .= ':1';
        }

        return self::parseLogDetails('from inventory ' . $descriptor, 'from');
    }

    /**
     * Parses the moved item from log details
     *
     * @param string $details
     * @return string
     */
    public static function parseItem(string $details): string
    {
        preg_match('/moved (\d+x .+?) to/mi', $details, $matches);

        return sizeof($matches) === 2 ? $matches[1] : 'N/A';
    }

    /**
     * Loads
     *
     * @return Inventory
     */
    public function get(): Inventory
    {
        switch ($this->type) {
            case 'character':
            case 'locker-police':
            case 'locker-mechanic':
            case 'locker-ems':
                $query = Character::query()->where('character_id', $this->id);
                $this->character = $query->first();
                return $this;
            case 'property':
                $query = Property::query()->where('property_id', $this->id);
                $this->property = $query->first();

                if ($this->property) {
                    $this->character = Character::query()->where('character_id', $this->property->property_renter_cid)->first();
                }

                return $this;
            case 'trunk':
            case 'glovebox':
                $query = Vehicle::query();
                if (is_numeric($this->id)) {
                    $query->where('vehicle_id', $this->id);
                } else {
                    $query->where('plate', $this->id);
                }
                $this->vehicle = $query->first();

                if ($this->vehicle) {
                    $this->character = $this->vehicle->character()->first();
                    $this->more_info['garage'] = $this->vehicle->garage();
                }
                return $this;
            default:
                return $this;
        }
    }
}
