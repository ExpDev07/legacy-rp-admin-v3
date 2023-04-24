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

	const HitComponents = [
		// Confirmed (very sure):
		0 => "crotch",
		1 => "upper left leg",
		2 => "lower left leg",
		3 => "left foot",
		4 => "upper right leg",
		5 => "lower right leg",
		6 => "right foot",
		8 => "stomach",
		9 => "upper chest",
		10 => "upper chest",
		11 => "left shoulder",
		12 => "upper left arm",
		13 => "lower left arm",
		14 => "left hand",
		15 => "right shoulder",
		16 => "upper right arm",
		17 => "lower right arm",
		18 => "right hand",
		19 => "neck",
		20 => "head",

		// Unknown/Unsure:
		7 => "unknown (7)",
		21 => "unknown (21)",
		22 => "unknown (22)"
	];

	const Weapons = [
		-61198893 => "molotov",
		-1946516017 => "weapon_addon_huntingrifle",
		883325847 => "weapon_petrolcan",
		-1833087301 => "weapon_electric_fence",
		-1474357123 => "gas_tank",
		-1253095144 => "vehicle_weapon_turret_boxville",
		435594297 => "weapon_addon_m870",
		-72657034 => "gadget_parachute",
		-656458692 => "weapon_knuckle",
		317205821 => "weapon_autoshotgun",
		940833800 => "weapon_stone_hatchet",
		729375873 => "vehicle_weapon_turret_limo",
		-624592211 => "vehicle_weapon_tampa_fixedminigun",
		-1045183535 => "weapon_revolver",
		-102323637 => "weapon_bottle",
		-1090665087 => "weapon_vehicle_rocket",
		2138347493 => "weapon_firework",
		1820910717 => "vehicle_weapon_rogue_missile",
		1752584910 => "weapon_stinger",
		-608341376 => "weapon_combatmg_mk2",
		-448894556 => "vehicle_weapon_mobileops_cannon",
		861723357 => "weapon_boar",
		-494615257 => "weapon_assaultshotgun",
		-146175596 => "vehicle_weapon_barrage_top_mg",
		-853065399 => "weapon_battleaxe",
		-275439685 => "weapon_dbshotgun",
		-1169823560 => "weapon_pipebomb",
		819155540 => "weapon_addon_357mag",
		984333226 => "weapon_heavyshotgun",
		-496173278 => "weapon_addon_colt",
		1762719600 => "boat",
		-651022627 => "vehicle_weapon_oppressor_mg",
		-1832600771 => "smokegrenade",
		-1171817471 => "vehicle_weapon_mogul_dualturret",
		727643628 => "weapon_ceramicpistol",
		-947031628 => "weapon_heavyrifle",
		-1638383454 => "vehicle_weapon_tampa_missile",
		-2083880407 => "vehicle_bullet",
		1195540791 => "programmablear",
		-340621788 => "weapon_addon_vfcombatpistol",
		-1508194956 => "vehicle_weapon_nightshark_mg",
		615608432 => "weapon_molotov",
		1226518132 => "vehicle_weapon_halftrack_quadmg",
		-1834847097 => "weapon_dagger",
		798856618 => "rocket",
		-96609051 => "weapon_killer_whale",
		-1955384325 => "weapon_bleeding",
		-619010992 => "weapon_machinepistol",
		171789620 => "weapon_combatpdw",
		-150975354 => "blimp",
		1693512364 => "tankshell",
		-1840517646 => "weapon_addon_ak74",
		286056380 => "grenadelauncher",
		328167896 => "vehicle_weapon_apc_cannon",
		1432025498 => "weapon_pumpshotgun_mk2",
		1834887169 => "weapon_bird_crap",
		2144741730 => "weapon_combatmg",
		2100324592 => "weapon_addon_glock",
		1907543711 => "ship_destroy",
		-666617255 => "vehicle_weapon_bombushka_cannon",
		741814745 => "weapon_stickybomb",
		-901318531 => "vehicle_weapon_dogfighter_missile",
		1853742572 => "weapon_precisionrifle",
		2017895192 => "weapon_sawnoffshotgun",
		-1628533868 => "dir_steam",
		205991906 => "weapon_heavysniper",
		153396725 => "vehicle_weapon_hunter_missile",
		1186503822 => "vehicle_weapon_player_buzzard",
		-787150897 => "vehicle_weapon_dune_mg",
		1217122433 => "vehicle_weapon_tula_mg",
		-1853920116 => "weapon_navyrevolver",
		-1660422300 => "weapon_mg",
		507170720 => "vehicle_weapon_khanjali_cannon",
		1470379660 => "weapon_gadgetpistol",
		1119518887 => "vehicle_weapon_hunter_mg",
		1205296881 => "weapon_pig",
		1331922171 => "vehicle_weapon_halftrack_dualmg",
		2092838988 => "vehicle_weapon_akula_missile",
		-486730914 => "vehicle_weapon_mogul_turret",
		-266763809 => "weapon_addon_stidvc",
		376489128 => "vehicle_weapon_tula_minigun",
		324215364 => "weapon_microsmg",
		-1572351938 => "vehicle_weapon_cherno_missile",
		-991944340 => "vehicle_weapon_microlight_mg",
		-1553120962 => "weapon_run_over_by_car",
		-495648874 => "weapon_cat",
		341774354 => "weapon_heli_crash",
		-358074893 => "vehicle_weapon_comet_mg",
		-1813897027 => "weapon_grenade",
		1672152130 => "weapon_hominglauncher",
		2132975508 => "weapon_bullpuprifle",
		-1190244519 => "extinguisher",
		-1330848211 => "flare",
		-1706910483 => "weapon_addon_hk416b",
		1649403952 => "weapon_compactrifle",
		-1433899528 => "vehicle_weapon_insurgent_minigun",
		394659298 => "vehicle_weapon_khanjali_gl",
		-270015777 => "weapon_assaultsmg",
		1595421922 => "vehicle_weapon_dogfighter_mg",
		-1716189206 => "weapon_knife",
		964555122 => "weapon_addon_gardonepistol",
		84788907 => "vehicle_weapon_ruiner_rocket",
		1950855132 => "weapon_addon_pp19",
		-842959696 => "weapon_fall",
		1744687076 => "vehicle_weapon_tampa_dualminigun",
		-411549476 => "bike",
		-2084633992 => "weapon_carbinerifle",
		-924350237 => "weapon_addon_mp9",
		-1121678507 => "weapon_minismg",
		177293209 => "weapon_heavysniper_mk2",
		-1625648674 => "vehicle_weapon_player_hunter",
		-1328456693 => "vehicle_weapon_tula_dualmg",
		-1951375401 => "weapon_flashlight",
		148160082 => "weapon_cougar",
		-416629822 => "vehicle_weapon_rogue_cannon",
		-1117887894 => "vehicle_weapon_revolter_mg",
		-1246512723 => "vehicle_weapon_akula_turret_single",
		-818313621 => "dir_flame_explode",
		743550225 => "weapon_tiger_shark",
		1119849093 => "weapon_minigun",
		855547631 => "vehicle_weapon_havok_minigun",
		-1786099057 => "weapon_bat",
		-844344963 => "vehicle_weapon_searchlight",
		-188319074 => "weapon_deer",
		-1716589765 => "weapon_pistol50",
		-100946242 => "weapon_animal",
		-624163738 => "weapon_addon_hk433",
		324506233 => "weapon_airstrike_rocket",
		-1258723020 => "vehicle_weapon_deluxo_missile",
		-166158518 => "vehicle_weapon_mogul_nose",
		-1768145561 => "weapon_specialcarbine_mk2",
		-1076751822 => "weapon_snspistol",
		-581044007 => "weapon_machete",
		-879347409 => "weapon_revolver_mk2",
		190244068 => "vehicle_weapon_apc_mg",
		-764006018 => "vehicle_weapon_radar",
		1198256469 => "weapon_raycarbine",
		-771403250 => "weapon_heavypistol",
		-10959621 => "weapon_drowning",
		1945616459 => "vehicle_weapon_tank",
		-618237638 => "weapon_emplauncher",
		-335937730 => "vehicle_weapon_cannon_blazer",
		1171102963 => "weapon_stungun_mp",
		1726445501 => "weapon_addon_rpk16",
		1566990507 => "vehicle_weapon_enemy_laser",
		1305664598 => "weapon_grenadelauncher_smoke",
		1150790720 => "vehicle_weapon_volatol_dualmg",
		748372090 => "weapon_addon_p320b",
		1638077257 => "vehicle_weapon_player_savage",
		1100844565 => "vehicle_weapon_tula_nosemg",
		-441697337 => "weapon_addon_vandal",
		600439132 => "weapon_ball",
		527211813 => "dir_flame",
		-1737915262 => "gas_canister",
		28811031 => "weapon_briefcase_02",
		1347266149 => "vehicle_weapon_vigilante_missile",
		961495388 => "weapon_assaultrifle_mk2",
		1741783703 => "vehicle_weapon_water_cannon",
		1161062353 => "weapon_coyote",
		-352920269 => "weapon_addon_katana",
		-650859329 => "weapon_addon_berserker",
		1737195953 => "weapon_nightstick",
		1627465347 => "weapon_gusenberg",
		158495693 => "vehicle_weapon_rogue_mg",
		1097917585 => "vehicle_weapon_nose_turret_valkyrie",
		1151689097 => "vehicle_weapon_apc_missile",
		525623141 => "vehicle_weapon_barrage_rear_minigun",
		539292904 => "weapon_explosion",
		-1001503935 => "vehicle_weapon_ardent_mg",
		584646201 => "weapon_appistol",
		-86904375 => "weapon_carbinerifle_mk2",
		-1263987253 => "weapon_hammerhead_shark",
		955837630 => "weapon_hen",
		-1594068723 => "vehicle_weapon_dune_grenadelauncher",
		-410795078 => "vehicle_weapon_subcar_torpedo",
		-1673197750 => "firework",
		-868994466 => "weapon_hit_by_water_cannon",
		881270991 => "plane_rocket",
		1155224728 => "vehicle_weapon_turret_insurgent",
		785467445 => "vehicle_weapon_hunter_barrage",
		-1853879617 => "propane",
		2144528907 => "vehicle_weapon_turret_technical",
		-1420407917 => "weapon_proxmine",
		335057065 => "barrel",
		-2138288820 => "vehicle_weapon_trailer_dualaa",
		1015970717 => "train",
		1785463520 => "weapon_marksmanrifle_mk2",
		-611760632 => "vehicle_weapon_technical_minigun",
		-515713583 => "bzgas",
		-1014218325 => "smokegrenadelauncher",
		1593441988 => "weapon_combatpistol",
		-1696146015 => "bullet",
		1115750597 => "truck",
		-598887786 => "weapon_marksmanpistol",
		352593635 => "dir_water_hydrant",
		-123497569 => "vehicle_weapon_space_rocket",
		1223143800 => "weapon_barbed_wire",
		1768518260 => "car",
		431576697 => "vehicle_weapon_akula_minigun",
		-1746263880 => "weapon_doubleaction",
		50118905 => "vehicle_weapon_ruiner_bullet",
		1436779599 => "hi_octane",
		1177935125 => "vehicle_weapon_thruster_missile",
		-937058049 => "stickybomb",
		-1236251694 => "grenade",
		1114654932 => "petrol_pump",
		1697521053 => "vehicle_weapon_thruster_mg",
		-810431678 => "weapon_addon_rc4",
		1176362416 => "vehicle_weapon_subcar_mg",
		453432689 => "weapon_pistol",
		-348002226 => "vehicle_weapon_savestra_mg",
		-2066285827 => "weapon_bullpuprifle_mk2",
		711953949 => "vehicle_weapon_khanjali_mg",
		406929569 => "weapon_fertilizercan",
		419712736 => "weapon_wrench",
		375527679 => "weapon_passenger_rocket",
		-1538514291 => "vehicle_weapon_barrage_rear_gl",
		-200835353 => "vehicle_weapon_vigilante_mg",
		-1355376991 => "weapon_raypistol",
		736523883 => "weapon_smg",
		-2115075845 => "weapon_addon_endurancepistol",
		-1063057011 => "weapon_specialcarbine",
		731779237 => "weapon_addon_mk18",
		100416529 => "weapon_sniperrifle",
		-807467678 => "weapon_addon_m9a3",
		856002082 => "weapon_remotesniper",
		-1168940174 => "weapon_hazardcan",
		-18093114 => "weapon_addon_g36c",
		-1685291364 => "weapon_addon_stungun",
		845770333 => "dir_gas_canister",
		704686874 => "vehicle_weapon_hunter_cannon",
		-37975472 => "weapon_smokegrenade",
		1141786504 => "weapon_golfclub",
		133987706 => "weapon_rammed_by_car",
		487013001 => "weapon_pumpshotgun",
		-2009644972 => "weapon_snspistol_mk2",
		126349499 => "weapon_snowball",
		-1654528753 => "weapon_bullpupshotgun",
		-774507221 => "weapon_tacticalrifle",
		-1569615261 => "weapon_unarmed",
		-538741184 => "weapon_switchblade",
		-2067956739 => "weapon_crowbar",
		911657153 => "weapon_stungun",
		1198879012 => "weapon_flaregun",
		-1600701090 => "weapon_bzgas",
		94989220 => "weapon_combatshotgun",
		125959754 => "weapon_compactlauncher",
		320620122 => "weapon_addon_m4",
		2024373456 => "weapon_smg_mk2",
		1259576109 => "vehicle_weapon_player_bullet",
		1371067624 => "vehicle_weapon_seabreeze_mg",
		-268631733 => "vehicle_weapon_player_laser",
		-38085395 => "weapon_digiscanner",
		859191078 => "weapon_addon_dutypistol",
		-821520672 => "vehicle_weapon_plane_rocket",
		-102973651 => "weapon_hatchet",
		1943503626 => "weapon_addon_reaper",
		-1658906650 => "weapon_militaryrifle",
		-494786007 => "vehicle_weapon_player_lazer",
		-437014993 => "vehicle_weapon_mogul_dualnose",
		94548753 => "weapon_cow",
		1936677264 => "weapon_drowning_in_vehicle",
		-440934790 => "weapon_animal_retriever",
		910830060 => "weapon_exhaustion",
		1317494643 => "weapon_hammer",
		1834241177 => "weapon_railgun",
		-1738072005 => "vehicle_weapon_avenger_cannon",
		-1312131151 => "weapon_rpg",
		341154295 => "vehicle_weapon_trailer_missile",
		-1694538890 => "vehicle_weapon_deluxo_mg",
		-544306709 => "weapon_fire",
		-1538179531 => "vehicle_weapon_turret_valkyrie",
		-1923845809 => "weapon_addon_dp9",
		-1148198339 => "weapon_small_dog",
		-1466123874 => "weapon_musket",
		992551041 => "weapon_addon_tacknife",
		-2012408590 => "vehicle_weapon_akula_barrage",
		476907586 => "vehicle_weapon_akula_turret_dual",
		-1238556825 => "weapon_rayminigun",
		1000258817 => "vehicle_weapon_barrage_top_minigun",
		-2000187721 => "weapon_briefcase",
		1015268368 => "vehicle_weapon_tampa_mortar",
		-1501041657 => "weapon_rabbit",
		-729187314 => "vehicle_weapon_subcar_missile",
		-952879014 => "weapon_marksmanrifle",
		101631238 => "weapon_fireextinguisher",
		428159217 => "plane",
		-1568386805 => "weapon_grenadelauncher",
		137902532 => "weapon_vintagepistol",
		-1027401503 => "weapon_addon_sentinelbbshotgun",
		-1075685676 => "weapon_pistol_mk2",
		-730904777 => "tanker",
		1200179045 => "vehicle_weapon_barrage_rear_mg",
		-1357824103 => "weapon_advancedrifle",
		-1323279794 => "vehicle_weapon_rotors",
		-1810795771 => "weapon_poolcue",
		-2019545594 => "vehicle_weapon_viseris_mg",
		1052850250 => "weapon_addon_sentinelshotgun",
		1416047217 => "vehicle_weapon_dune_minigun",
		-2088013459 => "vehicle_weapon_khanjali_cannon_heavy",
		1233104067 => "weapon_flare",
		1192341548 => "vehicle_weapon_trailer_quadmg",
		-1950890434 => "vehicle_weapon_oppressor_missile",
		741027160 => "vehicle_weapon_bombushka_dualmg",
		-1074790547 => "weapon_assaultrifle"
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

	public static function getHitComponent($component)
	{
		$component = intval($component);

		if (isset(self::HitComponents[$component])) {
			return self::HitComponents[$component];
		}

		return "undiscovered ($component)";
	}

	public static function getDamageType($type)
	{
		if (isset(self::DamageTypes[$type])) {
			return self::DamageTypes[$type];
		}

		return "unknown ($type)";
	}

	public static function getDamageWeapon($hash)
	{
		if (isset(self::Weapons[$hash])) {
			return self::Weapons[$hash];
		}

		$signed = $hash - 4294967296;

		if (isset(self::Weapons[$signed])) {
			return self::Weapons[$signed];
		}

		return "$hash/$signed";
	}

	public static function getDamaged(string $license)
	{
		return self::query()
			->select(['license_identifier', 'timestamp', 'hit_component', 'damage_type', 'weapon_type', 'distance', 'weapon_damage'])
			->whereRaw("JSON_CONTAINS(hit_players, '\"" . $license . "\"', '$')")
			->orderByDesc('timestamp')
			->limit(500)
			->get()->toArray();
	}
}
