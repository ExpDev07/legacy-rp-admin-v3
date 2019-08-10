import Controller from '@ember/controller';

export default Controller.extend({

    actions: {
        /**
         * Searches for players using the provided query.
         * 
         * @param {string} query The query 
         */
        async search(query) {
            // Query for any records that matches the provided query.
            return await this.store.query('player', { filter: {
                query
            }});
        },

        /**
         * Called when the user selects a player.
         * 
         * @param {*} selected The selected.
         */
        select(selected) {
            // Transition to the route where we can view the selected player.
            this.transitionToRoute('players.view', selected);
        }
    }

});
