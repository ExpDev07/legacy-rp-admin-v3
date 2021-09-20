import Vector3 from './Vector3';
import {shouldIgnoreInvisible, mapNumber, replaceLast} from './helper';
import {Character, Vehicle} from './Objects';
import L from "leaflet";

const IconSizes = {
    circle: 17,
    circle_yellow: 17,
    skull: 17,
    skull_red: 12,
    circle_red: 12,
    circle_green: 13
};

class Player {
    constructor(rawData, staffMembers, onDutyList) {
        this.update(rawData, staffMembers, onDutyList);
    }

    update(rawData, staffMembers, onDutyList) {
        this.player = {
            name: rawData.name,
            steam: rawData.steamIdentifier,
            source: rawData.source,
            isStaff: staffMembers.includes(rawData.steamIdentifier)
        };

        this.character = Character.fromRaw(rawData);
        this.vehicle = Vehicle.fromRaw(rawData);

        this.location = Vector3.fromGameCoords(rawData.coords.x, rawData.coords.y, rawData.coords.z);
        this.heading = mapNumber(-rawData.heading, -180, 180, 0, 360) - 180;
        this.speed = Math.round(rawData.speed * 2.236936); // Convert to mph

        this.invisible = {
            raw: rawData.invisible,
            time: rawData.invisible_since,
            value: rawData.invisible && !shouldIgnoreInvisible(staffMembers, rawData)
        };

        // Player is afk if they either haven't moved for 45 minutes
        // Or haven't moved for 30 minutes and are inside their apartment
        this.afk = {
            time: rawData.afk,
            apartment: rawData.afk > 30 * 60 && this.location.z < -10,
            normal: rawData.afk > 45 * 60,
            staff: this.player.isStaff
        };

        this.onDuty = 'none';
        if (onDutyList.police.includes(this.player.steam)) {
            this.onDuty = 'police';
        } else if (onDutyList.ems.includes(this.player.steam)) {
            this.onDuty = 'ems';
        }

        this.icon = {
            dead: this.character && this.character.isDead,
            driving: this.character && this.character.isDriving,
            passenger: this.character && !this.character.isDriving && this.vehicle,
            invisible: this.invisible.raw
        };

        this.attributes = [
            this.icon.invisible ? 'invisible' : null,
            this.icon.dead ? 'dead' : null,
            this.player.isStaff ? 'staff' : null,
            this.icon.driving ? 'driving (' + this.vehicle.icon.type + ')' : null,
            this.icon.passenger ? 'passenger' : null,
            !this.icon.passenger && !this.icon.driving ? 'on foot' : null,
            this.onDuty === 'police' ? 'on duty (police)' : null,
            this.onDuty === 'ems' ? 'on duty (ems)' : null,
        ].filter(a => !!a);
    }

    isAFK() {
        return !this.afk.staff && (this.afk.apartment || this.afk.normal);
    }

    getAFKTitle() {
        if (this.afk.apartment) {
            return 'Player has not moved for more than 30 minutes while inside an apartment.';
        } else if (this.afk.normal) {
            return 'Player has not moved for more than 45 minutes.'
        }
        return 'Player is not considered afk.';
    }

    getID() {
        return this.player.steam;
    }

    static getPlayerID(rawData) {
        return rawData.steamIdentifier;
    }

    getTitle(useHtml) {
        const name = this.character ? this.character.name : 'N/A';

        if (useHtml) {
            return name + '<sup>' + this.player.source + '</sup>';
        }
        return name + ' (' + this.player.source + ')';
    }

    getVehicleID() {
        if (this.character && this.vehicle && this.character.isDriving) {
            return this.vehicle.id;
        }
        return null;
    }

    getCharacterID() {
        return this.character ? this.character.id : null;
    }

    isTracked() {
        const track = window.location.hash.substr(1);

        const isTracked = [
            'server_' + this.player.source,
            this.player.steam,
            this.character ? 'player_' + this.character.id : null
        ].includes(track);

        if (isTracked && track !== this.player.steam) {
            window.location.hash = this.player.steam;
        }

        return isTracked;
    }

    getIcon(highlightedPeople) {
        let icon = new L.Icon(
            {
                iconUrl: '/images/icons/circle.png',
                iconSize: [IconSizes.circle, IconSizes.circle]
            }
        );

        if (this.player.steam in highlightedPeople) {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle_yellow.png',
                    iconSize: [IconSizes.circle_yellow, IconSizes.circle_yellow]
                }
            );
        } else if (this.icon.invisible) {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle_green.png',
                    iconSize: [IconSizes.circle_green, IconSizes.circle_green]
                }
            );
        } else if (this.icon.driving) {
            icon = this.vehicle.getIcon();
        } else if (this.icon.passenger && this.icon.dead) {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/skull_red.png',
                    iconSize: [IconSizes.skull_red, IconSizes.skull_red]
                }
            )
        } else if (this.icon.passenger) {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle_red.png',
                    iconSize: [IconSizes.circle_red, IconSizes.circle_red]
                }
            )
        } else if (this.icon.dead) {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/skull.png',
                    iconSize: [IconSizes.skull, IconSizes.skull]
                }
            );
        } else if (this.onDuty === 'police') {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle_police.png',
                    iconSize: [IconSizes.circle, IconSizes.circle]
                }
            );
        } else if (this.onDuty === 'ems') {
            icon = new L.Icon(
                {
                    iconUrl: '/images/icons/circle_ems.png',
                    iconSize: [IconSizes.circle, IconSizes.circle]
                }
            );
        }

        return icon;
    }

    getZIndex(highlightedPeople) {
        if (this.isTracked()) {
            return 200;
        } else if (this.player.steam in highlightedPeople) {
            return 150;
        } else if (this.icon.passenger) {
            return 102;
        } else if (!this.icon.driving) {
            return 101;
        }
        return 100;
    }

    static newMarker() {
        let marker = L.marker({lat: 0, lng: 0}, {});

        marker.bindPopup('', {
            autoPan: false
        });

        return marker;
    }

    updateMarker(marker, highlightedPeople) {
        marker.setIcon(this.getIcon(highlightedPeople));
        marker.setLatLng(this.location.toMap());
        marker.setRotationAngle(this.heading);

        const attributes = this.attributes.map(a => '<span class="text-xxs italic block leading-3">- is ' + a + '</span>');

        const popup = [
            '<a href="/players/' + this.player.steam + '" target="_blank" class="font-bold block border-b border-gray-700 mb-1">' + this.getTitle(true) + '</a>',
            '<span class="block"><b>Altitude:</b> ' + this.location.z + 'm</span>',
            '<span class="block mb-1"><b>Speed:</b> ' + this.speed + 'mph</span>',
            attributes.join('')
        ].join('');

        marker._popup.setContent(popup);

        marker.options.forceZIndex = this.getZIndex(highlightedPeople);

        return marker;
    }
}

export default Player;
