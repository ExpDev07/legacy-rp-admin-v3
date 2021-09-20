import {initNotifications, notify} from 'browser-notification';

const StateUnloaded = 0,
    StateLoaded = 1;

class Notifier {
    constructor() {
        this.lastPlayerState = {};
        this.currentPlayerState = {};

        this.playerMap = {};
        this.notifications = {
            onload: {},
            onunload: {}
        };
    }

    init() {
        initNotifications({
            ignoreFocused: true
        });
    }

    notifyOnLoad(steam) {
        this.notifications.onload[steam] = true;
        this.init();
    }

    notifyOnUnload(steam) {
        this.notifications.onunload[steam] = true;
        this.init();
    }

    removeNotifyOnLoad(steam) {
        delete this.notifications.onload[steam];
    }

    removeNotifyOnUnload(steam) {
        delete this.notifications.onunload[steam];
    }

    checkPlayers(container, vue) {
        const _this = this;

        container.eachPlayer(function (id, player) {
            _this.checkPlayer(id, player);
        });

        for (const id in this.lastPlayerState) {
            if (!this.lastPlayerState.hasOwnProperty(id)) continue;

            const last = this.lastPlayerState[id],
                now = id in this.currentPlayerState ? this.currentPlayerState[id] : StateUnloaded;

            if (last === StateLoaded && now === StateUnloaded) {
                this.onUnload(id, this.playerMap[id], vue);
            }
        }

        for (const id in this.currentPlayerState) {
            if (!this.currentPlayerState.hasOwnProperty(id)) continue;
            if (!(id in this.lastPlayerState)) {
                this.lastPlayerState[id] = StateUnloaded;
            }

            const last = this.lastPlayerState[id],
                now = this.currentPlayerState[id];

            if (last === StateUnloaded && now === StateLoaded) {
                this.onLoad(id, this.playerMap[id], vue);
            }
        }


        this.lastPlayerState = this.currentPlayerState;
        this.currentPlayerState = {};


        for (const id in this.notifications.onload) {
            if (!this.notifications.onload.hasOwnProperty(id)) continue;

            if (id in this.playerMap) {
                this.notifications.onload[id] = this.playerMap[id];
            }
        }
        for (const id in this.notifications.onunload) {
            if (!this.notifications.onunload.hasOwnProperty(id)) continue;

            if (id in this.playerMap) {
                this.notifications.onunload[id] = this.playerMap[id];
            }
        }
    }

    checkPlayer(id, player) {
        if (player.character) {
            this.currentPlayerState[id] = StateLoaded;
        } else {
            this.currentPlayerState[id] = StateUnloaded;
        }

        this.playerMap[id] = player.player;
    }

    onUnload(id, player, vue) {
        if (id in this.notifications.onunload) {
            this.playSound('player-left');
            this.notification('Unloaded ' + player.name, player.name + ' (' + player.steam + ') unloaded or left the server.', vue);
        }
    }

    onLoad(id, player, vue) {
        if (id in this.notifications.onload) {
            this.playSound('player-joined');
            this.notification('Loaded ' + player.name, player.name + ' (' + player.steam + ') has loaded into a character.', vue);
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
