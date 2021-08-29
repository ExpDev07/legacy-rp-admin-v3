<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white !mb-2">
                <span id="map_title">{{ t('map.title') }}</span>
                <select class="inline-block w-90 ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                        id="server">
                    <option v-for="server in servers" :key="server.name" :value="server.name">{{ server.name }}</option>
                </select>
            </h1>
            <p>
                {{ data }}
            </p>
        </portal>

        <portal to="actions">
            <div class="mb-2">
                <!-- Stop tracking -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="stopTracking()" v-if="trackedPlayer">
                    <i class="fas fa-stop mr-1"></i>
                    {{ t('map.stop_track') }}
                </button>
                <!-- Play/Pause -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isPaused = true" v-if="!isPaused">
                    <i class="fas fa-pause"></i>
                    {{ t('map.pause') }}
                </button>
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isPaused = false" v-if="isPaused">
                    <i class="fas fa-play"></i>
                    {{ t('map.play') }}
                </button>
            </div>
        </portal>

        <!-- Area Add -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-1k" v-if="isAddingDetectionArea">
            <div class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded w-alert">
                <h3 class="mb-2">
                        {{ t('map.area_title') }}
                </h3>

                <!-- Radius -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold" for="area_radius">
                        {{ t('map.area_radius') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" min="1" max="400" id="area_radius" value="5" v-model="form.area_radius" />
                </div>

                <!-- Type -->
                <div class="w-full p-3 flex justify-between px-0">
                    <label class="mr-4 block w-1/4 text-center pt-2 font-bold" for="area_type">
                        {{ t('map.area_type.title') }}
                    </label>
                    <select class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="area_type" v-model="form.area_type">
                        <option value="normal">{{ t('map.area_type.normal') }}</option>
                        <option value="persistent">{{ t('map.area_type.persistent') }}</option>
                    </select>
                </div>

                <p class="my-2">
                    <span class="font-bold">{{ t('map.area_type.normal') }}</span>: {{ t('map.area_type.normal_description') }}<br>
                    <span class="font-bold">{{ t('map.area_type.persistent') }}</span>: {{ t('map.area_type.persistent_description') }}
                </p>

                <!-- Buttons -->
                <div class="flex items-center mt-2">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded mr-2"
                            @click="confirmArea">
                        <i class="mr-1 fas fa-plus"></i>
                        {{ t('map.area_add') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            @click="isAddingDetectionArea = false">
                        {{ t('global.cancel') }}
                    </button>
                </div>
            </div>
        </div>

        <template>
            <div class="-mt-12" id="map-wrapper">
                <div class="flex flex-wrap justify-between mb-2 w-map max-w-full">
                    <div class="flex flex-wrap">
                        <input type="text"
                               class="form-control w-56 rounded border block mobile:w-full px-4 py-2 bg-gray-200 dark:bg-gray-600"
                               :placeholder="t('map.track_placeholder')" v-model="tracking.id"/>
                        <select
                            class="block w-44 ml-2 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded mobile:w-full mobile:m-0 mobile:mt-1"
                            v-model="tracking.type">
                            <option value="server_">{{ t('map.track_server') }}</option>
                            <option value="">{{ t('map.track_steam') }}</option>
                            <option value="player_">{{ t('map.track_character') }}</option>
                        </select>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="trackId(tracking.type + tracking.id)">
                            {{ t('map.do_track') }}
                        </button>
                    </div>
                    <div class="flex flex-wrap">
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="addArea">
                            {{ t('map.area_add') }}
                        </button>
                        <button
                            class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1"
                            @click="advancedTracking = !advancedTracking"
                            :title="advancedTracking ? t('global.enabled') : t('global.disabled')"
                        >
                            {{ t('map.advanced_track') }}
                            <i class="fas fa-check ml-1" v-if="advancedTracking"></i>
                            <i class="fas fa-times ml-1" v-else></i>
                        </button>
                    </div>
                </div>
                <div class="relative w-map max-w-full">
                    <div id="map" class="w-map max-w-full relative h-max"></div>
                    <pre class="bg-opacity-70 bg-white coordinate-attr absolute bottom-0 left-0 cursor-pointer z-1k"
                         v-if="clickedCoords"><span @click="copyText($event, clickedCoords)">{{ clickedCoords }}</span> / <span
                        @click="copyText($event, coordsCommand)">{{ t('map.command') }}</span></pre>
                    <pre
                        class="w-map-gauge bg-opacity-70 bg-white absolute bottom-attr2 right-0 z-1k p-2 text-gray-800 text-xs"
                        v-if="advancedTracking && trackedPlayer"
                    >{{ tracking.data.advanced }}</pre>
                    <div
                        class="w-map-gauge bg-opacity-70 bg-white absolute bottom-attr right-0 z-1k px-2 pt-2 pb-1 flex"
                        :class="{'hidden' : !advancedTracking || !trackedPlayer}"
                    >
                        <div class="relative w-map-other-gauge">
                            <img src="/images/height-indicator.png" style="height: 90px" alt="Height indicator" />
                            <div
                                class="font-bold absolute border-b-2 border-gray-700 left-8 text-gray-700 w-map-height-ind text-right text-xxs leading-3"
                                :style="'bottom: ' + tracking.data.alt + '%;'"
                            >
                                {{ tracking.data.altitude }}
                            </div>
                        </div>
                        <vue-speedometer
                            class="inline-block"
                            :value="tracking.data.speed"
                            labelFontSize="12px"
                            :ringWidth="20"
                            :height="90"
                            :width="120"
                            startColor="#90EF90"
                            endColor="#fa1e43"
                            :minValue="0"
                            :maxValue="360"
                            :segments="4"
                            currentValueText="${value}mph"
                            valueTextFontSize="14px"
                            :needleHeightRatio="0.7"
                        />
                    </div>
                </div>
                <div class="my-2 flex flex-wrap -mx-2 justify-between text-xs w-map max-w-full">
                    <div class="mx-2">
                        <img src="/images/icons/circle.png" class="w-map-icon inline-block" alt="on foot" />
                        <span class="leading-map-icon">Someone is on foot</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/circle_green.png" class="w-map-icon inline-block" alt="invisible" />
                        <span class="leading-map-icon">Someone is invisible</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/circle_red.png" class="w-map-icon inline-block" alt="passenger" />
                        <span class="leading-map-icon">Someone is a passenger</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/skull.png" class="w-map-icon inline-block" alt="dead" />
                        <span class="leading-map-icon">Someone is dead</span>
                    </div>
                    <div class="mx-2">
                        <img src="/images/icons/skull_red.png" class="w-map-icon inline-block" alt="dead passenger" />
                        <span class="leading-map-icon">Someone is dead and a passenger</span>
                    </div>
                </div>
                <div class="flex flex-wrap" v-if="detectionAreas.length > 0">
                    <div class="pt-4 mr-4" v-for="(area, index) in detectionAreas" :key="index">
                        <h3 class="mb-2">
                            {{ t('map.area_label', index + 1) }}
                            <sup>
                                <a href="#" class="text-red-500 font-bold" @click="removeArea($event, index)" :title="t('global.remove')">&#x1F5D9;</a>
                            </sup>
                        </h3>
                        <table class="text-xs font-mono">
                            <tr v-for="(player, x) in area.people" :key="x">
                                <td class="pr-2">
                                    <a class="text-yellow-500" target="_blank" :href="'/players/' + player.steam">{{ player.name }} ({{ player.source }})</a>
                                </td>
                                <td class="pr-2" v-if="player.exited_at" :title="t('map.area_time', humanizeMilliseconds(player.exited_at - player.entered_at))">
                                    {{ t('map.area_exit', $moment(player.exited_at).fromNow()) }}
                                </td>
                                <td class="pr-2" v-else>
                                    {{ t('map.area_inside') }}
                                </td>
                                <td>
                                    <a class="track-cid text-yellow-600" href="#" :data-trackid="'player_' + player.cid" data-popup="true">[{{ t('map.do_track') }}]</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="flex flex-wrap">
                    <div v-if="afkPeople" class="pt-4 mr-4">
                        <h3 class="mb-2">{{ t('map.afk_title') }}</h3>
                        <pre v-html="afkPeople" class="text-sm">{{ afkPeople }}</pre>
                    </div>
                    <div v-if="invisiblePeople" class="pt-4">
                        <h3 class="mb-2">{{ t('map.invisible_title') }}</h3>
                        <pre v-html="invisiblePeople" class="text-sm">{{ invisiblePeople }}</pre>
                    </div>
                </div>
            </div>
        </template>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import L from "leaflet";
import {GestureHandling} from "leaflet-gesture-handling";
import "leaflet-rotatedmarker";
import 'leaflet-fullscreen';
import custom_icons from "../../data/vehicles.json";
import ignore_invisible from "../../data/ignore_invisible.json";
import VueSpeedometer from "vue-speedometer";

const Rainbow = require('rainbowvis.js');

(function (global) {
    let MarkerMixin = {
        _updateZIndex: function (offset) {
            this._icon.style.zIndex = this.options.forceZIndex ? (this.options.forceZIndex + (this.options.zIndexOffset || 0)) : (this._zIndex + offset);
        },
        setForceZIndex: function (forceZIndex) {
            this.options.forceZIndex = forceZIndex ? forceZIndex : null;
        }
    };
    if (global) global.include(MarkerMixin);
})(L.Marker);

// Some functions for debugging
let playerCallback = null,
    playerCallbackCid = null,
    VueInstance = null;
window.debug = function (cid) {
    playerCallbackCid = cid;
    playerCallback = function (player, coords, _this) {
        console.info('Player debug', player);
    };
};
window.track = function (cid) {
    window.location.hash = 'player_' + cid;
    window.location.reload();
};
window.marker = function (cid) {
    playerCallbackCid = cid;
    playerCallback = function (player, coords, _this) {
        console.info('Marker');
        const markerCode = `{lat: ` + coords.lat + `, lng: ` + coords.lng + `}`;
        _this.copyText(null, markerCode);

        console.info(`{lat: ` + coords.lat + `, lng: ` + coords.lng + `}`, '(Copied to clipboard)');
    };
};
window.convertCoords = function (coords) {
    if (VueInstance) {
        const converted = VueInstance.convertCoords(coords);

        if ('x' in converted) {
            console.info(`{x: ` + converted.x.toFixed(3) + `, y: ` + converted.y.toFixed(3) + `}`);
        } else {
            console.info(`{lat: ` + converted.lat + `, lng: ` + converted.lng + `}`);
        }
    } else {
        console.error('VueInstance not set!');
    }
};

window.loadHistory = function (server, player, day) {
    if (VueInstance) {
        $.post(VueInstance.hostname() + '/map/go/history', {
            server: server,
            player: player,
            day: day,
            token: VueInstance.token
        }, console.log);
    }
};

let InvisibleHistoryDebug = {};
window.getInvisibleHistory = function() {
    return Object.keys(InvisibleHistoryDebug);
}

export default {
    layout: Layout,
    components: {
        VSection,
        VueSpeedometer,
    },
    props: {
        servers: {
            type: Array,
            required: true
        },
        staff: {
            type: Array,
            required: true
        },
        blips: {
            type: Array,
            required: true
        },
        token: {
            type: String,
            required: true
        },
    },
    data() {
        return {
            map: null,
            markers: {},
            data: this.t('map.loading'),
            connection: null,
            isPaused: false,
            trackedPlayer: window.location.hash.substr(1),
            firstRefresh: true,
            clickedCoords: '',
            rawClickedCoords: null,
            coordsCommand: '',
            afkPeople: '',
            invisiblePeople: '',
            openPopup: null,
            isDragging: false,
            isAddingDetectionArea: false,
            form: {
                area_radius: 0,
                area_type: 'normal',
                area_location: {
                    x: 0,
                    y: 0
                }
            },
            layers: {
                "Players": L.layerGroup(),
                "Dead Players": L.layerGroup(),
                "Emergency Vehicles": L.layerGroup(),
                "Vehicles": L.layerGroup(),
                "Blips": L.layerGroup(),
            },
            detectionAreas: [],
            tracking: {
                id: '',
                type: 'server_',
                data: {
                    speed: 0,
                    alt: 0,
                    altitude: '0m',
                    advanced: 'Loading...'
                }
            },
            advancedTracking: false,
            cayoCalibrationMode: false // Set this to true to recalibrate the cayo perico map
        };
    },
    methods: {
        getBounds() {
            return {
                game: {
                    bounds: {
                        min: {x: -2861.987, y: 7664.36},
                        max: {x: 4179.125, y: -3579.824}
                    }
                },
                map: {
                    bounds: {
                        min: {x: 81.6328125, y: -9.986328125},
                        max: {x: 178.2734375, y: -164.267578125}
                    }
                },
                cayo: {
                    minMap: {x: 149.03777506175518, y: -166.5205908658534}, // top left corner of transition point
                    game: {
                        bounds: {
                            min: {x: 3925.583, y: -4688.479},
                            max: {x: 5478.356, y: -5849.987}
                        }
                    },
                    map: {
                        bounds: {
                            min: {x: 195.0625, y: -210.908203125},
                            max: {x: 242.9453125, y: -246.48828125}
                        }
                    }
                },
                version: "cayo_v2"
            };
        },
        mapNumber(val, in_min, in_max, out_min, out_max) {
            return (val - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
        },
        copyText(e, text) {
            if (e !== null) {
                e.preventDefault();
            }

            this.copyToClipboard(text);
        },
        coords(coords) {
            return {
                lat: coords.y,
                lng: coords.x
            }
        },
        stopTracking() {
            this.trackedPlayer = null;
            window.location.hash = '';
        },
        trackId(id) {
            this.trackedPlayer = id;
            window.location.hash = id;
            this.firstRefresh = true;
        },
        confirmArea() {
            if (this.form.area_radius < 1 || this.form.area_radius > 500) {
                return alert(this.t('map.area_inv_radius'));
            }

            const convertDistance = d => {
                const a = this.convertCoords({x: 0, y: 0}),
                    b = this.convertCoords({x: d, y: d});

                return Math.abs(b.lat - a.lat);
            };

            this.isAddingDetectionArea = false;
            const area = {
                    x: parseInt(this.form.area_location.x),
                    y: parseInt(this.form.area_location.y),
                    radius: parseInt(this.form.area_radius),
                    type: this.form.area_type+'',
                    people: [],

                    _timestamp: Date.now()
                },
                coords = this.convertCoords(this.form.area_location);

            area.marker = L.marker(coords,
                {
                    icon: new L.Icon(
                        {
                            iconUrl: '/images/icons/area.png',
                            iconSize: [16, 16]
                        }
                    ),
                    forceZIndex: 300
                }
            );
            area.marker.bindPopup(this.t('map.area_label', this.detectionAreas.length + 1) +
                '<br><span class="italic">' + area.x + ' ' + area.y + ' (' + area.radius + 'm)</span>' +
                '<br>' + this.t('map.area_type.title') + ': <span class="italic">' + this.t('map.area_type.' + area.type) + '</span>', {
                autoPan: false
            });
            area.marker.addTo(this.map);

            area.circle = L.circle(coords, {
                radius: convertDistance(area.radius),
                color: '#FFBF00',
                fillColor: '#FFBF00',
                weight: 2,
                opacity: 0.85,
                fill: true
            });
            area.circle.addTo(this.map);

            this.detectionAreas.push(area);

            this.form.area_location = null;
            this.form.area_type = 'normal';
            this.form.area_radius = 5;
        },
        addArea() {
            if (!this.rawClickedCoords) {
                return alert(this.t('map.area_no_location'));
            }

            this.form.area_location = this.rawClickedCoords;
            this.form.area_radius = 5;

            this.isAddingDetectionArea = true;
        },
        removeArea(e, index) {
            e.preventDefault();

            this.map.removeLayer(this.detectionAreas[index].marker);
            this.map.removeLayer(this.detectionAreas[index].circle);

            let areas = [];
            for (let x=0;x<this.detectionAreas.length;x++) {
                if (x !== index) {
                    areas.push(this.detectionAreas[x]);
                }
            }

            this.detectionAreas = areas;
        },
        humanizeMilliseconds(ms) {
            const sec = Math.round(ms / 1000);

            return this.$options.filters.humanizeSeconds(sec) + ' (' + sec + 's)';
        },
        updateDetectionAreas(coords, player) {
            if (!player.character) {
                return;
            }

            const _this = this;
            $.each(this.detectionAreas, function (index, area) {
                const dist = Math.sqrt((coords.x - area.x) ** 2 + (coords.y - area.y) ** 2);

                if (area.people.length >= 800) {
                    return;
                }

                if (dist > area.radius) {
                    area.people = area.people.filter((p, x) => {
                        if (p.exited_at) {
                            return true;
                        } else if (p.steam === player.steamIdentifier) {
                            if (area.type === 'persistent') {
                                area.people[x].exited_at = Date.now();
                                return true;
                            }
                            return false;
                        }
                        return true;
                    });
                } else {
                    const addToList = area.people.filter(p => {
                        return p.steam === player.steamIdentifier && !p.exited_at;
                    }).length === 0;

                    if (addToList) {
                        area.people.push({
                            steam: player.steamIdentifier,
                            cid: player.character.id,
                            source: player.source,
                            name: player.character.fullName,
                            entered_at: Date.now(),
                            exited_at: null
                        });
                    }
                }

                _this.detectionAreas[index].people = area.people;
                _this.detectionAreas[index]._timestamp = Date.now();
            });
        },
        hostname(isSocket) {
            const isDev = window.location.hostname === 'localhost';

            if (isSocket) {
                return isDev ? 'ws://' + window.location.hostname + ':8080' : 'wss://' + window.location.hostname + ':8443';
            } else {
                return isDev ? 'http://' + window.location.hostname + ':8080' : 'https://' + window.location.hostname + ':8443';
            }
        },
        getOTToken() {
            const _this = this;

            return new Promise(function(resolve, reject) {
                $.get(_this.hostname(false) + '/map/go/token?token=' + _this.token, function(data) {
                    if (data.status) {
                        resolve(data.token);
                    } else {
                        reject(data.error);
                    }
                }).fail(reject);
            });
        },
        shouldIgnoreInvisible(coords, steamIdentifier) {
            const parseSpot = spot => {
                const parts = spot.coords.split(' ');

                return {
                    "x": parseInt(parts[0]),
                    "y": parseInt(parts[1]),
                    "z": parseInt(parts[2]),
                    "radius": spot.radius,
                    "height": spot.height,
                };
            };
            const isInside = (spot, coords) => {
                return (
                    spot.z - spot.height < coords.z &&
                    spot.z + spot.height > coords.z
                ) && (
                    Math.pow(coords.x - spot.x, 2) + Math.pow(coords.y - spot.y, 2) < Math.pow(spot.radius, 2)
                );
            }

            // Check if staff member
            if (this.staff.includes(steamIdentifier)) {
                return true;
            }

            // Check if they are inside an apartment (most of the time that's about -99 below the ground)
            if (coords.z < -90 && coords.z > -140) {
                return true;
            }

            // Check if they are inside one of the ignore cylinders
            for (let x = 0; x < ignore_invisible.length; x++) {
                const spot = parseSpot(ignore_invisible[x]);

                if (isInside(spot, coords)) {
                    return true;
                }
            }

            const key = coords.x + ' ' + coords.y + ' ' + coords.z;
            if (!(key in InvisibleHistoryDebug)) {
                InvisibleHistoryDebug[key] = steamIdentifier;
            }

            // Hmm why are they invisible?
            return false;
        },
        async doMapRefresh(server) {
            const _this = this;

            if (this.connection) {
                this.connection.close();
            }

            try {
                const token = await this.getOTToken();

                this.connection = new WebSocket(this.hostname(true) + "/map/go/socket?ott=" + token + "&server=" + encodeURIComponent(server));

                this.connection.onmessage = function (event) {
                    try {
                        const data = JSON.parse(event.data);

                        _this.renderMapData(data);

                        _this.firstRefresh = false;
                    } catch (e) {
                        console.error('Failed to parse socket message ', e)
                    }
                }

                this.connection.onclose = function () {
                    _this.data = _this.t('map.closed', $('#server option:selected').text());
                };
            } catch (e) {
                this.data = this.t('map.closed', $('#server option:selected').text());

                console.error('Failed to connect to socket', e);
            }
        },
        getVehicleType(vehicle) {
            if (!vehicle) {
                return null;
            }

            let ret = {
                type: 'car',
                size: 23
            };

            $.each(custom_icons, function (type, cfg) {
                if (cfg.models.includes(vehicle.model + "")) {
                    ret.type = type;
                    ret.size = cfg.size;
                }
            });

            return ret;
        },
        getIcon(player, isDriving, isPassenger, isInvisible, isDead) {
            let size = {
                circle: 17,
                skull: 17,
                skull_red: 12,
                circle_red: 12,
                circle_green: 13
            };

            let icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle.png',
                    iconSize: [size.circle, size.circle]
                }
            );

            if (isInvisible) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/circle_green.png',
                        iconSize: [size.circle_green, size.circle_green]
                    }
                );
            } else if (isDriving) {
                const info = this.getVehicleType(player.vehicle);

                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/' + info.type + '.png',
                        iconSize: [info.size, info.size]
                    }
                );
            } else if (isPassenger && isDead) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/skull_red.png',
                        iconSize: [size.skull_red, size.skull_red]
                    }
                )
            } else if (isPassenger) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/circle_red.png',
                        iconSize: [size.circle_red, size.circle_red]
                    }
                )
            } else if (isDead) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/skull.png',
                        iconSize: [size.skull, size.skull]
                    }
                );
            }

            return icon;
        },
        formatSeconds(seconds) {
            return this.$moment.utc(seconds * 1000).format('HH:mm:ss');
        },
        convertCoords(coords) {
            const conf = this.getBounds();

            if ('x' in coords) {
                const x = this.mapNumber(coords.x, conf.game.bounds.min.x, conf.game.bounds.max.x, conf.map.bounds.min.x, conf.map.bounds.max.x);
                const y = this.mapNumber(coords.y, conf.game.bounds.min.y, conf.game.bounds.max.y, conf.map.bounds.min.y, conf.map.bounds.max.y);

                if (x > conf.cayo.minMap.x && y < conf.cayo.minMap.y || this.cayoCalibrationMode) {
                    coords.x = this.mapNumber(coords.x, conf.cayo.game.bounds.min.x, conf.cayo.game.bounds.max.x, conf.cayo.map.bounds.min.x, conf.cayo.map.bounds.max.x);
                    coords.y = this.mapNumber(coords.y, conf.cayo.game.bounds.min.y, conf.cayo.game.bounds.max.y, conf.cayo.map.bounds.min.y, conf.cayo.map.bounds.max.y);
                } else {
                    coords.x = x;
                    coords.y = y;
                }

                return this.coords(coords);
            } else {
                const x = this.mapNumber(coords.lng, conf.map.bounds.min.x, conf.map.bounds.max.x, conf.game.bounds.min.x, conf.game.bounds.max.x);
                const y = this.mapNumber(coords.lat, conf.map.bounds.min.y, conf.map.bounds.max.y, conf.game.bounds.min.y, conf.game.bounds.max.y);

                if (coords.lng > conf.cayo.minMap.x && coords.lat < conf.cayo.minMap.y || this.cayoCalibrationMode) {
                    coords.x = this.mapNumber(coords.lng, conf.cayo.map.bounds.min.x, conf.cayo.map.bounds.max.x, conf.cayo.game.bounds.min.x, conf.cayo.game.bounds.max.x);
                    coords.y = this.mapNumber(coords.lat, conf.cayo.map.bounds.min.y, conf.cayo.map.bounds.max.y, conf.cayo.game.bounds.min.y, conf.cayo.game.bounds.max.y);
                } else {
                    coords.x = x;
                    coords.y = y;
                }

                return coords;
            }
        },
        addToLayer(marker, layer) {
            const _this = this;

            $.each(this.layers, function (key) {
                if (layer !== key) {
                    _this.layers[key].removeLayer(marker);
                }
            });

            this.layers[layer].addLayer(marker);
        },
        getLayer(player, isDriving, isPassenger, isInvisible, isDead) {
            const vehicle = this.getVehicleType(player.vehicle);

            if (vehicle && (vehicle.type === 'police_car' || vehicle.type === 'ems_car')) {
                return "Emergency Vehicles";
            }
            if (isDriving || isPassenger) {
                return "Vehicles";
            } else if (isDead) {
                return "Dead Players";
            } else {
                return "Players";
            }
        },
        renderMapData(data) {
            if (this.isPaused || this.isDragging) {
                return;
            }

            if (this.trackedPlayer && this.trackedPlayer in this.markers) {
                this.map.dragging.disable();
            } else {
                this.map.dragging.enable();
            }

            if (data && 'status' in data && data.status) {
                this.data = this.t('map.advanced_error', $('#server option:selected').text(), data.message);
            } else if (data && Array.isArray(data)) {
                if (this.map) {
                    const _this = this;
                    let markers = this.markers;

                    let vehicles = {};
                    $.each(data, function (index, player) {
                        if ('vehicle' in player && player.vehicle && player.vehicle.driving) {
                            vehicles[player.vehicle.id] = !player.character ? 'Nobody' : player.character.fullName;
                        }

                        data[index] = player
                    });

                    let validIds = [];
                    let afkList = [];
                    let invisibleList = [];
                    $.each(data, function (_, player) {
                        if (!player.character) {
                            return;
                        }

                        const id = "player_" + player.character.id,
                            rawCoords = {x: Math.round(player.coords.x), y: Math.round(player.coords.y), z: Math.round(player.coords.z)},
                            originalCoords = rawCoords.x + ' ' + rawCoords.y + ' ' + rawCoords.z,
                            coords = _this.convertCoords(player.coords),
                            heading = _this.mapNumber(-player.heading, -180, 180, 0, 360) - 180,
                            isDriving = 'vehicle' in player && player.vehicle && player.vehicle.driving,
                            isPassenger = 'vehicle' in player && player.vehicle && !player.vehicle.driving,
                            isInvisible = 'invisible' in player && player.invisible,
                            ignoreInvisible = _this.shouldIgnoreInvisible(rawCoords, player.steamIdentifier),
                            isDead = player.character && 'dead' in player.character && player.character.dead,
                            speed = 'speed' in player ? player.speed : null,
                            icon = _this.getIcon(player, isDriving, isPassenger, isInvisible, isDead),
                            vehicle = _this.getVehicleType(player.vehicle),
                            isStaff = _this.staff.includes(player.steamIdentifier);

                        if (playerCallback && playerCallbackCid === player.character.id) {
                            playerCallbackCid = null;
                            playerCallback(player, coords, _this);
                            playerCallback = null;
                        }

                        _this.updateDetectionAreas(rawCoords, player);

                        if (isNaN(coords.lat) || isNaN(coords.lng)) {
                            console.debug('NaN Coords', coords, player);
                            return;
                        }

                        validIds.push(id);

                        if (id in markers) {
                            markers[id].setIcon(icon);
                            markers[id].setLatLng(coords);
                            markers[id].setRotationAngle(heading);
                        } else {
                            let marker = L.marker(coords,
                                {
                                    icon: icon,
                                    rotationAngle: heading
                                }
                            );

                            marker.bindPopup('', {
                                autoPan: false
                            });

                            markers[id] = marker;
                        }

                        _this.addToLayer(markers[id], _this.getLayer(player, isDriving, isPassenger, isInvisible, isDead));

                        let extra = '<br>Altitude: ' + Math.round(player.coords.z) + 'm';
                        if (speed) {
                            extra += '<br>Speed: ' + Math.round(speed * 2.236936) + ' mph';
                        }

                        let attributes = [];
                        if (isInvisible) {
                            attributes.push('invisible' + (ignoreInvisible ? ' (ok)' : ''));
                            markers[id].options.forceZIndex = 101;
                        }
                        if (isDead) {
                            attributes.push('dead');
                            markers[id].options.forceZIndex = 101;
                        }
                        if (isStaff) {
                            attributes.push('a staff member');
                        }
                        if (isDriving) {
                            attributes.push('driving (' + (vehicle.type === 'car' ? 'car/bike' : vehicle.type) + ')');
                            markers[id].options.forceZIndex = 100;
                        } else if (isPassenger) {
                            attributes.push('passenger of ' + (player.vehicle.id in vehicles ? vehicles[player.vehicle.id] : 'Nobody'));
                            markers[id].options.forceZIndex = 102;
                        } else {
                            attributes.push('on foot');
                            markers[id].options.forceZIndex = 101;
                        }
                        const lastExtra = attributes.pop();
                        extra += '<br><i>Is ' + (attributes.length > 0 ? attributes.join(', ') + ' and ' : '') + lastExtra + '</i>';

                        if (player.afk > 15 * 60) {
                            extra += '<br><i>Hasn\'t moved in ' + _this.formatSeconds(player.afk) + '</i>';
                        }
                        if (player.afk > 30 * 60) {
                            const linkColor = isStaff ? 'rgb(16, 185, 129)' : (() => {
                                let rainbow = new Rainbow();
                                rainbow.setNumberRange(30 * 60, 3 * 60 * 60);
                                rainbow.setSpectrum('#d9ff00', '#ffbf00', '#ff6600', '#ff0000');

                                return '#' + rainbow.colourAt(player.afk);
                            })();

                            const humanized = _this.$options.filters.humanizeSeconds(player.afk);

                            afkList.push(`<tr title="` + (isStaff ? 'Is a staff member' : '') + `">
    <td class="pr-2"><a style="color: ` + linkColor + `" target="_blank" href="/players/` + player.steamIdentifier + `">` + player.character.fullName + ` (` + player.source + `)</a></td>
    <td class="pr-2">hasn't moved in ` + _this.formatSeconds(player.afk) + `</td>
    <td><a class="track-cid" style="color: ` + linkColor + `" href="#" data-trackid="` + id + `" data-popup="true">[Track]</a></td>
    <td><a style="color: ` + linkColor + `" href="/players/` + player.steamIdentifier + `?kick=` + encodeURIComponent(_this.t('map.kick_reason', humanized)) + `">[Kick]</a></td>
</tr>`.replace(/\r?\n(\s{4})?/gm, ''));
                        }

                        if (isInvisible && !ignoreInvisible) {
                            invisibleList.push(`<tr>
    <td class="pr-2"><a style="color:#54BBFF" target="_blank" href="/players/` + player.steamIdentifier + `">` + player.character.fullName + `</a> (` + player.source + `)</td>
    <td class="pr-2">is invisible</td>
    <td><a class="track-cid" style="color:#54BBFF" href="#" data-trackid="` + id + `" data-popup="true">[Track]</a></td>
</tr>`.replace(/\r?\n(\s{4})?/gm, ''));
                        }

                        if (_this.trackedPlayer && (_this.trackedPlayer === 'server_' + player.source || (_this.trackedPlayer.startsWith('steam:') && _this.trackedPlayer === player.steamIdentifier))) {
                            _this.trackedPlayer = id;
                            window.location.hash = id;
                        }

                        if (_this.trackedPlayer === id) {
                            extra += '<br><br><a href="#" class="track-cid" data-trackid="stop">' + _this.t('map.stop_track') + '</a>';

                            _this.map.setView(coords, _this.firstRefresh ? 6 : _this.map.getZoom(), {
                                duration: 0.1
                            });

                            _this.tracking.data.speed = Math.round(speed * 2.236936);

                            const feet = Math.round(player.coords.z * 3.281);

                            _this.tracking.data.alt = (feet / 5000) * 100;
                            _this.tracking.data.alt = _this.tracking.data.alt > 99 ? 99 : _this.tracking.data.alt;
                            _this.tracking.data.altitude = feet + 'ft';

                            let trackingInfo = [
                                player.character.fullName + ' (' + player.source + ')',
                                'Coords:  ' + originalCoords
                            ];

                            !player.vehicle || trackingInfo.push('Vehicle: ' + player.vehicle.model);
                            player.afk < 15 || trackingInfo.push('AFK since ' + _this.$options.filters.humanizeSeconds(player.afk));

                            _this.tracking.data.advanced = trackingInfo.join("\n");

                            markers[id].options.forceZIndex = 200;

                            if (_this.firstRefresh) {
                                _this.openPopup = id;
                            }
                        } else {
                            extra += '<br><br><a href="#" class="track-cid" data-trackid="' + id + '">' + _this.t('map.track') + '</a>';
                        }

                        markers[id]._popup.setContent(player.character.fullName + '<sup>' + player.source + '</sup> (<a href="/players/' + player.steamIdentifier + '" target="_blank">#' + player.character.id + '</a>)' + extra);

                        if (_this.openPopup === id) {
                            markers[id].openPopup();
                            _this.openPopup = null;
                        }
                    });

                    this.afkPeople = afkList.length > 0 ? '<table>' + afkList.join("\n") + '</table>' : '';
                    this.invisiblePeople = invisibleList.length > 0 ? '<table>' + invisibleList.join("\n") + '</table>' : '';

                    $.each(markers, function (id, marker) {
                        if (!validIds.includes(id)) {
                            _this.map.removeLayer(marker);
                            delete markers[id];
                        }
                    });

                    this.markers = markers;

                    this.data = this.t('map.data', Object.keys(this.markers).length);
                }
            } else {
                this.data = this.t('map.error', $('#server option:selected').text());
            }
        },
        debugLocations(locations) {
            const _this = this;

            $.each(locations, function (k, coords) {
                L.marker(_this.convertCoords(coords),
                    {
                        icon: new L.Icon(
                            {
                                iconUrl: '/images/icons/circle_red.png',
                                iconSize: [25, 25]
                            }
                        ),
                        forceZIndex: 300
                    }
                ).addTo(_this.map);
            });
        },
        async buildMap() {
            if (this.map) {
                return;
            }
            const _this = this;

            L.Map.addInitHook("addHandler", "gestureHandling", GestureHandling);

            this.map = L.map('map', {
                crs: L.CRS.Simple,
                gestureHandling: true,
                minZoom: 1,
                maxZoom: 8,
                maxBounds: L.latLngBounds(L.latLng(0, 0), L.latLng(-256, 256))
            });
            this.map.attributionControl.addAttribution('map by <a href="https://github.com/milan60" target="_blank">milan60</a>, cayo-perico by Spitfire2k6');

            const b = this.getBounds();
            L.tileLayer("https://cdn.celestial.network/tiles_" + b.version + "/{z}/{x}/{y}.jpg", {
                noWrap: true,
                bounds: [
                    [0, 0],
                    [-256, 256],
                ],
            }).addTo(this.map);

            this.map.setView([-124, 124], 3);

            L.control.layers({}, this.layers).addTo(this.map);

            $.each(this.layers, function (key) {
                _this.layers[key].addTo(_this.map);
            });

            $.each(this.blips, function (_, blip) {
                const coords = eval('(() => (' + blip.coords + '))()'),
                    coordsText = coords.x.toFixed(2) + ' ' + coords.y.toFixed(2);
                let marker = L.marker(_this.convertCoords(coords),
                    {
                        icon: new L.Icon(
                            {
                                iconUrl: '/images/icons/' + blip.icon,
                                iconSize: [22, 22]
                            }
                        ),
                        forceZIndex: 99
                    }
                );

                marker.bindPopup(blip.label + '<br><i>' + coordsText + '</i>', {
                    autoPan: false
                });

                _this.layers["Blips"].addLayer(marker);
            });

            //this.debugLocations(require('../../data/tp_locations.json'));

            this.map.on('click', function (e) {
                let map = {
                    x: e.latlng.lng,
                    y: e.latlng.lat,
                };
                let game = _this.convertCoords(e.latlng);

                _this.clickedCoords = "[X=" + Math.round(game.x) + ",Y=" + Math.round(game.y) + "] / [X=" + map.x + ",Y=" + map.y + "]";
                _this.rawClickedCoords = {x: Math.round(game.x), y: Math.round(game.y)};
                _this.coordsCommand = "/tp_coords " + Math.round(game.x) + " " + Math.round(game.y);

                console.info('Clicked coordinates', map, game);
            });

            this.map.on('dragstart', function () {
                _this.isDragging = true;
            });
            this.map.on('dragend', function () {
                _this.isDragging = false;
            });
            this.map.on('fullscreenchange', function () {
                setTimeout(function () {
                    _this.map._onResize();
                }, 500);
            });

            this.map.addControl(new L.Control.Fullscreen());

            $('#map-wrapper').on('click', '.track-cid', function (e) {
                e.preventDefault();

                const track = $(this).data('trackid');
                if (track === 'stop') {
                    _this.trackedPlayer = null;
                    window.location.hash = '';
                } else {
                    _this.trackedPlayer = track;
                    window.location.hash = track;

                    _this.map.closePopup();

                    if ($(this).data('popup')) {
                        _this.openPopup = track;
                    }
                }
            });

            const styles = [
                '.leaflet-marker-icon {transform-origin:center center !important;}',
                '.leaflet-grab {cursor:default;}',
                '.coordinate-attr {font-size: 11px;padding:0 5px;color:rgb(0, 120, 168);line-height:16.5px}',
                '.leaflet-control-layers-overlays {user-select:none !important}',
                '.leaflet-control-layers-selector {outline:none !important}',
                '.leaflet-container {background:#143D6B}',
                'path.leaflet-interactive[stroke="#FFBF00"] {cursor:default}'
            ];
            $('#map').append('<style>' + styles.join('') + '</style>');
        }
    },
    mounted() {
        const _this = this;
        this.buildMap();

        $(document).ready(function () {
            $('#server').on('change', function () {
                _this.firstRefresh = true;

                _this.doMapRefresh($(this).val());
            }).trigger('change');
        });

        if (Math.round(Math.random() * 100) === 1) { // 1% chance it says fib spy satellite map
            $(document).ready(function () {
                $('#map_title').text(_this.t('map.spy_satellite'));
            });
        }

        VueInstance = this;
    }
};
</script>
