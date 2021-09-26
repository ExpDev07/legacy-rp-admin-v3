class DetectionFilter {
    constructor(type) {
        this.type = type;
    }

    check(player, characters, highlightedPeople) {
        const character = player.character.id in characters ? characters[player.character.id] : null;

        switch (this.type) {
            case 'is_vehicle':
                return !!player.vehicle;
            case 'is_not_vehicle':
                return !player.vehicle;
            case 'is_staff':
                return player.player.isStaff;
            case 'is_not_staff':
                return !player.player.isStaff;
            case 'is_dead':
                return player.character && player.icon.dead;
            case 'is_not_dead':
                return player.character && !player.icon.dead;
            case 'is_invisible':
                return player.invisible.raw;
            case 'is_not_invisible':
                return !player.invisible.raw;
            case 'is_highlighted':
                return player.player.steam in highlightedPeople;
            case 'is_not_highlighted':
                return !(player.player.steam in highlightedPeople);
            case 'is_male':
                return character && character.gender === 0;
            case 'is_female':
                return character && character.gender === 1;
        }

        return true;
    }
}

export default DetectionFilter;
