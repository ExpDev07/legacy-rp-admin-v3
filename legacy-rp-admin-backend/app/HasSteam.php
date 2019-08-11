<?php


namespace App;

use SteamID;

/**
 * Something that holds a steam profile.
 *
 * @package App
 */
trait HasSteam
{

    /**
     * The link used for Steam's new invite code.
     *
     * @var string
     */
    protected static $STEAM_INVITE_LINK = 'http://s.team/p/';

    /**
     * Gets the whole steam id instance. Can be used to get multiple different types of steam IDs.
     *
     * @return SteamID The steam id.
     */
    public function steam_id() {
        return new SteamID($this['account_id']);
    }

    /**
     * Gets a link to the user's steam profile.
     *
     * @return string The link.
     */
    public function steam_profile()
    {
        // Per https://github.com/xPaw/SteamID.php, we should use the "RenderSteamInvite" method to get the code which
        // can be used with the steam invite/profile link.
        return self::$STEAM_INVITE_LINK . $this->steam_id()->RenderSteamInvite();
    }

}