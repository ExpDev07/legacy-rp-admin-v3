import Route from '@ember/routing/route';
import AuthenticatedRouteMixin from 'ember-simple-auth/mixins/authenticated-route-mixin';

export default Route.extend(AuthenticatedRouteMixin, {

    actions: {
        // run this action every x seconds.
        updateModel() {

            this.store.query('log', {}).then((logs) => this.model = logs);
    
        }
    }

});
