<?php

/**
 * Gets the model name from the vehicle JOAAT hash. Returns the provided string if no match were found.
 *
 * @param string $hash
 * @return string
 */
function vehicle_model_name(string $hash): string
{
    // Vehicles we know are stored as JOAAT.
    $vehicle_model_hashes = [
        '1162065741' => 'Rumpo',
        '-956048545' => 'Taxi',
        '1353720154' => 'Flatbed',
        '-2137348917' => 'Phantom',
    ];

    return $vehicle_model_hashes[$hash] ?? $hash;
}
