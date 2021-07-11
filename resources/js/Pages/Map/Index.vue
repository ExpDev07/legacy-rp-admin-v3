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
            <div id="map" class="w-map h-max -mt-12 max-w-full"></div>
        </template>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import L from "leaflet";
import {GestureHandling} from "leaflet-gesture-handling";
import "leaflet-rotatedmarker";

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
            isDevelopment: false // For local development set it to true
        };
    },
    methods: {
        mapNumber(val, in_min, in_max, out_min, out_max) {
            return (val - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
        },
        coords(coords) {
            return {
                lat: coords.y,
                lng: coords.x
            }
        },
        hostname(isSocket) {
            if (isSocket) {
                return this.isDevelopment ? 'ws://' + window.location.hostname + ':8080/' : 'wss://' + window.location.hostname + ':8443';
            } else {
                return this.isDevelopment ? 'http://' + window.location.hostname + ':8080/' : 'https://' + window.location.hostname + ':8443';
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
        renderMapData(data) {
            if (this.isPaused) {
                return;
            }

            const conf = {
                game: {
                    bounds: {
                        min: {x: -2870, y: 7000},
                        max: {x: 4000, y: -3500}
                    }
                },
                map: {
                    bounds: {
                        min: {x: 85.5, y: -67.1},
                        max: {x: 172.1, y: -199.4}
                    }
                }
            };
            const _this = this;
            const convert = coords => {
                coords.x = _this.mapNumber(coords.x, conf.game.bounds.min.x, conf.game.bounds.max.x, conf.map.bounds.min.x, conf.map.bounds.max.x);
                coords.y = _this.mapNumber(coords.y, conf.game.bounds.min.y, conf.game.bounds.max.y, conf.map.bounds.min.y, conf.map.bounds.max.y);

                return _this.coords(coords);
            };
            const getIcon = (isDriving, isPassenger) => {
                const zoom = _this.map.getZoom();
                const zoomModifier = zoom === 7 ? 1.1 : 1;

                let icon = new L.Icon(
                    {
                        iconUrl: '/images/circle.png',
                        iconSize: [17 * zoomModifier, 17 * zoomModifier],
                        iconAnchor: [(17 * zoomModifier) / 2, (17 * zoomModifier) / 2]
                    }
                );

                if (isDriving) {
                    icon = new L.Icon(
                        {
                            iconUrl: '/images/car.png',
                            iconSize: [20 * zoomModifier, 20 * zoomModifier],
                            iconAnchor: [(20 * zoomModifier) / 2, (20 * zoomModifier) / 2]
                        }
                    );
                } else if (isPassenger) {
                    icon = new L.Icon(
                        {
                            iconUrl: '/images/circle_red.png',
                            iconSize: [12 * zoomModifier, 12 * zoomModifier],
                            iconAnchor: [(12 * zoomModifier) / 2, (12 * zoomModifier) / 2]
                        }
                    )
                }

                return icon;
            };

            if (data && Array.isArray(data)) {
                if (this.map) {
                    const _this = this;
                    let markers = this.markers;

                    let validIds = [];
                    $.each(data, function (_, player) {
                        const id = "player_" + player.character.id,
                            coords = convert(player.coords),
                            heading = _this.mapNumber(-player.heading, -180, 180, 0, 360) - 180,
                            isDriving = 'vehicle' in player && player.vehicle && player.vehicle.driving,
                            isPassenger = 'vehicle' in player && player.vehicle && !player.vehicle.driving,
                            icon = getIcon(isDriving, isPassenger);

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

                            marker.bindPopup('');

                            markers[id] = marker;
                        }

                        let extra = '';//'<br>Altitude: ' + Math.round(player.coords.z);
                        if (isDriving) {
                            extra += '<br>Is driving';
                            markers[id].options.forceZIndex = 100;
                            //markers[id].setZIndexOffset(2);
                        } else if (isPassenger) {
                            extra += '<br>Is a passenger';
                            markers[id].options.forceZIndex = 102;
                            //markers[id].setZIndexOffset(3);
                        } else {
                            extra += '<br>Is on foot';
                            markers[id].options.forceZIndex = 101;
                            //markers[id].setZIndexOffset(1);
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
            const range = (coords, max) => {
                if (coords.x < 0 || coords.y < 0 || coords.x > max || coords.y > max) {
                    coords.z = 2;
                    coords.y = 0;
                    coords.x = 0;
                }

                return coords;
            };

            L.Map.addInitHook("addHandler", "gestureHandling", GestureHandling);

            const _this = this,
                url = this.hostname(false);
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

            L.tileLayer.gta().addTo(this.map);

            this.map.setView([-124, 124], 3);

            this.map.on('click', function (e) {
                console.log('map', e.latlng);
            });
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
