const ignore_invisible = require("../../data/ignore_invisible.json");
const Rainbow = require("rainbowvis.js");

const rainbow = new Rainbow();
rainbow.setNumberRange(15 * 60, 1.5 * 60 * 60);
rainbow.setSpectrum('#f7ff00', '#ffbf00', '#ff6600', '#ff0000');

module.exports = {
    mapNumber(val, in_min, in_max, out_min, out_max) {
        return (val - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
    },
    shouldIgnoreInvisible(staffMembers, player, character) {
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

        // If you are in a shell (interior)
        if (character && character.inShell) {
            return true;
        }

        // If you are in a trunk
        if (character && character.inTrunk) {
            return true;
        }

        // Check if staff member
        if (staffMembers.includes(player.steamIdentifier)) {
            return true;
        }

        // Check if they are inside an apartment (most of the time that's about -99 below the ground)
        if (player.coords.z < -90) {
            return true;
        }

        // Check if they are inside one of the ignore cylinders
        for (let x = 0; x < ignore_invisible.length; x++) {
            const spot = parseSpot(ignore_invisible[x]);

            if (isInside(spot, player.coords)) {
                return true;
            }
        }

        // Hmm why are they invisible?
        return false;
    },
    getAFKColor(time, isStaff) {
        return isStaff ? 'rgb(16, 185, 129)' : '#' + rainbow.colourAt(time);
    },
    replaceLast(source, what, replacement) {
        if (!source.includes(what)) {
            return source;
        }

        let pcs = source.split(what);
        let lastPc = pcs.pop();
        return pcs.join(what) + replacement + lastPc;
    },
    dist(pointA, pointB) {
        return Math.sqrt((pointA.x - pointB.x) ** 2 + (pointA.y - pointB.y) ** 2);
    }
};
