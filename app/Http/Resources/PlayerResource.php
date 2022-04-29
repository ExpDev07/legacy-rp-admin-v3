<?php

namespace App\Http\Resources;

use App\Helpers\GeneralHelper;
use App\Player;
use App\Warning;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $path = explode('/', $request->path());
        $loadStatus = sizeof($path) === 2 && $path[0] = 'players';

        $status = $loadStatus ? Player::getOnlineStatus($this->steam_identifier, false) : null;

        $identifiers = is_array($this->player_aliases) ? $this->player_aliases : json_decode($this->player_aliases, true);
        $enabledCommands = is_array($this->enabled_commands) ? $this->enabled_commands : json_decode($this->enabled_commands, true);

        $drug = (isset($this->panel_drug_department) && $this->panel_drug_department) || GeneralHelper::isUserRoot($this->steam_identifier);

        return [
            'id'              => $this->user_id,
            'avatar'          => $this->avatar,
            'discord'         => $this->getDiscordID(),
            'steamIdentifier' => $this->steam_identifier,
            'steam36'         => base_convert(str_replace('steam:', '', $this->steam_identifier), 16, 36),
            'playerName'      => $this->player_name,
            'playTime'        => $this->playtime,
            'lastConnection'  => $this->last_connection,
            'steamProfileUrl' => $this->getSteamProfileUrl(),
            'isTrusted'       => $this->is_trusted,
            'isDebugger'      => $this->isDebugger(),
            'isPanelTrusted'  => $this->isPanelTrusted(),
            'isStaff'         => $this->isStaff(),
            'isSuperAdmin'    => $this->isSuperAdmin(),
            'isRoot'          => $this->isRoot(),
            'isBanned'        => $this->isBanned(),
            'isSoftBanned'    => $this->is_soft_banned,
            'warnings'        => $this->warnings()->whereIn('warning_type', [Warning::TypeStrike, Warning::TypeWarning])->count(),
            'ban'             => new BanResource($this->getActiveBan()),
            'status'          => $status,
            'fakeName'        => $status ? $status->fakeName : false,
            'playerAliases'   => $identifiers ? array_values(array_unique(array_filter($identifiers, function($e) {
                return $e !== $this->player_name && str_replace('?', '', $e) !== '';
            }))) : [],
            'enabledCommands' => $enabledCommands ?? [],
            'panelDrugDepartment' => $drug,
            'tag'             => $this->panel_tag
        ];
    }

}
