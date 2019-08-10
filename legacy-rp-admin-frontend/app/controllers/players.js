import Controller from '@ember/controller';

export default Controller.extend({

    actions: {
        /**
         * Searches for players using the provided query.
         * 
         * @param {string} query The query 
         */
        search(query) {
            // Query for any records that matches the provided query.
            return this.store.query('player', { filter: {
                query
            }})
            .then((players) => players);
        }
    }

});
