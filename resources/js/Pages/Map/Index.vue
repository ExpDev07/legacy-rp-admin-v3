<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white !mb-2">
                {{ t('map.title') }}
                <select class="inline-block w-90 ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="server">
                    <option v-for="server in servers" :key="server.id" :value="server.id">{{ server.name }}</option>
                </select>
            </h1>
            <p>
                {{ data }}
            </p>
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
import { GestureHandling } from "leaflet-gesture-handling";
import "leaflet-rotatedmarker";

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
            players: [],
            map: null,
            markers: {},
            data: this.t('map.loading')
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
        async refreshMap() {
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

            const server = $('#server').val();
            const data = (await axios.get('/map/data?server=' + (server ? server : 0))).data;

            if (data && 'players' in data && Array.isArray(data.players)) {
                this.players = data.players;

                if (this.map) {
                    const _this = this;
                    let markers = this.markers;

                    let validIds = [];
                    $.each(this.players, function (_, player) {
                        const id = "player_" + player.character.id,
                            coords = convert(player.coords),
                            heading = _this.mapNumber(-player.heading, -180, 180, 0, 360) - 180,
                            icon = 'vehicle' in player && player.vehicle ? '/images/car.png' : '/images/circle.png',
                            size = 'vehicle' in player && player.vehicle ? [20, 20] : [17, 17],
                            iconObj = new L.Icon(
                                {
                                    iconUrl: icon,
                                    iconSize: size,
                                    iconAnchor: [size[0] / 2, size[1] / 2]
                                }
                            );

                        validIds.push(id);

                        if (id in markers) {
                            markers[id].setLatLng(coords);
                            markers[id].setIcon(iconObj);
                            markers[id].setRotationAngle(heading);
                        } else {
                            let marker = L.marker(coords,
                                {
                                    icon: iconObj,
                                    rotationAngle: heading
                                }
                            );

                            marker.addTo(_this.map);
                            marker.bindPopup(player.character.fullName + ' (#' + player.character.id + ')');

                            markers[id] = marker;
                        }
                    });

                    $.each(markers, function(id, marker) {
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

            setTimeout(function() {
                _this.refreshMap();
            }, 5000);
        },
        async buildMap() {
            if (this.map) {
                return;
            }

            L.Map.addInitHook("addHandler", "gestureHandling", GestureHandling);

            L.TileLayer.GTA = L.TileLayer.extend({
                getTileUrl: function(coords) {
                    coords.x = coords.x < 0 ? 0 : coords.x;
                    coords.y = coords.y < 0 ? 0 : coords.y;

                    switch(coords.z) {
                        case 2:
                            break;
                        case 3:
                            break;
                        case 4:
                            break;
                        case 5:
                            break;
                        case 6:
                            break;
                    }

                    return '/images/tiles/' + coords.z + '_' + coords.x + '_' + coords.y + '.jpg';
                }
            });

            L.tileLayer.gta = function() {
                return new L.TileLayer.GTA();
            }

            this.map = L.map('map', {
                crs: L.CRS.Simple,
                gestureHandling: true,
                minZoom: 2,
                maxZoom: 6,
                maxBounds: L.latLngBounds(L.latLng(-41, 66), L.latLng(-217, 185))
            });

            L.tileLayer.gta().addTo(this.map);

            this.map.setView([-124, 124], 3);

            this.map.on('click', function(e) {
                console.log('map', e.latlng);
            });

            await this.refreshMap();
        }
    },
    mounted() {
        this.buildMap();
    }
};
</script>
