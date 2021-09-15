import Player from './Player';
import {getAFKColor} from './helper';

class PlayerContainer {
    constructor(staffMembers) {
        this.staffMembers = staffMembers;
        this.players = {};
        this.vehicles = {};
        this.activePlayerIDs = [];

        this.invisible = [];
        this.afk = [];

        this.isTrackedPlayerVisible = false;
    }

    updatePlayers(rawData) {
        this.vehicles = {};
        this.activePlayerIDs = [];

        this.invisible = [];
        this.afk = [];

        this.isTrackedPlayerVisible = false;

        for (let x = 0; x < rawData.players.length; x++) {
            this.updatePlayer(rawData.players[x], rawData.on_duty);
        }

        this.invisible.sort((b, a) => (a.invisible > b.invisible) ? 1 : ((b.invisible > a.invisible) ? -1 : 0));
        this.afk.sort((b, a) => (a.afk > b.afk) ? 1 : ((b.afk > a.afk) ? -1 : 0));
    }

    updatePlayer(rawPlayer, onDutyList) {
        const id = Player.getPlayerID(rawPlayer);

        if (!onDutyList.police) {
            onDutyList.police = [];
        }
        if (!onDutyList.ems) {
            onDutyList.ems = [];
        }

        if (id in this.players) {
            this.players[id].update(rawPlayer, this.staffMembers, onDutyList);
        } else {
            this.players[id] = new Player(rawPlayer, this.staffMembers, onDutyList);
        }

        const vehicle = this.players[id].getVehicleID();
        if (vehicle) {
            this.vehicles[vehicle] = this.players[id].character.name;
        }

        if (this.players[id].character) {
            this.activePlayerIDs.push(id);

            if (this.players[id].isTracked()) {
                this.isTrackedPlayerVisible = true;
            }

            if (this.players[id].afk.value) {
                this.afk.push(this.getPlayerListInfo(this.players[id]));
            }
            if (this.players[id].invisible.value) {
                this.invisible.push(this.getPlayerListInfo(this.players[id]));
            }
        }
    }

    isActive(id) {
        return this.activePlayerIDs.includes(id);
    }

    get(id) {
        return id in this.players ? this.players[id] : null;
    }

    remove(id) {
        delete this.players[id];
    }

    eachPlayer(callback) {
        for (const id in this.players) {
            if (!this.players.hasOwnProperty(id)) continue;

            callback(id, this.players[id]);
        }
    }

    getPlayerListInfo(player) {
        return {
            color: getAFKColor(player.afk.time, player.player.isStaff),
            is_staff: player.player.isStaff,
            name: player.character ? player.character.name : 'N/A',
            steam: player.player.steam,
            afk: player.afk.time,
            invisible: player.invisible.time,
            cid: player.character ? player.character.id : 0,
            source: player.player.source
        };
    }
}

export default PlayerContainer;
