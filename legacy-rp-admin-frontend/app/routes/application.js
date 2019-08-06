import Route from '@ember/routing/route';
import AuthenticatedRouteMixin from 'ember-simple-auth/mixins/authenticated-route-mixin';
import { inject as service } from '@ember/service';

// Keep all routes unless explicitly stated otherwise authenticated for now.
export default Route.extend(AuthenticatedRouteMixin, {

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