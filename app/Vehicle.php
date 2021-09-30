<?php

namespace App;

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
        'model_name',
        'plate',
        'vehicle_deleted',
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
        $this->garage_identifier = trim($this->garage_identifier);

        if (is_numeric($this->garage_identifier)) {
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
            }
        }

        return $this->garage_identifier;
    }

}
