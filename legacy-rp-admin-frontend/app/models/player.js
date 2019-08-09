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
     * 
     */
    name: DS.attr('string'),

});
