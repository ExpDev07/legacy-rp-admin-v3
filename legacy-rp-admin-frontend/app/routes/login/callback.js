import Route from '@ember/routing/route';
import OAuth2ImplicitGrantCallbackRouteMixin from 'ember-simple-auth/mixins/oauth2-implicit-grant-callback-route-mixin';

export default Route.extend(OAuth2ImplicitGrantCallbackRouteMixin, {

    /**
     * The authenticator to use for the oauth2 implicit grant.
     */
    authenticator: 'authenticator:oauth2',

    redirect(model, transition) {
        // Just transition to index when this route is hit.
        this.transitionTo('/');
    },

});
