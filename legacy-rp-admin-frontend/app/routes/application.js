import Route from '@ember/routing/route';
import AuthenticatedRouteMixin from 'ember-simple-auth/mixins/authenticated-route-mixin';

// Keep all routes unless explicitly stated otherwise authenticated for now.
export default Route.extend(AuthenticatedRouteMixin);