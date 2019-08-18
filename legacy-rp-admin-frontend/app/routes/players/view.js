import Route from '@ember/routing/route';

export default Route.extend({

    queryParams: { 
        name: ''
    },

    afterModel(model) {
        console.log(JSON.stringify(model));
        this.set('name', model.name);
    }

});
