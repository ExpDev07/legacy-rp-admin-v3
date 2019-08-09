import DS from 'ember-data';

/**
 * A user model.
 */
export default DS.Model.extend({

    /**
     * The account id. Usually equal to the user's steam 64 id.
     */
    accountId: DS.attr('string'),

    /**
     * A HEX-ified version of "accountId" with a special prefix.
     */
    identifier: DS.attr('string'),

    /**
     * The name (also referred to as username).
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
