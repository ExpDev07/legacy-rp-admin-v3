<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @package App
 */
class Inventory
{
    /**
     * The Inventory descriptor (e.g. "trunk-4-15214")
     *
     * @var string
     */
    public string $descriptor;

    /**
     * The type of inventory. Can be ("ground", "character", "glovebox", "trunk")
     *
     * @var string
     */
    public string $type;

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
     * Inventory constructor.
     *
     * @param string $descriptor
     */
    public function __construct(string $descriptor)
    {
        $this->descriptor = $descriptor;
    }
}
