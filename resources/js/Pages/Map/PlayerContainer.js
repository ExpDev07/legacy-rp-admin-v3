import Player from './Player';
import Notifier from './Notifier';
import {getAFKColor} from './helper';

class PlayerContainer {
    constructor(staffMembers) {
        this.staffMembers = staffMembers;
        this.players = {};
        this.vehicles = {};
        this.activePlayerIDs = [];

        this.invisible = [];
        this.afk = [];
        this.resetStats();

        this.isTrackedPlayerVisible = false;

        this.notifier = new Notifier();
    }

    resetStats() {
        this.stats = {
            police: 0,
            ems: 0,
            staff: 0,
            loaded: 0,
            unloaded: 0,
            total: 0
        };
    }

    updatePlayers(rawData, vue) {
        this.resetStats();

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

        this.notifier.checkPlayers(this, vue);
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

            if (this.players[id].isAFK()) {
                this.afk.push(this.getPlayerListInfo(this.players[id]));
            }
            if (this.players[id].invisible.value) {
                this.invisible.push(this.getPlayerListInfo(this.players[id]));
            }

            this.stats.loaded++;
        } else {
            this.stats.unloaded++;
        }

        if (this.players[id].player.isStaff) {
            this.stats.staff++;
        }
        if (this.players[id].onDuty === 'police') {
            this.stats.police++;
        } else if (this.players[id].onDuty === 'ems') {
            this.stats.ems++;
        }

        this.stats.total++;
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
            afk_title: player.getAFKTitle(),
            invisible: player.invisible.time,
            cid: player.character ? player.character.id : 0,
            source: player.player.source
        };
    }
}

export default PlayerContainer;
