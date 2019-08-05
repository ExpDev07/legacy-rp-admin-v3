import DS from 'ember-data';

export default DS.Model.extend({

    /**
     * 
     */
    identifier: DS.attr('string'),

    /**
     * 
     */
    name: DS.attr('string'),

    /**
     * A link to an avatar (AKA profile picture).
     */
    avatar: DS.attr('string'),

    /**
     * Player associated with the user on the game-server.
     */
    player: DS.belongsTo('player'),

});
