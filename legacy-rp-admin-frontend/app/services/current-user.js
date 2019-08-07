import Service from '@ember/service';
import RSVP from 'rsvp';
import { inject as service } from '@ember/service';

/**
 * A service to manage the currently logged in user.
 */
export default Service.extend({

    session: service(),
    store:   service(),

    load() {
        // Check if the session is authenticated.
        if (this.session.isAuthenticated) {
            // Query the store for myself and set it when found.
            return this.store.queryRecord('user', { me: true }).then((user) => {
                this.set('user', user);
            });
        } else {
            // Just resolve.........
            return RSVP.resolve();
        }
    }
    
});
