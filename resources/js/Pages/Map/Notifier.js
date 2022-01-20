import {initNotifications, notify} from 'browser-notification';
import PlayerState from './PlayerState';

class Notifier {
    constructor() {
        this.lastPlayerState = {};
        this.currentPlayerState = {};

        this.playerMap = {};
        this.notifications = {
            load: {},
            unload: {},
            invisible: {}
        };
    }

    isEmpty() {
        return Object.entries(this.notifications).map(a => Object.keys(a[1]).length).reduce((part, a) => part + a, 0) === 0;
    }

    init() {
        initNotifications({
            ignoreFocused: true
        });
    }

    on(target, steam) {
        if (target in this.notifications) {
            this.notifications[target][steam] = true;
        }
    }

    removeNotify(target, steam) {
        if (target in this.notifications) {
            delete this.notifications[target][steam];
        }
    }

    checkPlayers(container, vue) {
        const _this = this;

        // Set current states
        container.eachPlayer(function (id, player) {
            _this.checkPlayer(id, player);
        });

        // Set default states for new players and check for state changes
        for (const id in this.currentPlayerState) {
            if (!this.currentPlayerState.hasOwnProperty(id)) continue;

            const lastState = id in this.lastPlayerState ? this.lastPlayerState[id] : PlayerState.defaultState(),
                currentState = this.currentPlayerState[id];

            // Check if connection state changed
            if (lastState.loaded && !currentState.loaded) {
                this.trigger('unload', id, this.playerMap[id], vue);
            } else if (!lastState.loaded && currentState.loaded) {
                this.trigger('load', id, this.playerMap[id], vue);
            }

            // Check if player just went invisible
            if (!lastState.invisible && currentState.invisible) {
                this.trigger('invisible', id, this.playerMap[id], vue);
            }
        }

        // Advance states
        this.lastPlayerState = this.currentPlayerState;
        this.currentPlayerState = {};

        // Update Player map
        for (const target in this.notifications) {
            if (!this.notifications.hasOwnProperty(target)) continue;

            for (const id in this.notifications[target]) {
                if (!this.notifications[target].hasOwnProperty(id)) continue;

                if (id in this.playerMap) {
                    this.notifications[target][id] = this.playerMap[id];
                }
            }
        }
    }

    checkPlayer(id, player) {
        this.currentPlayerState[id] = new PlayerState(
            !!player.character,
            player.invisible.value
        );
        this.playerMap[id] = player.player;
    }

    trigger(target, id, player, vue) {
        if (target in this.notifications) {
            const hasGlob = Object.keys(this.notifications[target]).some(entry => entry === '*');

            if (id in this.notifications[target] || hasGlob) {
                switch (target) {
                    case 'load':
                        this.playSound('player-joined');
                        this.notification('Loaded ' + player.name, player.name + ' (' + player.steam + ') has loaded into a character.', vue);
                        break;
                    case 'unload':
                        this.playSound('player-left');
                        this.notification('Unloaded ' + player.name, player.name + ' (' + player.steam + ') unloaded or left the server.', vue);
                        break;
                    case 'invisible':
                        this.playSound('player-invisible');
                        this.notification(player.name + ' just went invisible', player.name + ' (' + player.steam + ') just went invisible.', vue);
                        break;
                }
            }
        }
    }

    playSound(sound) {
        const audio = new Audio('/images/sounds/' + sound + '.mp3');
        audio.play()
            .then(() => console.log('Played ' + sound + '.mp3'))
            .catch((e) => {
                console.error('Failed to play sound ' + sound + '.mp3');
                console.error(e);
            });
    }

    notification(title, body, vue) {
        notify(title, {body: body});

        vue.$toast.info(title, {
            position: "bottom-right",
            timeout: 10000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: "button",
            icon: true,
            rtl: false
        });
    }
}

export default Notifier;
