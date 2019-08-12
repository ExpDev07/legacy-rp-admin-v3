import Route from '@ember/routing/route';

export default Route.extend({

    queryParams: { 
        query: ''
    },

    model(params) {
        // Query for any records that matches the provided query.
        return this.store.query('player', { filter: {
            query: params.query
        }});
    }

});
