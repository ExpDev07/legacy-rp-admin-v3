import DS from 'ember-data';

/**
 * A player model.
 */
export default DS.Model.extend({

    /**
     * 
     */
    identifier: DS.attr('string'),

    /**
     * The name (also referred to as username).
     */
    name: DS.attr('string'),

    /**
     * A link to the players's steam profile.
     */
    steamProfile: DS.attr('string'),

});
