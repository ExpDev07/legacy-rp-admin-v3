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
                <button class="px-5 py-2 mr-3 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3" @click="stopTracking()" v-if="trackedPlayer">
                    <i class="fas fa-stop mr-1"></i>
                    {{ t('map.stop_track') }}
                </button>
                <!-- Play/Pause -->
                <button class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3" @click="isPaused = true" v-if="!isPaused">
                    <i class="fas fa-pause"></i>
                    {{ t('map.pause') }}
                </button>
                <button class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3" @click="isPaused = false" v-if="isPaused">
                    <i class="fas fa-play"></i>
                    {{ t('map.play') }}
                </button>
            </div>
        </portal>

        <template>
            <div class="-mt-12" id="map-wrapper">
                <div class="flex flex-wrap mb-2">
                    <input type="text" class="form-control w-56 rounded border block mobile:w-full px-4 py-2 bg-gray-200 dark:bg-gray-600" :placeholder="t('map.track_placeholder')" v-model="tracking.id" />
                    <select class="block w-44 ml-2 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded mobile:w-full mobile:m-0 mobile:mt-1" v-model="tracking.type">
                        <option value="server_">{{ t('map.track_server') }}</option>
                        <option value="">{{ t('map.track_steam') }}</option>
                        <option value="player_">{{ t('map.track_character') }}</option>
                    </select>
                    <button class="px-5 py-2 ml-2 font-semibold text-white rounded bg-primary dark:bg-dark-primary mobile:block mobile:w-full mobile:m-0 mobile:mt-1" @click="trackId(tracking.type + tracking.id)">
                        {{ t('map.do_track') }}
                    </button>
                </div>
                <div class="relative">
                    <div id="map" class="w-map max-w-full relative h-max"></div>
                    <pre class="bg-opacity-70 bg-white coordinate-attr absolute bottom-0 left-0 cursor-pointer z-1k" v-if="clickedCoords"><span @click="copyText($event, clickedCoords)">{{ clickedCoords }}</span> / <span @click="copyText($event, coordsCommand)">{{ t('map.command') }}</span></pre>
                </div>
                <div v-if="afkPeople" class="w-map-right pt-4">
                    <h3 class="mb-2">{{ t('map.afk_title') }}</h3>
                    <pre v-html="afkPeople" class="text-sm">{{ afkPeople }}</pre>
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
import custom_icons from "../../data/vehicles.json";
import model_hash from "../../data/model_hash.json";
const Rainbow = require('rainbowvis.js');

(function(global){
    let MarkerMixin = {
        _updateZIndex: function (offset) {
            this._icon.style.zIndex = this.options.forceZIndex ? (this.options.forceZIndex + (this.options.zIndexOffset || 0)) : (this._zIndex + offset);
        },
        setForceZIndex: function(forceZIndex) {
            this.options.forceZIndex = forceZIndex ? forceZIndex : null;
        }
    };
    if (global) global.include(MarkerMixin);
})(L.Marker);

// Some functions for debugging
let playerCallback = null,
    playerCallbackCid = null,
    VueInstance = null;
