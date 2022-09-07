import * as pako from "pako";

class DataCompressor {
    static async GUnZIP(data) {
        return pako.ungzip(data, {
            to: 'string'
        });
    }

    static decompressData(data) {
        if ('v' in data && Array.isArray(data.v) && 'p' in data && Array.isArray(data.p)) {
            const decompressor = new DataCompressor();

            return {
                players: decompressor.decompressPlayers(data.p),
                viewers: data.v
            };
        }

        return false;
    }

    decompressPlayers(players) {
        for (let x = 0; x < players.length; x++) {
            players[x] = this.decompress(players[x]);
        }

        return players;
    }

    decompress(player) {
        this.player = player;

        const character = 'b' in this.player ? {
            flags: this.get('a', 0, this.player['b']),
            fullName: this.get('b', '', this.player['b']),
            id: this.get('c', 0, this.player['b'])
        } : false;

        const vehicle = 'i' in this.player ? {
            driving: this.get('a', false, this.player['i']),
            id: this.get('b', 0, this.player['i']),
            model: this.get('c', '', this.player['i']),
            name: this.get('d', '', this.player['i']),
        } : false;

        const duty = 'e' in this.player ? {
            type: this.get('a', false, this.player['e']),
            department: this.get('b', false, this.player['e'])
        } : false;

        const coordsArray = 'c' in this.player ? this.player['c'].split(',') : [],
            coords = coordsArray.length >= 3 ? {
                x: parseFloat(coordsArray[0]),
                y: parseFloat(coordsArray[1]),
                z: parseFloat(coordsArray[2])
            } : {x: 0, y: 0, z: 0};

        return {
            afk: this.get('a', 0),
            character: character,
            coords: coords,
            heading: coordsArray.length >= 4 ? parseFloat(coordsArray[3]) : 0.0,
            flags: this.get('d', 0),
            duty: duty,
            name: this.get('f', ''),
            source: this.get('g', 0),
            speed: coordsArray.length >= 5 ? parseFloat(coordsArray[4]) : 0.0,
            steamIdentifier: this.get('h', ''),
            vehicle: vehicle,
            instance: this.get('j', 0)
        };
    }

    get(key, def, obj) {
        if (!obj) {
            obj = this.player;
        }

        if (obj && key in obj) {
            return obj[key];
        }
        return def;
    }
}

export default DataCompressor;
