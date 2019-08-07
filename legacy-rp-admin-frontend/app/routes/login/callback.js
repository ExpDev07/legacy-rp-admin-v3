import Route from '@ember/routing/route';
import ApplicationRouteMixin from 'ember-simple-auth/mixins/application-route-mixin';
import OAuth2ImplicitGrantCallbackRouteMixin from 'ember-simple-auth/mixins/oauth2-implicit-grant-callback-route-mixin';

export default Route.extend(ApplicationRouteMixin, OAuth2ImplicitGrantCallbackRouteMixin, {

    /**
     * The authenticator to use for the oauth2 implicit grant.
     */
    authenticator: 'authenticator:oauth2',

});
