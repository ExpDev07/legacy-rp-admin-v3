import EmberRouter from '@ember/routing/router';
import config from './config/environment';

const Router = EmberRouter.extend({
    location: config.locationType,
    rootURL: config.rootURL
});

Router.map(function() {
  this.route('test');

  this.route('login', function() {
    this.route('callback');
  });

  this.route('players', function() {
    this.route('view', { path: '/:player_id' });
    this.route('search');
  });
});

export default Router;
