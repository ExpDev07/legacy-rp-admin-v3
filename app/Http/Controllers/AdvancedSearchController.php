<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\OPFWHelper;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\CharacterIndexResource;
use App\Http\Resources\PlayerResource;
use App\Motel;
use App\PanelLog;
use App\Player;
use App\Property;
use App\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdvancedSearchController extends Controller
{
    const Config = [
        'characters' => [
            'character_id',
            'backstory',
            'bank',
            'cash',
            'date_of_birth',
            'department_name',
            'first_name',
            'gender',
            'jail',
            'job_name',
            'last_name',
            'phone_number',
            'position_name',
            'steam_identifier',
            'stocks_balance',
        ],
        'vehicles'   => [
            'vehicle_id',
            'garage_identifier',
            'garage_impound',
            'garage_state',
            'mileage',
            'model_name',
            'owner_cid',
            'plate',
        ],
        'users'      => [
            'user_id',
            'last_connection',
            'player_name',
            'playtime',
            'steam_identifier',
            'total_joins',
            'is_staff',
            'is_super_admin',
        ],
        'properties' => [
            'property_id',
            'property_name',
            'property_type',
            'property_address',
            'property_cost',
            'property_renter',
            'property_renter_cid',
            'property_income',
            'property_last_pay',
        ],
    ];

    /**
     * Allowed advance search types
     */
    const AllowedAdvancedTypes = [
        'exact'     => '=',
        'more'      => '>=',
        'less'      => '<=',
        'like'      => 'LIKE',
        'not_null'  => 'not_null',
        'null'      => 'null',
        'not_empty' => 'not_empty',
        'empty'     => 'empty',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $page = Paginator::resolveCurrentPage('page');

        $table = $request->get('table') ?? 'characters';
        $field = $request->get('field') ?? 'character_id';
        $type = $request->get('searchType') ?? 'exact';
        $value = trim($request->get('value')) ?? '';

        if (in_array($type, [
            'not_null',
            'null',
            'not_empty',
            'empty',
        ])) {
            $value = '*';
        }

        $results = [];
        $header = [];

        $error = '';
        if (!isset(self::AllowedAdvancedTypes[$type])) {
            $error = 'Invalid type';
        } else {
            $type = self::AllowedAdvancedTypes[$type];

            if (!isset(self::Config[$table]) || !in_array($field, self::Config[$table])) {
                $error = 'Invalid table of field';
            } else {
                if ($type === 'LIKE') {
                    $value = '%' . $value . '%';
                }
                if (($type === '>=' || $type === '<=') && !is_numeric($value)) {
                    $error = 'Value has to be numeric for more/less';
                } else if ($value) {
                    switch ($table) {
                        case 'characters':
                            $data = $this->searchCharacters($field, $type, $value, $page);
                            $results = $data['results'];
                            $header = $data['header'];
                            break;
                        case 'vehicles':
                            $data = $this->searchVehicles($field, $type, $value, $page);
                            $results = $data['results'];
                            $header = $data['header'];
                            break;
                        case 'users':
                            $data = $this->searchUsers($field, $type, $value, $page);
                            $results = $data['results'];
                            $header = $data['header'];
                            break;
                        case 'properties':
                            $data = $this->searchProperties($field, $type, $value, $page);
                            $results = $data['results'];
                            $header = $data['header'];
                            break;
                        default:
                            $error = 'Unknown table';
                    }
                }
            }
        }

        $end = round(microtime(true) * 1000);

        return Inertia::render('Advanced/Index', [
            'results'       => $results,
            'header'        => $header ?? [''],
            'filters'       => [
                'table'      => $table,
                'field'      => $field,
                'searchType' => $request->get('searchType') ?? 'exact',
                'value'      => $request->get('value') ?? '',
            ],
            'links'         => $this->getPageUrls($page),
            'page'          => $page,
            'config'        => self::Config,
            'time'          => $end - $start,
            'error'         => $error,
            'searchedTable' => $table,
        ]);
    }

    /**
     * Searches the vehicle table
     *
     * @param string $field
     * @param string $type
     * @param string $value
     * @param int $page
     * @return array
     */
    private function searchVehicles(string $field, string $type, string $value, int $page): array
    {
        $query = Vehicle::query()->orderBy('vehicle_id');
        self::where($query, $field, $type, $value);
        $query->where('vehicle_deleted', '=', '0');
        $query->select(self::Config['vehicles'])->limit(15)->offset(($page - 1) * 15);

        $data = $query->get()->toArray();

        $result = array_map(function ($entry) {
            $json = $entry;

            unset($json['vehicle_id']);
            unset($json['model_name']);
            unset($json['owner_cid']);

            return [
                [
                    'link' => self::characterLinkArray($entry['owner_cid']),
                ],
                $entry['model_name'] . ' (' . $entry['vehicle_id'] . ')',
                self::formatJSON($json),
            ];
        }, $data);

        return [
            'results' => $result,
            'header'  => [
                'character',
                'info',
                'more',
            ],
        ];
    }

    /**
     * Searches the stocks_company_properties table
     *
     * @param string $field
     * @param string $type
     * @param string $value
     * @param int $page
     * @return array
     */
    private function searchProperties(string $field, string $type, string $value, int $page): array
    {
        $query = Property::query()->orderBy('property_id');
        self::where($query, $field, $type, $value);
        $query->select(self::Config['properties'])->limit(15)->offset(($page - 1) * 15);

        $data = $query->get()->toArray();

        $result = array_map(function ($entry) {
            $json = $entry;

            unset($json['property_renter_cid']);
            unset($json['property_address']);

            return [
                [
                    'link' => self::characterLinkArray($entry['property_renter_cid']),
                ],
                $entry['property_address'],
                self::formatJSON($json),
            ];
        }, $data);

        return [
            'results' => $result,
            'header'  => [
                'character',
                'address',
                'more',
            ],
        ];
    }

    /**
     * Searches the users table
     *
     * @param string $field
     * @param string $type
     * @param string $value
     * @param int $page
     * @return array
     */
    private function searchUsers(string $field, string $type, string $value, int $page): array
    {
        $query = Player::query()->orderBy('user_id');
        self::where($query, $field, $type, $value);
        $query->select(self::Config['users'])->limit(15)->offset(($page - 1) * 15);

        $data = $query->get()->toArray();

        $result = array_map(function ($entry) {
            $json = $entry;

            unset($json['player_name']);
            unset($json['steam_identifier']);
            unset($json['last_connection']);

            return [
                [
                    'link' => [
                        'target' => '/players/' . $entry['steam_identifier'],
                        'label'  => $entry['player_name'],
                    ],
                ],
                [
                    'time' => intval($entry['last_connection']),
                ],
                self::formatJSON($json),
            ];
        }, $data);

        return [
            'results' => $result,
            'header'  => [
                'player',
                'last_connection',
                'more',
            ],
        ];
    }

    /**
     * Searches the character table
     *
     * @param string $field
     * @param string $type
     * @param string $value
     * @param int $page
     * @return array
     */
    private function searchCharacters(string $field, string $type, string $value, int $page): array
    {
        $query = Character::query()->orderBy('character_id');
        self::where($query, $field, $type, $value);
        $query->select(self::Config['characters'])->limit(15)->offset(($page - 1) * 15);

        $data = $query->get()->toArray();

        $players = [];

        $result = array_map(function ($entry) use ($players) {
            $json = $entry;

            unset($json['first_name']);
            unset($json['last_name']);
            unset($json['steam_identifier']);
            unset($json['character_id']);

            if (!isset($players[$entry['steam_identifier']])) {
                $player = Player::query()->where('steam_identifier', '=', $entry['steam_identifier'])->first(['player_name']);
                $players[$entry['steam_identifier']] = $player ? $player->player_name : $entry['steam_identifier'];
            }

            return [
                [
                    'link' => [
                        'target' => '/players/' . $entry['steam_identifier'],
                        'label'  => $players[$entry['steam_identifier']],
                    ],
                ],
                [
                    'link' => [
                        'target' => '/players/' . $entry['steam_identifier'] . '/characters/' . $entry['character_id'] . '/edit',
                        'label'  => $entry['first_name'] . ' ' . $entry['last_name'] . ' (' . $entry['character_id'] . ')',
                    ],
                ],
                self::formatJSON($json),
            ];
        }, $data);

        return [
            'results' => $result,
            'header'  => [
                'player',
                'character',
                'more',
            ],
        ];
    }

    private static function where(Builder &$query, string $field, string $type, string $value)
    {
        switch ($type) {
            case 'null':
                $query->whereNull($field);
                break;
            case 'not_null':
                $query->whereNotNull($field);
                break;
            case 'empty':
                $query->where($field, '=', '');
                break;
            case 'not_empty':
                $query->where($field, '!=', '');
                break;
            default:
                $query->where($field, $type, $value);
        }
    }

    /**
     * @param int $characterId
     * @return array
     */
    private static function characterLinkArray(int $characterId): array
    {
        $character = Character::find($characterId);

        return [
            'target' => $character
                ? '/players/' . $character['steam_identifier'] . '/characters/' . $character['character_id'] . '/edit'
                : '',
            'label'  => $character
                ? $character['first_name'] . ' ' . $character['last_name']
                : ($characterId ?? 'N/A'),
        ];
    }

    /**
     * Formats JSON array
     *
     * @param array $json
     * @return array
     */
    private static function formatJSON(array $json): array
    {
        foreach ($json as &$val) {
            if (is_numeric($val)) {
                if (Str::contains($val, '.')) {
                    $val = floatval($val);
                } else {
                    $val = intval($val);
                }
            } else if (is_string($val)) {
                if (strlen($val) > 50) {
                    $val = substr($val, 0, 47) . '...';
                }

                $val = htmlentities($val);
            }
        }

        return [
            'pre' => json_encode($json, JSON_PRETTY_PRINT),
        ];
    }

}
