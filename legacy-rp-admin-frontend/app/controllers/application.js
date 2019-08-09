import Controller from '@ember/controller';
import { inject as service } from '@ember/service';

/**
 * The application controller.
 */
export default Controller.extend({

    session:     service(),
    currentUser: service(),

    actions: {
        /**
         * Invalidates the session.
         */
        invalidateSession() {
            this.session.invalidate();
        }
    }

});
