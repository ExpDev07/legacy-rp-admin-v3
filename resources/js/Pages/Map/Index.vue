<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white !mb-2">
                {{ t('map.title') }}
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
            <div>
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
            <div class="relative">
                <div id="map" class="w-map h-max -mt-12 max-w-full relative"></div>
                <pre class="bg-opacity-70 bg-white coordinate-attr absolute bottom-0 left-0 cursor-pointer z-1k" @click="copyText($event, clickedCoords)" v-if="clickedCoords">{{ clickedCoords }}</pre>
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
let printPlayerInfo = null;
window.debug = function(cid) {
    printPlayerInfo = cid;
};
window.track = function(cid) {
    window.location.hash = 'player_' + cid;
    window.location.reload();
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
            clickedCoords: ''
        };
    },
    methods: {
        getBounds() {
            return {
                game: {
                    bounds: {
                        min: {x: -2862.10546875, y: 7616.0966796875},
                        max: {x: 4195.29248046875, y: -3579.89013671875}
                    }
                },
                map: {
                    bounds: {
                        min: {x: 85.546875, y: -59.62890625},
                        max: {x: 174.2109375, y: -200.24609375}
                    }
                }
            };
        },
        mapNumber(val, in_min, in_max, out_min, out_max) {
            return (val - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
        },
        copyText(e, text) {
            e.preventDefault();
            const button = $(e.target).closest('a');

            this.$copyText(text).then(function() {
                button.removeClass('bg-blue-800');
                button.addClass('bg-green-600');

                setTimeout(function() {
                    button.removeClass('bg-green-600');
                    button.addClass('bg-blue-800');
                }, 500);
            });
        },
        coords(coords) {
            return {
                lat: coords.y,
                lng: coords.x
            }
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

            $.each(custom_icons, function(type, cfg) {
                if (cfg.models.includes(vehicle.model)) {
                    ret.type = type;
                    ret.size = cfg.size;
                }
            });

            return ret;
        },
        getIcon(player, isDriving, isPassenger, isInvisible) {
            let size = {
                circle: 17,
                circle_red: 12,
                circle_green: 13
            };

            let icon = new L.Icon(
                {
                    iconUrl: '/images/circle.png',
                    iconSize: [size.circle, size.circle]
                }
            );

            if (isInvisible) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/circle_green.png',
                        iconSize: [size.circle_green, size.circle_green]
                    }
                );
            } else if (isDriving) {
                const info = this.getVehicleType(player.vehicle);

                icon = new L.Icon(
                    {
                        iconUrl: '/images/' + info.type + '.png',
                        iconSize: [info.size, info.size]
                    }
                );
            } else if (isPassenger) {
                icon = new L.Icon(
                    {
                        iconUrl: '/images/circle_red.png',
                        iconSize: [size.circle_red, size.circle_red]
                    }
                )
            }

            return icon;
        },
        renderMapData(data) {
            if (this.isPaused) {
                return;
            }

            if (this.trackedPlayer && this.trackedPlayer in this.markers) {
                this.map.dragging.disable();
            } else {
                this.map.dragging.enable();
            }

            const conf = this.getBounds();
            const _this = this;
            const convert = coords => {
                coords.x = _this.mapNumber(coords.x, conf.game.bounds.min.x, conf.game.bounds.max.x, conf.map.bounds.min.x, conf.map.bounds.max.x);
                coords.y = _this.mapNumber(coords.y, conf.game.bounds.min.y, conf.game.bounds.max.y, conf.map.bounds.min.y, conf.map.bounds.max.y);

                return _this.coords(coords);
            };

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
                    $.each(data, function (_, player) {
                        if (!player.character) {
                            return;
                        }

                        const id = "player_" + player.character.id,
                            coords = convert(player.coords),
                            heading = _this.mapNumber(-player.heading, -180, 180, 0, 360) - 180,
                            isDriving = 'vehicle' in player && player.vehicle && player.vehicle.driving,
                            isPassenger = 'vehicle' in player && player.vehicle && !player.vehicle.driving,
                            isInvisible = 'invisible' in player && player.invisible,
                            speed = 'vehicle' in player && player.vehicle && 'speed' in player.vehicle ? player.vehicle.speed : null,
                            icon = _this.getIcon(player, isDriving, isPassenger, isInvisible),
                            vehicle = _this.getVehicleType(player.vehicle);

                        if (printPlayerInfo && printPlayerInfo === player.character.id) {
                            printPlayerInfo = null;
                            console.info('Player debug', player);
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

                            marker.addTo(_this.map);

                            marker.bindPopup('', {
                                autoPan: false
                            });

                            markers[id] = marker;
                        }

                        let extra = '<br>Altitude: ' + Math.round(player.coords.z) + 'm';
                        if (speed) {
                            extra += '<br>Speed: ' + Math.round(speed * 2.236936) + ' mph';
                        }

                        let attributes = [];
                        if (isInvisible) {
                            attributes.push('invisible');
                            markers[id].options.forceZIndex = 101;
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
                        extra += '<br><i>Is ' + attributes.shift() + (attributes.length > 0 ? ' and ' + attributes.join(', ') : '') + '</i>';
                        if (_this.trackedPlayer === id) {
                            extra += '<br><br><a href="#" class="track-cid" data-trackid="stop"">' + _this.t('map.stop_track') + '</a>';

                            _this.map.setView(coords, _this.firstRefresh ? 6 : _this.map.getZoom(), {
                                duration: 0.1
                            });
                        } else {
                            extra += '<br><br><a href="#" class="track-cid" data-trackid="' + id + '"">' + _this.t('map.track') + '</a>';
                        }

                        markers[id]._popup.setContent(player.character.fullName + ' (<a href="/players/' + player.steamIdentifier + '">#' + player.character.id + '</a>)' + extra);
                    });

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
            const range = (coords, max) => {
                if (coords.x < 0 || coords.y < 0 || coords.x > max || coords.y > max) {
                    coords.z = 2;
                    coords.y = 0;
                    coords.x = 0;
                }

                return coords;
            };

            L.Map.addInitHook("addHandler", "gestureHandling", GestureHandling);

            const url = this.hostname(false);
            L.TileLayer.GTA = L.TileLayer.extend({
                getTileUrl: function (coords) {
                    coords.x = coords.x < 0 ? 0 : coords.x;
                    coords.y = coords.y < 0 ? 0 : coords.y;

                    switch (coords.z) {
                        case 0:
                            coords = range(coords, 0);
                            break;
                        case 1:
                            coords = range(coords, 1);
                            break;
                        case 2:
                            coords = range(coords, 3);
                            break;
                        case 3:
                            coords = range(coords, 7);
                            break;
                        case 4:
                            coords = range(coords, 15);
                            break;
                        case 5:
                            coords = range(coords, 31);
                            break;
                        case 6:
                            coords = range(coords, 63);
                            break;
                        case 7:
                            coords = range(coords, 127);
                            break;
                        case 8:
                            break;
                    }

                    return url + '/map/go/tiles/' + coords.z + '_' + coords.x + '_' + coords.y + '.jpg';
                }
            });

            L.tileLayer.gta = function () {
                return new L.TileLayer.GTA();
            }

            this.map = L.map('map', {
                crs: L.CRS.Simple,
                gestureHandling: true,
                minZoom: 2,
                maxZoom: 7,
                maxBounds: L.latLngBounds(L.latLng(-41, 66), L.latLng(-217, 185))
            });
            this.map.attributionControl.addAttribution('<a href="https://github.com/milan60" target="_blank">milan60</a>');

            L.tileLayer.gta().addTo(this.map);

            this.map.setView([-124, 124], 3);

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

                console.info('Clicked coordinates', map);
            });

            $('#map').on('click', '.track-cid', function(e) {
                e.preventDefault();

                const track = $(this).data('trackid');
                if (track === 'stop') {
                    _this.trackedPlayer = null;
                    window.location.hash = '';
                } else {
                    _this.trackedPlayer = track;
                    window.location.hash = track;

                    _this.map.closePopup();
                }
            });

            const styles = [
                '.leaflet-marker-icon {transform-origin: center center !important;}',
                '.leaflet-grab {cursor: default;}',
                '.coordinate-attr{font-size: 11px;padding: 0 5px;color:rgb(0, 120, 168);line-height:16.5px}',
            ];
            $('#map').append('<style>' + styles.join('') + '</style>');
        }
    },
    mounted() {
        const _this = this;
        this.buildMap();

        $(document).ready(function () {
            $('#server').on('change', function () {
                _this.doMapRefresh($(this).val());
            });
            $('#server').trigger('change');
        });
    }
};
</script>
