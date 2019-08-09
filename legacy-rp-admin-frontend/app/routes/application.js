import Route from '@ember/routing/route';
import ApplicationRouteMixin from 'ember-simple-auth/mixins/application-route-mixin';
import { inject as service } from '@ember/service';

/**
 * Application route.
 */
export default Route.extend(ApplicationRouteMixin, {
    
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
        let _super = this._super;
        await this._loadCurrentUser();
        _super.call(this, ...arguments);
    },
  
    /**
     * Loads the currently logged in user. Invalidates the session if the loading fails.
     */
    _loadCurrentUser() {
        return this.currentUser.load().catch(() => this.session.invalidate());
    }

});