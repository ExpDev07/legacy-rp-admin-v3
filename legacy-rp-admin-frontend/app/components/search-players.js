import Component from '@ember/component';

export default Component.extend({

    actions: {
        search() {
            let query = this.query;
            this.transitionTo('players.index', { queryParams: { query } });
        }
    }

});
