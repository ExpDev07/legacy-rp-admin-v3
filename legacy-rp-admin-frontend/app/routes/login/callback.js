import Route from '@ember/routing/route';
import OAuth2ImplicitGrantCallbackRouteMixin from 'ember-simple-auth/mixins/oauth2-implicit-grant-callback-route-mixin';

export default Route.extend(OAuth2ImplicitGrantCallbackRouteMixin, {

    /**
     * The authenticator to use for the oauth2 implicit grant.
     */
    authenticator: 'authenticator:oauth2',

    activate() {
        // Fix this shit, but it works for now. Honestly can't be bothered.
        this._super(...arguments);
        this.transitionTo('/');
    }

});
