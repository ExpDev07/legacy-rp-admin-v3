import Route from '@ember/routing/route';
import ApplicationRouteMixin from 'ember-simple-auth/mixins/application-route-mixin';
import { inject as service } from '@ember/service';

export default Route.extend(ApplicationRouteMixin, {

    //session: service(),
    
    /**
     * The user that's currently logged in.
     */
    currentUser: service(),

    beforeModel() {
        // Load the current user here so that it becomes available in all routes.
        return this._loadCurrentUser();
    },
  
    /**
     * Called when the session gets authenticated.
     */
    async sessionAuthenticated() {
        await this._loadCurrentUser();
        this._super(...arguments);
    },
  
    /**
     * Loads the currently logged in user. Invalidates the session if the loading fails.
     */
    _loadCurrentUser() {
        return this.currentUser.load().catch(() => this.session.invalidate());
    }

});