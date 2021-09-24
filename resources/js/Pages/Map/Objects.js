const custom_icons = require("../../data/vehicles.json");
const blip_map = require("../../data/blip-map.json");
const L = require("leaflet");

class Character {
    static fromRaw(rawData) {
        if (!rawData.character) {
            return null;
        }

        let c = new Character();

        c.id = rawData.character.id;
        c.name = rawData.character.fullName;
        c.isDead = rawData.character.dead;
        c.isDriving = rawData.vehicle && rawData.vehicle.driving;

        return c;
    }
}

class Vehicle {
    static fromRaw(rawData) {
        if (!rawData.vehicle) {
            return null;
        }

        let v = new Vehicle();

        v.id = rawData.vehicle.id;
        v.name = rawData.vehicle.name;
        v.model = rawData.vehicle.model + '';

        let type = 'car',
            size = 23;

        $.each(custom_icons, function (typ, cfg) {
            if (cfg.models.includes(v.model)) {
                type = typ;
                size = cfg.size;
            }
        });

        v.icon = {
            type: type,
            size: size
        };

        return v;
    }

    getIcon() {
        if (this.model in blip_map) {
            return new L.Icon(
                {
                    iconUrl: '/images/icons/gta/Blip_' + blip_map[this.model] + '.png',
                    iconSize: [28, 28]
                }
            );
        }

        return new L.Icon(
            {
                iconUrl: '/images/icons/' + this.icon.type + '.png',
                iconSize: [this.icon.size, this.icon.size]
            }
        );
    }
}

module.exports = {
    Character: Character,
    Vehicle: Vehicle
};
