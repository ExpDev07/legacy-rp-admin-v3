import Route from '@ember/routing/route';
import UnauthenticatedRouteMixin from 'ember-simple-auth/mixins/unauthenticated-route-mixin';

/**
 * Route to login with steam.
 */
export default Route.extend(UnauthenticatedRouteMixin);