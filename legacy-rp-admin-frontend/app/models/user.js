import DS from 'ember-data';

/**
 * A user model.
 */
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
     * A link to the user's steam profile.
     */
    steamProfile: DS.attr('string'),

    /**
     * Player associated with the user on the game-server.
     */
    player: DS.belongsTo('player'),

});
