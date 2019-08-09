import Route from '@ember/routing/route';

export default Route.extend({

    /**
     * Required query parameters.
     */
    queryParams: { query: '' },

    model(params) {
        // Create a record and set "recipient"
        return this.store.createRecord('player', { query: params.query });
    }

});
