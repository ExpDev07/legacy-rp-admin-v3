<?php


namespace App\Util;

use Illuminate\Support\Collection;

/**
 * Custom inspiring implementation.
 *
 * @package App\Util
 */
class Inspiring
{

    /**
     * Gets the quotes.
     *
     * @var array
     */
    private static $quotes = [
        'When there is no desire, all things are at peace. - Laozi',
        'Simplicity is the essence of happiness. - Cedric Bledsoe',
        'Smile, breathe, and go slowly. - Thich Nhat Hanh',
        'Simplicity is an acquired taste. - Katharine Gerould',
        'Well begun is half done. - Aristotle',
        'He who is contented is rich. - Laozi',
        'An unexamined life is not worth living. - Socrates',
        'Order your soul. Reduce your wants. - Augustine',
        'Be present above all else. - Naval Ravikant',
        'No surplus words or unnecessary actions. - Marcus Aurelius',
    ];

    /**
     * Gets a random quote.
     */
    public static function quote()
    {
        return Collection::make(Inspiring::$quotes)->random();
    }

}