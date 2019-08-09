import Route from '@ember/routing/route';
import UnauthenticatedRouteMixin from 'ember-simple-auth/mixins/unauthenticated-route-mixin';
import OAuth2ImplicitGrantCallbackRouteMixin from 'ember-simple-auth/mixins/oauth2-implicit-grant-callback-route-mixin';

/**
 * Callback for authenticating with oauth2 implicit grant.
 */
export default Route.extend(UnauthenticatedRouteMixin, OAuth2ImplicitGrantCallbackRouteMixin, {

    /**
     * The authenticator to use for the oauth2 implicit grant.
     */
    authenticator: 'authenticator:oauth2',

});
