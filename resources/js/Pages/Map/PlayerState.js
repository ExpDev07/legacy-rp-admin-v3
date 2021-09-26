class PlayerState {
    constructor(isLoaded, invisible) {
        this.loaded = isLoaded;
        this.invisible = invisible;
    }

    static defaultState() {
        return new PlayerState(false, false);
    }
}

export default PlayerState;
