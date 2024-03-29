import Player from './Player';
import Notifier from './Notifier';
import {getAFKColor, getOnDutyClass} from './helper';
import {Character} from './Objects.js';

class PlayerContainer {
    constructor(staffMembers) {
        this.staffMembers = staffMembers;
        this.players = {};
        this.vehicles = {};
        this.activePlayerIDs = [];

        this.invisible = [];
        this.afk = [];
        this.on_duty = {
            pd: [],
            ems: []
        };
        this.staff = [];
        this.resetStats();

		this.unloadedPlayers = [];

		this.mainInstance = 1;

        this.instances = [];

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

    updatePlayers(rawData, vue, selectedInstance, world) {
        this.resetStats();

        this.vehicles = {};
        this.activePlayerIDs = [];

		this.unloadedPlayers = [];

        this.invisible = [];
        this.afk = [];
        this.on_duty = {
            pd: [],
            ems: []
        };
        this.staff = [];

        this.instances = {};

        this.isTrackedPlayerVisible = false;

        for (let x = 0; x < rawData.length; x++) {
            rawData[x] = Player.fixData(rawData[x]);

            this.updatePlayer(rawData[x], selectedInstance);
        }

        this.instances = Object.entries(this.instances).map(entry => {
			return {
				id: parseInt(entry[0]),
				count: entry[1]
			};
		}).sort((a, b) => a.id > b ? 1 : (a.id < b.id ? -1 : 0));

        this.invisible.sort((b, a) => (a.invisible > b.invisible) ? 1 : ((b.invisible > a.invisible) ? -1 : 0));

        this.afk.sort((b, a) => (a.afk > b.afk) ? 1 : ((b.afk > a.afk) ? -1 : 0));

        this.on_duty.pd.sort((a, b) => (a.source > b.source) ? 1 : ((b.source > a.source) ? -1 : 0));
        this.on_duty.ems.sort((a, b) => (a.source > b.source) ? 1 : ((b.source > a.source) ? -1 : 0));

        this.staff.sort((a, b) => (a.source > b.source) ? 1 : ((b.source > a.source) ? -1 : 0));

        this.notifier.checkPlayers(this, vue);

		if (world && world.instance) {
			this.mainInstance = world.instance;
		} else {
			const possibleInstances = this.instances.filter(instance => instance.count > 1);

			this.mainInstance = possibleInstances.length > 0 ? possibleInstances[0].id : 1;
		}

		this.unloadedPlayers.sort((a, b) => {
			return a.source - b.source;
		});
    }

    updatePlayer(rawPlayer, selectedInstance) {
        const id = Player.getPlayerID(rawPlayer);

        const flags = Player.getPlayerFlags(rawPlayer);

        if (flags.fakeDisconnected) {
            return;
        }

		if (rawPlayer.character) {
			this.stats.loaded++;
		} else {
			this.unloadedPlayers.push(rawPlayer);

			this.stats.unloaded++;
		}

		this.stats.total++;

        const characterFlags = Character.getCharacterFlags(rawPlayer.character);
        if (!characterFlags.spawned) {
            return;
        }

        const instance = parseInt(rawPlayer.instance);
        if (rawPlayer.character) {
            this.instances[instance] = (this.instances[instance] || 0) + 1;
        }

        if (instance !== selectedInstance) {
            return;
        }

        if (id in this.players) {
            this.players[id].update(rawPlayer, this.staffMembers);
        } else {
            this.players[id] = new Player(rawPlayer, this.staffMembers);
        }

        const vehicle = this.players[id].getVehicleID();
        if (vehicle) {
            if (!(vehicle in this.vehicles)) {
                this.vehicles[vehicle] = {
                    driver: null,
                    passengers: []
                };
            }

            const info = {
                license: this.players[id].player.license,
                name: this.players[id].character.name
            };

            if (this.players[id].character.isDriving) {
                this.vehicles[vehicle].driver = info;
            } else {
                this.vehicles[vehicle].passengers.push(info);
            }
        }

		if (23 == this.players[id].player.source)
			console.log(this.players[id])

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
        }

        if (this.players[id].player.isStaff) {
            this.stats.staff++;

            this.staff.push(this.getPlayerListInfo(this.players[id]));
        }
        if (this.players[id].onDuty === 'police') {
            this.stats.police++;

            this.on_duty.pd.push(this.getPlayerListInfo(this.players[id]));
        } else if (this.players[id].onDuty === 'medical') {
            this.stats.ems++;

            this.on_duty.ems.push(this.getPlayerListInfo(this.players[id]));
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
            color: player.isAFK() ? getAFKColor(player.afk.time, player.player.isStaff) : '',
            is_staff: player.player.isStaff,
            name: player.character ? player.character.name : 'N/A',
            playerName: player.player.name,
            license: player.player.license,
            afk: player.afk.time,
            afk_title: player.getAFKTitle(),
            invisible: player.invisible.time,
            cid: player.character ? player.character.id : 0,
            source: player.player.source,
            onDuty: player.onDuty
        };
    }
}

export default PlayerContainer;
