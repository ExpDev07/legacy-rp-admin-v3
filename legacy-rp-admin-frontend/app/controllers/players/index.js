import Controller from '@ember/controller';

export default Controller.extend({

    actions: {
        search() {
            this.set('query', this.search);
        }
    }

});
