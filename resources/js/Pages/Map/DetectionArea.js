import DetectionFilter from './DetectionFilter';
import {dist} from './helper';
import L from "leaflet";
import Vector3 from "./Vector3";

const convertDistance = d => {
    const a = Vector3.fromGameCoords(0, 0),
        b = Vector3.fromGameCoords(d, d);

    return Math.abs(b.toMap().lat - a.toMap().lat);
};

class DetectionArea {
    constructor(id, location, radius, filters, isPersistent) {
        this.id = id;
        this.location = location;
        this.radius = radius;
        this.filters = filters.map(f => new DetectionFilter(f));
        this.isPersistent = isPersistent;

        this.marker = null;
        this.circle = null;

        this.players = {};
    }

    getMarker(vue) {
        if (!this.marker) {
            const formattedFilters = this.filters.map(f => vue.t('map.area_filters.' + f.type));

            this.marker = L.marker(this.location.toMap(),
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
            this.marker.bindPopup(vue.t('map.area_label', this.id) +
                '<br><span class="italic">' + this.location.x + ' ' + this.location.y + ' (' + this.radius + 'm)</span>' +
                '<br>' + vue.t('map.area_type.title') + ': <span class="italic">' + vue.t('map.area_type.' + (this.isPersistent ? 'persistent' : 'normal')) + '</span>' +
                '<br>' + (formattedFilters.length > 0 ? vue.t('map.area_filter') + ': <span class="italic">' + formattedFilters.join(', ') + '</span>' : vue.t('map.filter_none')), {
                autoPan: false
            });
        }

        return this.marker;
    }

    getCircle() {
        if (!this.circle) {
            this.circle = L.circle(this.location.toMap(), {
                radius: convertDistance(this.radius),
                color: '#FFBF00',
                fillColor: '#FFBF00',
                weight: 2,
                opacity: 0.85,
                fill: true
            });
        }

        return this.circle;
    }

    checkPlayers(players, characters, highlightedPeople) {
        if (!this.isPersistent) {
            this.players = {};
        } else {
            for (const id in this.players) {
                if (!this.players.hasOwnProperty(id)) continue;

                this.players[id].inside = false;
            }
        }

        for (let x = 0; x < players.length; x++) {
            this.checkPlayer(players[x], characters, highlightedPeople);
        }
    }

    checkPlayer(player, characters, highlightedPeople) {
        const coords = player.location.toGame(),
            isInArea = dist(coords, this.location) <= this.radius && this.matchesFilters(player, characters, highlightedPeople);

        if (isInArea) {
            this.players[player.player.steam] = {
                steam: player.player.steam,
                cid: player.character.id,
                source: player.player.source,
                name: player.character.name,
                inside: true
            };
        }
    }

    matchesFilters(player, characters, highlightedPeople) {
        for (let x = 0; x < this.filters.length; x++) {
            if (!this.filters[x].check(player, characters, highlightedPeople)) {
                return false;
            }
        }

        return true;
    }
}

export default DetectionArea;