window.debug = function(cid) {
    playerCallbackCid = cid;
    playerCallback = function(player, coords, _this) {
        console.info('Player debug', player);
    };
};
window.track = function(cid) {
    window.location.hash = 'player_' + cid;
    window.location.reload();
};
window.marker = function(cid) {
    playerCallbackCid = cid;
    playerCallback = function(player, coords, _this) {
        console.info('Marker');
        const markerCode = `{lat: ` + coords.lat + `, lng: ` + coords.lng + `}`;
        _this.copyText(null, markerCode);

        console.info(`{lat: ` + coords.lat + `, lng: ` + coords.lng + `}`, '(Copied to clipboard)');
    };
};
window.convertCoords = function(coords) {
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

window.loadHistory = function(server, player, day) {
    if (VueInstance) {
        $.post(VueInstance.hostname() + '/map/go/history', {
            server: server,
            player: player,
            day: day,
            token: VueInstance.token
        }, console.log);
    }
};

export default {
    layout: Layout,
    components: {
        VSection,
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
        }
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
            coordsCommand: '',
            afkPeople: '',
            openPopup: null,
            isDragging: false,
            layers: {
                "Players": L.layerGroup(),
                "Dead Players": L.layerGroup(),
                "Emergency Vehicles": L.layerGroup(),
                "Vehicles": L.layerGroup(),
                "Blips": L.layerGroup(),
            },
            tracking: {
                id: '',
                type: 'server_'
            }
        };
    },
    methods: {
        getBounds() {
            return {
                game: {
                    bounds: {
                        min: {x: -2861.829, y: 7679.829},
                        max: {x: 4180.101, y: -3579.837}
                    }
                },
                map: {
                    bounds: {
                        min: {x: 58.45703125, y: -14.693359375},
                        max: {x: 203.40234375, y: -246.373046875}
                    }
                }
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
        convertVehicleHash(vehicle) {
            const hash = parseInt(vehicle);
            if (isNaN(hash)) {
                return vehicle in model_hash ? model_hash[vehicle] : null;
            } else {
                let result = null;
                $.each(model_hash, function(model, _hash) {
                    if (hash === _hash) {
                        result = model;
                        return false;
                    }
                });

                return result;
            }
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
        hostname(isSocket) {
            const isDev = window.location.hostname === 'localhost';

            if (isSocket) {
                return isDev ? 'ws://' + window.location.hostname + ':8080' : 'wss://' + window.location.hostname + ':8443';
            } else {
                return isDev ? 'http://' + window.location.hostname + ':8080' : 'https://' + window.location.hostname + ':8443';
            }
        },
        async doMapRefresh(server) {
            const _this = this;

            if (this.connection) {
                this.connection.close();
            }

            try {
                this.connection = new WebSocket(this.hostname(true) + "/map/go/socket?server=" + encodeURIComponent(server));

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

            const inverse = this.convertVehicleHash(vehicle.model);

            $.each(custom_icons, function(type, cfg) {
                if (cfg.models.includes(vehicle.model+"") || (inverse && cfg.models.includes(inverse+""))) {
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
                circle_red: 12,
                circle_green: 13
            };

            let icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle.png',
                    iconSize: [size.circle, size.circle]
                }
            );

            if (isDead) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/skull.png',
                        iconSize: [size.skull, size.skull]
                    }
                );
            } else if (isInvisible) {
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
            } else if (isPassenger) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/icons/circle_red.png',
                        iconSize: [size.circle_red, size.circle_red]
                    }
                )
            }

            return icon;
        },
        formatSeconds(seconds) {
            return this.$moment.utc(seconds * 1000).format('HH:mm:ss');
        },
        convertCoords(coords) {
            const conf = this.getBounds();

            if ('x' in coords) {
                coords.x = this.mapNumber(coords.x, conf.game.bounds.min.x, conf.game.bounds.max.x, conf.map.bounds.min.x, conf.map.bounds.max.x);
                coords.y = this.mapNumber(coords.y, conf.game.bounds.min.y, conf.game.bounds.max.y, conf.map.bounds.min.y, conf.map.bounds.max.y);

                return this.coords(coords);
            } else {
                coords.x = this.mapNumber(coords.lng, conf.map.bounds.min.x, conf.map.bounds.max.x, conf.game.bounds.min.x, conf.game.bounds.max.x);
                coords.y = this.mapNumber(coords.lat, conf.map.bounds.min.y, conf.map.bounds.max.y, conf.game.bounds.min.y, conf.game.bounds.max.y);

                return coords;
            }
        },
        addToLayer(marker, layer) {
            const _this = this;

            $.each(this.layers, function(key) {
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
            } if (isDriving || isPassenger) {
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

            if (data && Array.isArray(data)) {
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
                    $.each(data, function (_, player) {
                        if (!player.character) {
                            return;
                        }

                        const id = "player_" + player.character.id,
                            coords = _this.convertCoords(player.coords),
                            heading = _this.mapNumber(-player.heading, -180, 180, 0, 360) - 180,
                            isDriving = 'vehicle' in player && player.vehicle && player.vehicle.driving,
                            isPassenger = 'vehicle' in player && player.vehicle && !player.vehicle.driving,
                            isInvisible = 'invisible' in player && player.invisible,
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
                            attributes.push('invisible');
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
    <td class="pr-2"><a style="color: ` + linkColor + `" target="_blank" href="/players/` + player.steamIdentifier + `">` + player.character.fullName + `</a></td>
    <td class="pr-2">hasn't moved in ` + _this.formatSeconds(player.afk) + `</td>
    <td><a class="track-cid" style="color: ` + linkColor + `" href="#" data-trackid="` + id + `" data-popup="true">[Track]</a></td>
    <td><a style="color: ` + linkColor + `" href="/players/` + player.steamIdentifier + `?kick=` + encodeURIComponent(_this.t('map.kick_reason', humanized)) + `">[Kick]</a></td>
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
            this.map.attributionControl.addAttribution('<a href="https://github.com/milan60" target="_blank">milan60</a>');

            L.tileLayer("https://cdn.celestial.network/tiles/{z}/{x}/{y}.jpg", {
                noWrap: true,
                bounds: [
                    [0, 0],
                    [-256, 256],
                ],
            }).addTo(this.map);

            this.map.setView([-124, 124], 3);

            L.control.layers({}, this.layers).addTo(this.map);

            $.each(this.layers, function(key) {
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

            this.map.on('click', function (e) {
                const conf = _this.getBounds();
                let map = {
                    x: e.latlng.lng,
                    y: e.latlng.lat,
                };
                let game = {
                    x: _this.mapNumber(e.latlng.lng, conf.map.bounds.min.x, conf.map.bounds.max.x, conf.game.bounds.min.x, conf.game.bounds.max.x),
                    y: _this.mapNumber(e.latlng.lat, conf.map.bounds.min.y, conf.map.bounds.max.y, conf.game.bounds.min.y, conf.game.bounds.max.y),
                };

                _this.clickedCoords = "[X=" + Math.round(game.x) + ",Y=" + Math.round(game.y) + "] / [X=" + map.x + ",Y=" + map.y + "]";
                _this.coordsCommand = "/tp_coords " + Math.round(game.x) + " " + Math.round(game.y);

                console.info('Clicked coordinates', map, game);
            });

            this.map.on('dragstart', function (e) {
                _this.isDragging = true;
            });
            this.map.on('dragend', function (e) {
                _this.isDragging = false;
            });

            $('#map-wrapper').on('click', '.track-cid', function(e) {
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
                '.leaflet-control-layers-selector {outline:none !important}'
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
            });
            $('#server').trigger('change');
        });

        if (Math.round(Math.random() * 100) === 1) { // 1% chance it says fib spy satellite map
            $(document).ready(function() {
                $('#map_title').text(_this.t('map.spy_satellite'));
            });
        }

        VueInstance = this;
    }
};
</script>
