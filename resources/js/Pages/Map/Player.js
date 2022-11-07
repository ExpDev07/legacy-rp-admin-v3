import Vector3 from './Vector3';
import {shouldIgnoreInvisible, mapNumber, replaceLast} from './helper';
import {Character, Vehicle} from './Objects';
import L from "leaflet";
import Bounds from "./map.config";

const IconSizes = {
    circle: 17,
    circle_yellow: 17,
    skull: 17,
    skull_red: 12,
    circle_red: 12,
    circle_green: 13
};

class Player {
    constructor(rawData, staffMembers) {
        this.update(rawData, staffMembers);
    }

    static fixData(rawData) {
        const flags = Player.getPlayerFlags(rawData);

        if (flags.identityOverride) {
            rawData.steamIdentifier = rawData.steamIdentifier.replace('steam:1100001', 'steam:1100002');
        }

        return rawData;
    }

    update(rawData, staffMembers) {
        const flags = Player.getPlayerFlags(rawData);

        this.player = {
            name: rawData.name,
            steam: rawData.steamIdentifier,
            source: rawData.source,
            isStaff: !flags.identityOverride && staffMembers.includes(rawData.steamIdentifier),
            isFake: flags.identityOverride
        };

        this.character = Character.fromRaw(rawData);
        this.vehicle = Vehicle.fromRaw(rawData);

        if (this.heading) {
            this.lastHeading = this.heading;
        } else {
            this.lastHeading = null;
        }

        this.location = Vector3.fromGameCoords(rawData.coords.x, rawData.coords.y, rawData.coords.z);
        this.heading = mapNumber(-rawData.heading, -180, 180, 0, 360) - 180;
        this.speed = Math.round(rawData.speed * 2.236936); // Convert to mph

        const invisible = this.character && this.character.invisible;

        this.invisible = {
            raw: invisible,
            value: invisible && !shouldIgnoreInvisible(staffMembers, rawData, this.character)
        };

        // Player is afk if they either haven't moved for 15 minutes
        this.afk = {
            time: rawData.afk,
            value: rawData.afk > 15 * 60,
            staff: this.player.isStaff
        };

        this.onDuty = rawData.duty ? rawData.duty.type : 'none';

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
            this.icon.driving ? 'driving (' + this.vehicle.model + ')' : null,
            this.icon.passenger ? 'passenger' : null,
            !this.icon.passenger && !this.icon.driving ? 'on foot' : null,
            this.onDuty === 'police' ? 'on duty (police)' : null,
            this.onDuty === 'ems' ? 'on duty (ems)' : null,
        ].filter(a => !!a);
    }

    static getPlayerFlags(player) {
        const flags = player.flags ? player.flags : 0;

        return {
            modifiedCameraCoords: !!(flags & 8),
            inMiniGame: !!(flags & 4),
            fakeDisconnected: !!(flags & 2),
            identityOverride: !!(flags & 1)
        }
    }

    isAFK() {
        return !this.afk.staff && this.afk.value;
    }

    getAFKTitle() {
        if (this.afk.value) {
            return 'Player has not moved for more than 15 minutes.'
        }
        return 'Player is not considered afk.';
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
        if (this.character && this.vehicle) {
            return this.vehicle.id;
        }
        return null;
    }

    getCharacterID() {
        return this.character ? this.character.id : null;
    }

    isTracked() {
        const track = window.location.hash.substr(1).toLowerCase();

        const isTracked = [
            'server_' + this.player.source,
            this.player.steam.toLowerCase(),
            this.character ? 'player_' + this.character.id : null,
            this.vehicle && this.vehicle.plate ? 'plate_' + this.vehicle.plate.toLowerCase() : null
        ].includes(track);

        if (isTracked && track !== this.player.steam) {
            window.location.hash = this.player.steam;
        }

        return isTracked;
    }

    getIcon(highlightedPeople) {
        if (Bounds.calibrating) {
            return new L.Icon(
                {
                    iconUrl: '/images/icons/calibrate.png',
                    iconSize: [20, 20]
                }
            );
        }

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

    updateMarker(marker, highlightedPeople, vehicles) {
        const _this = this;

        marker.setIcon(this.getIcon(highlightedPeople));
        marker.setLatLng(this.location.toMap());

        // Reset transition for icon
        if (marker._icon) {
            marker._icon.style.transition = 'inherit';
        }

        // Check if we have a last heading otherwise just set the rotation
        if (this.lastHeading !== null && marker._icon) {
            // Calculate the difference between the last and the new heading
            const headingDiff = this.lastHeading - this.heading;

            // Are we doing a 360?
            if (Math.abs(headingDiff) >= 180) {
                // Calculate how the heading should be relative to the old one and set it
                const newHeading = headingDiff > 0 ? this.heading + 360 : this.heading - 360;
                marker.setRotationAngle(newHeading);

                // Wait for the animation to finish (300ms)
                setTimeout(function () {
                    if (!marker._icon) {
                        return;
                    }

                    // Set the transition to 0s so we dont see a 360
                    marker._icon.style.transition = '0s';

                    // Update the icons rotation with the actual heading while we still have no transition
                    marker._icon.style.transform = marker._icon.style.transform.replace(/(?<=rotateZ\().+?(?=\))/gm, _this.heading + 'deg');
                }, 300);
            } else {
                // We are not doing a 360 so no fancy stuff needed
                marker.setRotationAngle(this.heading);
            }
        } else {
            marker.setRotationAngle(this.heading);
        }

        const attributes = this.attributes.map(a => '<span class="text-xxs italic block leading-3">- is ' + a + '</span>');

        let vehicleInfo = '';
        const vehicle = this.getVehicleID();
        if (vehicle && vehicle in vehicles) {
            const formatInfo = info => '<a href="/players/' + info.steam + '" target="_blank">' + info.name + '</a>';

            vehicleInfo = '<span class="block mt-1 text-xxs leading-3"><b>Driver:</b> ' + (vehicles[vehicle].driver ? formatInfo(vehicles[vehicle].driver) : 'N/A') + '</span>' +
                '<span class="block text-xxs leading-3"><b>Passengers:</b> ' + (
                    vehicles[vehicle].passengers.length > 0 ?
                        vehicles[vehicle].passengers.map(i => formatInfo(i)).join(', ') :
                        'N/A'
                ) + '</span>';
        }

        const popup = [
            '<a href="/players/' + this.player.steam + '" target="_blank" class="font-bold block border-b border-gray-700 mb-1">' + this.getTitle(true) + '</a>',
            '<span class="block"><b>Altitude:</b> ' + this.location.z + 'm</span>',
            '<span class="block mb-1"><b>Speed:</b> ' + this.speed + 'mph</span>',
            attributes.join(''),
            vehicleInfo
        ].join('');

        marker._popup.setContent(popup);

        marker.options.forceZIndex = this.getZIndex(highlightedPeople);

        return marker;
    }
}

export default Player;
