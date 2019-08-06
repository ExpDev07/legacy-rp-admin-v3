import EmberObject from '@ember/object';
import Oauth2ImplicitGrantCallbackRouteMixinMixin from 'legacy-rp-admin/mixins/oauth2-implicit-grant-callback-route-mixin';
import { module, test } from 'qunit';

module('Unit | Mixin | oauth2-implicit-grant-callback-route-mixin', function() {
  // Replace this with your real tests.
  test('it works', function (assert) {
    let Oauth2ImplicitGrantCallbackRouteMixinObject = EmberObject.extend(Oauth2ImplicitGrantCallbackRouteMixinMixin);
    let subject = Oauth2ImplicitGrantCallbackRouteMixinObject.create();
    assert.ok(subject);
  });
});
