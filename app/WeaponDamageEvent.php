<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WeaponDamageEvent extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'weapon_damage_events';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Whether to use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

	const Weapons = [
		1752584910 => "weapon_stinger",
		-1660422300 => "weapon_mg",
		-1090665087 => "weapon_vehicle_rocket",
		992551041 => "weapon_addon_tacknife",
		2100324592 => "weapon_addon_glock",
		-1853920116 => "weapon_navyrevolver",
		-1045183535 => "weapon_revolver",
		1627465347 => "weapon_gusenberg",
		-581044007 => "weapon_machete",
		1119849093 => "weapon_minigun",
		911657153 => "weapon_stungun",
		-10959621 => "weapon_drowning",
		964555122 => "weapon_addon_gardonepistol",
		1593441988 => "weapon_combatpistol",
		-1074790547 => "weapon_assaultrifle",
		-1810795771 => "weapon_poolcue",
		1141786504 => "weapon_golfclub",
		961495388 => "weapon_assaultrifle_mk2",
		1259576109 => "vehicle_weapon_player_bullet",
		1186503822 => "vehicle_weapon_player_buzzard",
		-1955384325 => "weapon_bleeding",
		-1834847097 => "weapon_dagger",
		584646201 => "weapon_appistol",
		-771403250 => "weapon_heavypistol",
		1233104067 => "weapon_flare",
		-268631733 => "vehicle_weapon_player_laser",
		406929569 => "weapon_fertilizercan",
		743550225 => "weapon_tiger_shark",
		137902532 => "weapon_vintagepistol",
		-1263987253 => "weapon_hammerhead_shark",
		-1716189206 => "weapon_knife",
		600439132 => "weapon_ball",
		1432025498 => "weapon_pumpshotgun_mk2",
		1950855132 => "weapon_addon_pp19",
		-340621788 => "weapon_addon_vfcombatpistol",
		984333226 => "weapon_heavyshotgun",
		100416529 => "weapon_sniperrifle",
		940833800 => "weapon_stone_hatchet",
		1198879012 => "weapon_flaregun",
		1305664598 => "weapon_grenadelauncher_smoke",
		-1813897027 => "weapon_grenade",
		1672152130 => "weapon_hominglauncher",
		1223143800 => "weapon_barbed_wire",
		-1746263880 => "weapon_doubleaction",
		-275439685 => "weapon_dbshotgun",
		-100946242 => "weapon_animal",
		317205821 => "weapon_autoshotgun",
		-1706910483 => "weapon_addon_hk416b",
		453432689 => "weapon_pistol",
		-1238556825 => "weapon_rayminigun",
		-266763809 => "weapon_addon_stidvc",
		727643628 => "weapon_ceramicpistol",
		741814745 => "weapon_stickybomb",
		-1716589765 => "weapon_pistol50",
		2138347493 => "weapon_firework",
		-1951375401 => "weapon_flashlight",
		-72657034 => "gadget_parachute",
		324506233 => "weapon_airstrike_rocket",
		1945616459 => "vehicle_weapon_tank",
		2017895192 => "weapon_sawnoffshotgun",
		-947031628 => "weapon_heavyrifle",
		177293209 => "weapon_heavysniper_mk2",
		731779237 => "weapon_addon_mk18",
		-1027401503 => "weapon_addon_sentinelbbshotgun",
		748372090 => "weapon_addon_p320b",
		-1568386805 => "weapon_grenadelauncher",
		-86904375 => "weapon_carbinerifle_mk2",
		-1654528753 => "weapon_bullpupshotgun",
		-608341376 => "weapon_combatmg_mk2",
		94548753 => "weapon_cow",
		-952879014 => "weapon_marksmanrifle",
		-810431678 => "weapon_addon_rc4",
		375527679 => "weapon_passenger_rocket",
		-868994466 => "weapon_hit_by_water_cannon",
		-495648874 => "weapon_cat",
		-1501041657 => "weapon_rabbit",
		-624163738 => "weapon_addon_hk433",
		-441697337 => "weapon_addon_vandal",
		-2000187721 => "weapon_briefcase",
		-924350237 => "weapon_addon_mp9",
		28811031 => "weapon_briefcase_02",
		-1357824103 => "weapon_advancedrifle",
		-270015777 => "weapon_assaultsmg",
		1205296881 => "weapon_pig",
		861723357 => "weapon_boar",
		-1466123874 => "weapon_musket",
		-96609051 => "weapon_killer_whale",
		-1148198339 => "weapon_small_dog",
		205991906 => "weapon_heavysniper",
		-1169823560 => "weapon_pipebomb",
		-1923845809 => "weapon_addon_dp9",
		1171102963 => "weapon_stungun_mp",
		-544306709 => "weapon_fire",
		-1312131151 => "weapon_rpg",
		341774354 => "weapon_heli_crash",
		1834241177 => "weapon_railgun",
		1317494643 => "weapon_hammer",
		-1553120962 => "weapon_run_over_by_car",
		819155540 => "weapon_addon_357mag",
		-650859329 => "weapon_addon_berserker",
		910830060 => "weapon_exhaustion",
		-618237638 => "weapon_emplauncher",
		-807467678 => "weapon_addon_m9a3",
		539292904 => "weapon_explosion",
		-1833087301 => "weapon_electric_fence",
		1936677264 => "weapon_drowning_in_vehicle",
		1161062353 => "weapon_coyote",
		-38085395 => "weapon_digiscanner",
		-842959696 => "weapon_fall",
		148160082 => "weapon_cougar",
		615608432 => "weapon_molotov",
		-1569615261 => "weapon_unarmed",
		-494786007 => "vehicle_weapon_player_lazer",
		1785463520 => "weapon_marksmanrifle_mk2",
		-1658906650 => "weapon_militaryrifle",
		-2067956739 => "weapon_crowbar",
		-1323279794 => "vehicle_weapon_rotors",
		-2084633992 => "weapon_carbinerifle",
		1470379660 => "weapon_gadgetpistol",
		1052850250 => "weapon_addon_sentinelshotgun",
		-844344963 => "vehicle_weapon_searchlight",
		1566990507 => "vehicle_weapon_enemy_laser",
		-1121678507 => "weapon_minismg",
		-102973651 => "weapon_hatchet",
		-1625648674 => "vehicle_weapon_player_hunter",
		-821520672 => "vehicle_weapon_plane_rocket",
		859191078 => "weapon_addon_dutypistol",
		-123497569 => "vehicle_weapon_space_rocket",
		171789620 => "weapon_combatpdw",
		-1076751822 => "weapon_snspistol",
		-879347409 => "weapon_revolver_mk2",
		-494615257 => "weapon_assaultshotgun",
		324215364 => "weapon_microsmg",
		-1075685676 => "weapon_pistol_mk2",
		1737195953 => "weapon_nightstick",
		320620122 => "weapon_addon_m4",
		883325847 => "weapon_petrolcan",
		125959754 => "weapon_compactlauncher",
		-598887786 => "weapon_marksmanpistol",
		-1600701090 => "weapon_bzgas",
		-188319074 => "weapon_deer",
		2132975508 => "weapon_bullpuprifle",
		-102323637 => "weapon_bottle",
		-1768145561 => "weapon_specialcarbine_mk2",
		1943503626 => "weapon_addon_reaper",
		-1786099057 => "weapon_bat",
		1726445501 => "weapon_addon_rpk16",
		-1946516017 => "weapon_addon_huntingrifle",
		955837630 => "weapon_hen",
		-538741184 => "weapon_switchblade",
		-774507221 => "weapon_tacticalrifle",
		101631238 => "weapon_fireextinguisher",
		-1420407917 => "weapon_proxmine",
		2144741730 => "weapon_combatmg",
		126349499 => "weapon_snowball",
		-1168940174 => "weapon_hazardcan",
		-656458692 => "weapon_knuckle",
		-2009644972 => "weapon_snspistol_mk2",
		-496173278 => "weapon_addon_colt",
		487013001 => "weapon_pumpshotgun",
		856002082 => "weapon_remotesniper",
		1853742572 => "weapon_precisionrifle",
		-619010992 => "weapon_machinepistol",
		-1840517646 => "weapon_addon_ak74",
		-2115075845 => "weapon_addon_endurancepistol",
		435594297 => "weapon_addon_m870",
		-37975472 => "weapon_smokegrenade",
		1649403952 => "weapon_compactrifle",
		-853065399 => "weapon_battleaxe",
		94989220 => "weapon_combatshotgun",
		-352920269 => "weapon_addon_katana",
		736523883 => "weapon_smg",
		1834887169 => "weapon_bird_crap",
		-18093114 => "weapon_addon_g36c",
		-440934790 => "weapon_animal_retriever",
		2024373456 => "weapon_smg_mk2",
		-764006018 => "vehicle_weapon_radar",
		1198256469 => "weapon_raycarbine",
		-1063057011 => "weapon_specialcarbine",
		133987706 => "weapon_rammed_by_car",
		-2066285827 => "weapon_bullpuprifle_mk2",
		-1685291364 => "weapon_addon_stungun",
		-1355376991 => "weapon_raypistol",
		419712736 => "weapon_wrench"
	];

	const DamageTypes = [
		0 => "unknown (0)",
		1 => "no damage",
		2 => "melee",
		3 => "bullet",
		4 => "force ragdoll fall",
		5 => "explosive",
		6 => "fire",
		//7 => "",
		8 => "fall",
		//9 => "",
		10 => "electric",
		11 => "barbed wire",
		12 => "extinguisher",
		13 => "gas",
		14 => "water cannon",
	];

	public static function getDamageType($type)
	{
		if (isset(self::DamageTypes[$type])) {
			return self::DamageTypes[$type];
		}

		return "unknown ($type)";
	}

	public static function getDamageWeapon($hash)
	{
		$hash = $hash - 4294967296;

		if (isset(self::Weapons[$hash])) {
			return self::Weapons[$hash];
		}

		return "N/A ($hash)";
	}

	public static function getDamaged(string $license)
	{
		return self::query()
			->select(['license_identifier', 'timestamp', 'damage_type', 'weapon_type', 'distance', 'weapon_damage'])
			->whereRaw("JSON_CONTAINS(hit_players, '\"" . $license . "\"', '$')")
			->orderByDesc('timestamp')
			->limit(500)
			->get()->toArray();
	}
}
