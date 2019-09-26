'use strict';

define("legacy-rp-admin/tests/helpers/ember-power-select", ["exports", "ember-power-select/test-support/helpers"], function (_exports, _helpers) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = deprecatedRegisterHelpers;
  _exports.selectChoose = _exports.touchTrigger = _exports.nativeTouch = _exports.clickTrigger = _exports.typeInSearch = _exports.triggerKeydown = _exports.nativeMouseUp = _exports.nativeMouseDown = _exports.findContains = void 0;

  function deprecateHelper(fn, name) {
    return function (...args) {
      (true && !(false) && Ember.deprecate("DEPRECATED `import { ".concat(name, " } from '../../tests/helpers/ember-power-select';` is deprecated. Please, replace it with `import { ").concat(name, " } from 'ember-power-select/test-support/helpers';`"), false, {
        until: '1.11.0',
        id: "ember-power-select-test-support-".concat(name)
      }));
      return fn(...args);
    };
  }

  let findContains = deprecateHelper(_helpers.findContains, 'findContains');
  _exports.findContains = findContains;
  let nativeMouseDown = deprecateHelper(_helpers.nativeMouseDown, 'nativeMouseDown');
  _exports.nativeMouseDown = nativeMouseDown;
  let nativeMouseUp = deprecateHelper(_helpers.nativeMouseUp, 'nativeMouseUp');
  _exports.nativeMouseUp = nativeMouseUp;
  let triggerKeydown = deprecateHelper(_helpers.triggerKeydown, 'triggerKeydown');
  _exports.triggerKeydown = triggerKeydown;
  let typeInSearch = deprecateHelper(_helpers.typeInSearch, 'typeInSearch');
  _exports.typeInSearch = typeInSearch;
  let clickTrigger = deprecateHelper(_helpers.clickTrigger, 'clickTrigger');
  _exports.clickTrigger = clickTrigger;
  let nativeTouch = deprecateHelper(_helpers.nativeTouch, 'nativeTouch');
  _exports.nativeTouch = nativeTouch;
  let touchTrigger = deprecateHelper(_helpers.touchTrigger, 'touchTrigger');
  _exports.touchTrigger = touchTrigger;
  let selectChoose = deprecateHelper(_helpers.selectChoose, 'selectChoose');
  _exports.selectChoose = selectChoose;

  function deprecatedRegisterHelpers() {
    (true && !(false) && Ember.deprecate("DEPRECATED `import registerPowerSelectHelpers from '../../tests/helpers/ember-power-select';` is deprecated. Please, replace it with `import registerPowerSelectHelpers from 'ember-power-select/test-support/helpers';`", false, {
      until: '1.11.0',
      id: 'ember-power-select-test-support-register-helpers'
    }));
    return (0, _helpers.default)();
  }
});
define("legacy-rp-admin/tests/helpers/ember-simple-auth", ["exports", "ember-simple-auth/authenticators/test"], function (_exports, _test) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.authenticateSession = authenticateSession;
  _exports.currentSession = currentSession;
  _exports.invalidateSession = invalidateSession;
  const TEST_CONTAINER_KEY = 'authenticator:test';

  function ensureAuthenticator(app, container) {
    const authenticator = container.lookup(TEST_CONTAINER_KEY);

    if (!authenticator) {
      app.register(TEST_CONTAINER_KEY, _test.default);
    }
  }

  function authenticateSession(app, sessionData) {
    const {
      __container__: container
    } = app;
    const session = container.lookup('service:session');
    ensureAuthenticator(app, container);
    session.authenticate(TEST_CONTAINER_KEY, sessionData);
    return app.testHelpers.wait();
  }

  function currentSession(app) {
    return app.__container__.lookup('service:session');
  }

  function invalidateSession(app) {
    const session = app.__container__.lookup('service:session');

    if (session.get('isAuthenticated')) {
      session.invalidate();
    }

    return app.testHelpers.wait();
  }
});
define("legacy-rp-admin/tests/integration/components/search-players-test", ["qunit", "ember-qunit", "@ember/test-helpers"], function (_qunit, _emberQunit, _testHelpers) {
  "use strict";

  (0, _qunit.module)('Integration | Component | search-players', function (hooks) {
    (0, _emberQunit.setupRenderingTest)(hooks);
    (0, _qunit.test)('it renders', async function (assert) {
      // Set any properties with this.set('myProperty', 'value');
      // Handle any actions with this.set('myAction', function(val) { ... });
      await (0, _testHelpers.render)(Ember.HTMLBars.template({
        "id": "FbCKTRZG",
        "block": "{\"symbols\":[],\"statements\":[[1,[21,\"search-players\"],false]],\"hasEval\":false}",
        "meta": {}
      }));
      assert.equal(this.element.textContent.trim(), ''); // Template block usage:

      await (0, _testHelpers.render)(Ember.HTMLBars.template({
        "id": "U63QtZ/n",
        "block": "{\"symbols\":[],\"statements\":[[0,\"\\n\"],[4,\"search-players\",null,null,{\"statements\":[[0,\"        template block text\\n\"]],\"parameters\":[]},null],[0,\"    \"]],\"hasEval\":false}",
        "meta": {}
      }));
      assert.equal(this.element.textContent.trim(), 'template block text');
    });
  });
});
define("legacy-rp-admin/tests/lint/app.lint-test", [], function () {
  "use strict";

  QUnit.module('ESLint | app');
  QUnit.test('adapters/application.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'adapters/application.js should pass ESLint\n\n');
  });
  QUnit.test('app.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'app.js should pass ESLint\n\n');
  });
  QUnit.test('authenticators/oauth2.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'authenticators/oauth2.js should pass ESLint\n\n');
  });
  QUnit.test('controllers/application.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'controllers/application.js should pass ESLint\n\n');
  });
  QUnit.test('controllers/index.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'controllers/index.js should pass ESLint\n\n');
  });
  QUnit.test('controllers/players.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'controllers/players.js should pass ESLint\n\n');
  });
  QUnit.test('controllers/players/index.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'controllers/players/index.js should pass ESLint\n\n');
  });
  QUnit.test('models/player.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'models/player.js should pass ESLint\n\n');
  });
  QUnit.test('models/user.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'models/user.js should pass ESLint\n\n');
  });
  QUnit.test('resolver.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'resolver.js should pass ESLint\n\n');
  });
  QUnit.test('router.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'router.js should pass ESLint\n\n');
  });
  QUnit.test('routes/application.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/application.js should pass ESLint\n\n');
  });
  QUnit.test('routes/index.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/index.js should pass ESLint\n\n');
  });
  QUnit.test('routes/login/callback.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/login/callback.js should pass ESLint\n\n');
  });
  QUnit.test('routes/login/index.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/login/index.js should pass ESLint\n\n');
  });
  QUnit.test('routes/players.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/players.js should pass ESLint\n\n');
  });
  QUnit.test('routes/players/index.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/players/index.js should pass ESLint\n\n');
  });
  QUnit.test('routes/players/search.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/players/search.js should pass ESLint\n\n');
  });
  QUnit.test('routes/players/view.js', function (assert) {
    assert.expect(1);
    assert.ok(false, 'routes/players/view.js should pass ESLint\n\n7:9 - Unexpected console statement. (no-console)');
  });
  QUnit.test('routes/players/view/logs.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/players/view/logs.js should pass ESLint\n\n');
  });
  QUnit.test('routes/players/view/warnings.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/players/view/warnings.js should pass ESLint\n\n');
  });
  QUnit.test('routes/test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'routes/test.js should pass ESLint\n\n');
  });
  QUnit.test('serializers/application.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'serializers/application.js should pass ESLint\n\n');
  });
  QUnit.test('services/current-user.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'services/current-user.js should pass ESLint\n\n');
  });
});
define("legacy-rp-admin/tests/lint/templates.template.lint-test", [], function () {
  "use strict";

  QUnit.module('TemplateLint');
  QUnit.test('legacy-rp-admin/templates/application.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/application.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/index.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/index.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/login/index.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/login/index.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/players.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/players.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/players/index.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/players/index.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/players/search.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/players/search.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/players/view.hbs', function (assert) {
    assert.expect(1);
    assert.ok(false, 'legacy-rp-admin/templates/players/view.hbs should pass TemplateLint.\n\nlegacy-rp-admin/templates/players/view.hbs\n  10:21  error  links with target="_blank" must have rel="noopener"  link-rel-noopener\n');
  });
  QUnit.test('legacy-rp-admin/templates/players/view/logs.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/players/view/logs.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/players/view/warnings.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/players/view/warnings.hbs should pass TemplateLint.\n\n');
  });
  QUnit.test('legacy-rp-admin/templates/test.hbs', function (assert) {
    assert.expect(1);
    assert.ok(true, 'legacy-rp-admin/templates/test.hbs should pass TemplateLint.\n\n');
  });
});
define("legacy-rp-admin/tests/lint/tests.lint-test", [], function () {
  "use strict";

  QUnit.module('ESLint | tests');
  QUnit.test('integration/components/search-players-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'integration/components/search-players-test.js should pass ESLint\n\n');
  });
  QUnit.test('test-helper.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'test-helper.js should pass ESLint\n\n');
  });
  QUnit.test('unit/adapters/application-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/adapters/application-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/controllers/application-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/controllers/application-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/controllers/index-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/controllers/index-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/controllers/players/application-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/controllers/players/application-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/controllers/players/index-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/controllers/players/index-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/mixins/application-route-mixin-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/mixins/application-route-mixin-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/models/player-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/models/player-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/models/user-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/models/user-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/index-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/index-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/login-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/login-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/login/callback-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/login/callback-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/players/application-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/players/application-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/players/index-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/players/index-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/players/search-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/players/search-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/players/view-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/players/view-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/players/view/logs-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/players/view/logs-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/players/view/warnings-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/players/view/warnings-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/routes/test-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/routes/test-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/serializers/application-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/serializers/application-test.js should pass ESLint\n\n');
  });
  QUnit.test('unit/services/current-user-test.js', function (assert) {
    assert.expect(1);
    assert.ok(true, 'unit/services/current-user-test.js should pass ESLint\n\n');
  });
});
define("legacy-rp-admin/tests/test-helper", ["legacy-rp-admin/app", "legacy-rp-admin/config/environment", "@ember/test-helpers", "ember-qunit"], function (_app, _environment, _testHelpers, _emberQunit) {
  "use strict";

  (0, _testHelpers.setApplication)(_app.default.create(_environment.default.APP));
  (0, _emberQunit.start)();
});
define("legacy-rp-admin/tests/unit/adapters/application-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Adapter | application', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let adapter = this.owner.lookup('adapter:application');
      assert.ok(adapter);
    });
  });
});
define("legacy-rp-admin/tests/unit/controllers/application-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Controller | application', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let controller = this.owner.lookup('controller:application');
      assert.ok(controller);
    });
  });
});
define("legacy-rp-admin/tests/unit/controllers/index-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Controller | index', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let controller = this.owner.lookup('controller:index');
      assert.ok(controller);
    });
  });
});
define("legacy-rp-admin/tests/unit/controllers/players/application-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Controller | players/application', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let controller = this.owner.lookup('controller:players/application');
      assert.ok(controller);
    });
  });
});
define("legacy-rp-admin/tests/unit/controllers/players/index-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Controller | players/index', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let controller = this.owner.lookup('controller:players/index');
      assert.ok(controller);
    });
  });
});
define("legacy-rp-admin/tests/unit/mixins/application-route-mixin-test", ["legacy-rp-admin/mixins/application-route-mixin", "qunit"], function (_applicationRouteMixin, _qunit) {
  "use strict";

  (0, _qunit.module)('Unit | Mixin | ApplicationRouteMixin', function () {
    // Replace this with your real tests.
    (0, _qunit.test)('it works', function (assert) {
      let ApplicationRouteMixinObject = Ember.Object.extend(_applicationRouteMixin.default);
      let subject = ApplicationRouteMixinObject.create();
      assert.ok(subject);
    });
  });
});
define("legacy-rp-admin/tests/unit/models/player-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Model | player', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let store = this.owner.lookup('service:store');
      let model = store.createRecord('player', {});
      assert.ok(model);
    });
  });
});
define("legacy-rp-admin/tests/unit/models/user-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Model | user', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let store = this.owner.lookup('service:store');
      let model = store.createRecord('user', {});
      assert.ok(model);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/index-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | index', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:index');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/login-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | login', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:login');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/login/callback-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | login/callback', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:login/callback');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/players/application-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | players/application', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:players/application');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/players/index-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | players/index', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:players/index');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/players/search-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | players/search', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:players/search');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/players/view-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | players/view', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:players/view');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/players/view/logs-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | players/view/logs', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:players/view/logs');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/players/view/warnings-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | players/view/warnings', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:players/view/warnings');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/routes/test-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Route | test', function (hooks) {
    (0, _emberQunit.setupTest)(hooks);
    (0, _qunit.test)('it exists', function (assert) {
      let route = this.owner.lookup('route:test');
      assert.ok(route);
    });
  });
});
define("legacy-rp-admin/tests/unit/serializers/application-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Serializer | application', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let store = this.owner.lookup('service:store');
      let serializer = store.serializerFor('application');
      assert.ok(serializer);
    });
    (0, _qunit.test)('it serializes records', function (assert) {
      let store = this.owner.lookup('service:store');
      let record = store.createRecord('application', {});
      let serializedRecord = record.serialize();
      assert.ok(serializedRecord);
    });
  });
});
define("legacy-rp-admin/tests/unit/services/current-user-test", ["qunit", "ember-qunit"], function (_qunit, _emberQunit) {
  "use strict";

  (0, _qunit.module)('Unit | Service | current-user', function (hooks) {
    (0, _emberQunit.setupTest)(hooks); // Replace this with your real tests.

    (0, _qunit.test)('it exists', function (assert) {
      let service = this.owner.lookup('service:current-user');
      assert.ok(service);
    });
  });
});
define('legacy-rp-admin/config/environment', [], function() {
  var prefix = 'legacy-rp-admin';
try {
  var metaName = prefix + '/config/environment';
  var rawConfig = document.querySelector('meta[name="' + metaName + '"]').getAttribute('content');
  var config = JSON.parse(unescape(rawConfig));

  var exports = { 'default': config };

  Object.defineProperty(exports, '__esModule', { value: true });

  return exports;
}
catch(err) {
  throw new Error('Could not read config from meta tag with name "' + metaName + '".');
}

});

require('legacy-rp-admin/tests/test-helper');
EmberENV.TESTS_FILE_LOADED = true;
//# sourceMappingURL=tests.map
