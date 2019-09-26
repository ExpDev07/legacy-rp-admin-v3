'use strict';



;define("legacy-rp-admin/adapters/application", ["exports", "ember-data", "ember-simple-auth/mixins/data-adapter-mixin"], function (_exports, _emberData, _dataAdapterMixin) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * The application adapter.
   */
  var _default = _emberData.default.JSONAPIAdapter.extend(_dataAdapterMixin.default, {
    // Define the namespace used for API requests.
    namespace: 'api/v1',

    /**
     * Authorizes the outgoing request by adding an "Authorization" header.
     * 
     * @param {*} xhr The request.
     */
    authorize(xhr) {
      let {
        access_token
      } = this.session.data.authenticated;

      if (Ember.isPresent(access_token)) {
        xhr.setRequestHeader('Authorization', "Bearer ".concat(access_token));
      }
    }

  });

  _exports.default = _default;
});
;define("legacy-rp-admin/app", ["exports", "legacy-rp-admin/resolver", "ember-load-initializers", "legacy-rp-admin/config/environment"], function (_exports, _resolver, _emberLoadInitializers, _environment) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  const App = Ember.Application.extend({
    modulePrefix: _environment.default.modulePrefix,
    podModulePrefix: _environment.default.podModulePrefix,
    Resolver: _resolver.default
  });
  (0, _emberLoadInitializers.default)(App, _environment.default.modulePrefix);
  var _default = App;
  _exports.default = _default;
});
;define("legacy-rp-admin/authenticators/oauth2", ["exports", "ember-simple-auth/authenticators/oauth2-implicit-grant"], function (_exports, _oauth2ImplicitGrant) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = _oauth2ImplicitGrant.default.extend();

  _exports.default = _default;
});
;define("legacy-rp-admin/components/basic-dropdown", ["exports", "ember-basic-dropdown/components/basic-dropdown"], function (_exports, _basicDropdown) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _basicDropdown.default;
    }
  });
});
;define("legacy-rp-admin/components/basic-dropdown/content-element", ["exports", "ember-basic-dropdown/components/basic-dropdown/content-element"], function (_exports, _contentElement) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _contentElement.default;
    }
  });
});
;define("legacy-rp-admin/components/basic-dropdown/content", ["exports", "ember-basic-dropdown/components/basic-dropdown/content"], function (_exports, _content) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _content.default;
    }
  });
});
;define("legacy-rp-admin/components/basic-dropdown/trigger", ["exports", "ember-basic-dropdown/components/basic-dropdown/trigger"], function (_exports, _trigger) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _trigger.default;
    }
  });
});
;define("legacy-rp-admin/components/fa-icon", ["exports", "@fortawesome/ember-fontawesome/components/fa-icon"], function (_exports, _faIcon) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _faIcon.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select-multiple", ["exports", "ember-power-select/components/power-select-multiple"], function (_exports, _powerSelectMultiple) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _powerSelectMultiple.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select-multiple/trigger", ["exports", "ember-power-select/components/power-select-multiple/trigger"], function (_exports, _trigger) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _trigger.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select", ["exports", "ember-power-select/components/power-select"], function (_exports, _powerSelect) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _powerSelect.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select/before-options", ["exports", "ember-power-select/components/power-select/before-options"], function (_exports, _beforeOptions) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _beforeOptions.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select/options", ["exports", "ember-power-select/components/power-select/options"], function (_exports, _options) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _options.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select/placeholder", ["exports", "ember-power-select/components/power-select/placeholder"], function (_exports, _placeholder) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _placeholder.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select/power-select-group", ["exports", "ember-power-select/components/power-select/power-select-group"], function (_exports, _powerSelectGroup) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _powerSelectGroup.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select/search-message", ["exports", "ember-power-select/components/power-select/search-message"], function (_exports, _searchMessage) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _searchMessage.default;
    }
  });
});
;define("legacy-rp-admin/components/power-select/trigger", ["exports", "ember-power-select/components/power-select/trigger"], function (_exports, _trigger) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _trigger.default;
    }
  });
});
;define("legacy-rp-admin/components/welcome-page", ["exports", "ember-welcome-page/components/welcome-page"], function (_exports, _welcomePage) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _welcomePage.default;
    }
  });
});
;define("legacy-rp-admin/controllers/application", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * The application controller.
   */
  var _default = Ember.Controller.extend({
    session: Ember.inject.service(),
    currentUser: Ember.inject.service(),
    actions: {
      /**
       * Invalidates the current session.
       */
      invalidateSession() {
        this.session.invalidate();
      }

    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/controllers/index", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Controller.extend({
    currentUser: Ember.inject.service()
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/controllers/players", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Controller.extend({
    actions: {
      /**
       * Searches for players using the provided query.
       * 
       * @param {string} query The query 
       */
      async search(query) {
        // Query for any records that matches the provided query.
        return await this.store.query('player', {
          filter: {
            query
          }
        });
      },

      /**
       * Called when the user selects a player.
       * 
       * @param {*} selected The selected.
       */
      select(selected) {
        // Transition to the route where we can view the selected player.
        this.transitionToRoute('players.view', selected);
      }

    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/controllers/players/index", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Controller.extend({
    actions: {
      search() {
        this.set('query', this.search);
      }

    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/helpers/and", ["exports", "ember-truth-helpers/helpers/and"], function (_exports, _and) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _and.default;
    }
  });
  Object.defineProperty(_exports, "and", {
    enumerable: true,
    get: function () {
      return _and.and;
    }
  });
});
;define("legacy-rp-admin/helpers/app-version", ["exports", "legacy-rp-admin/config/environment", "ember-cli-app-version/utils/regexp"], function (_exports, _environment, _regexp) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.appVersion = appVersion;
  _exports.default = void 0;

  function appVersion(_, hash = {}) {
    const version = _environment.default.APP.version; // e.g. 1.0.0-alpha.1+4jds75hf
    // Allow use of 'hideSha' and 'hideVersion' For backwards compatibility

    let versionOnly = hash.versionOnly || hash.hideSha;
    let shaOnly = hash.shaOnly || hash.hideVersion;
    let match = null;

    if (versionOnly) {
      if (hash.showExtended) {
        match = version.match(_regexp.versionExtendedRegExp); // 1.0.0-alpha.1
      } // Fallback to just version


      if (!match) {
        match = version.match(_regexp.versionRegExp); // 1.0.0
      }
    }

    if (shaOnly) {
      match = version.match(_regexp.shaRegExp); // 4jds75hf
    }

    return match ? match[0] : version;
  }

  var _default = Ember.Helper.helper(appVersion);

  _exports.default = _default;
});
;define("legacy-rp-admin/helpers/cancel-all", ["exports", "ember-concurrency/helpers/cancel-all"], function (_exports, _cancelAll) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _cancelAll.default;
    }
  });
});
;define("legacy-rp-admin/helpers/ember-power-select-is-group", ["exports", "ember-power-select/helpers/ember-power-select-is-group"], function (_exports, _emberPowerSelectIsGroup) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _emberPowerSelectIsGroup.default;
    }
  });
  Object.defineProperty(_exports, "emberPowerSelectIsGroup", {
    enumerable: true,
    get: function () {
      return _emberPowerSelectIsGroup.emberPowerSelectIsGroup;
    }
  });
});
;define("legacy-rp-admin/helpers/ember-power-select-is-selected", ["exports", "ember-power-select/helpers/ember-power-select-is-selected"], function (_exports, _emberPowerSelectIsSelected) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _emberPowerSelectIsSelected.default;
    }
  });
  Object.defineProperty(_exports, "emberPowerSelectIsSelected", {
    enumerable: true,
    get: function () {
      return _emberPowerSelectIsSelected.emberPowerSelectIsSelected;
    }
  });
});
;define("legacy-rp-admin/helpers/ember-power-select-true-string-if-present", ["exports", "ember-power-select/helpers/ember-power-select-true-string-if-present"], function (_exports, _emberPowerSelectTrueStringIfPresent) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _emberPowerSelectTrueStringIfPresent.default;
    }
  });
  Object.defineProperty(_exports, "emberPowerSelectTrueStringIfPresent", {
    enumerable: true,
    get: function () {
      return _emberPowerSelectTrueStringIfPresent.emberPowerSelectTrueStringIfPresent;
    }
  });
});
;define("legacy-rp-admin/helpers/eq", ["exports", "ember-truth-helpers/helpers/equal"], function (_exports, _equal) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _equal.default;
    }
  });
  Object.defineProperty(_exports, "equal", {
    enumerable: true,
    get: function () {
      return _equal.equal;
    }
  });
});
;define("legacy-rp-admin/helpers/gt", ["exports", "ember-truth-helpers/helpers/gt"], function (_exports, _gt) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _gt.default;
    }
  });
  Object.defineProperty(_exports, "gt", {
    enumerable: true,
    get: function () {
      return _gt.gt;
    }
  });
});
;define("legacy-rp-admin/helpers/gte", ["exports", "ember-truth-helpers/helpers/gte"], function (_exports, _gte) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _gte.default;
    }
  });
  Object.defineProperty(_exports, "gte", {
    enumerable: true,
    get: function () {
      return _gte.gte;
    }
  });
});
;define("legacy-rp-admin/helpers/is-array", ["exports", "ember-truth-helpers/helpers/is-array"], function (_exports, _isArray) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _isArray.default;
    }
  });
  Object.defineProperty(_exports, "isArray", {
    enumerable: true,
    get: function () {
      return _isArray.isArray;
    }
  });
});
;define("legacy-rp-admin/helpers/is-empty", ["exports", "ember-truth-helpers/helpers/is-empty"], function (_exports, _isEmpty) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _isEmpty.default;
    }
  });
});
;define("legacy-rp-admin/helpers/is-equal", ["exports", "ember-truth-helpers/helpers/is-equal"], function (_exports, _isEqual) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _isEqual.default;
    }
  });
  Object.defineProperty(_exports, "isEqual", {
    enumerable: true,
    get: function () {
      return _isEqual.isEqual;
    }
  });
});
;define("legacy-rp-admin/helpers/loc", ["exports", "@ember/string/helpers/loc"], function (_exports, _loc) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _loc.default;
    }
  });
  Object.defineProperty(_exports, "loc", {
    enumerable: true,
    get: function () {
      return _loc.loc;
    }
  });
});
;define("legacy-rp-admin/helpers/lt", ["exports", "ember-truth-helpers/helpers/lt"], function (_exports, _lt) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _lt.default;
    }
  });
  Object.defineProperty(_exports, "lt", {
    enumerable: true,
    get: function () {
      return _lt.lt;
    }
  });
});
;define("legacy-rp-admin/helpers/lte", ["exports", "ember-truth-helpers/helpers/lte"], function (_exports, _lte) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _lte.default;
    }
  });
  Object.defineProperty(_exports, "lte", {
    enumerable: true,
    get: function () {
      return _lte.lte;
    }
  });
});
;define("legacy-rp-admin/helpers/not-eq", ["exports", "ember-truth-helpers/helpers/not-equal"], function (_exports, _notEqual) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _notEqual.default;
    }
  });
  Object.defineProperty(_exports, "notEq", {
    enumerable: true,
    get: function () {
      return _notEqual.notEq;
    }
  });
});
;define("legacy-rp-admin/helpers/not", ["exports", "ember-truth-helpers/helpers/not"], function (_exports, _not) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _not.default;
    }
  });
  Object.defineProperty(_exports, "not", {
    enumerable: true,
    get: function () {
      return _not.not;
    }
  });
});
;define("legacy-rp-admin/helpers/or", ["exports", "ember-truth-helpers/helpers/or"], function (_exports, _or) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _or.default;
    }
  });
  Object.defineProperty(_exports, "or", {
    enumerable: true,
    get: function () {
      return _or.or;
    }
  });
});
;define("legacy-rp-admin/helpers/perform", ["exports", "ember-concurrency/helpers/perform"], function (_exports, _perform) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _perform.default;
    }
  });
});
;define("legacy-rp-admin/helpers/pluralize", ["exports", "ember-inflector/lib/helpers/pluralize"], function (_exports, _pluralize) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = _pluralize.default;
  _exports.default = _default;
});
;define("legacy-rp-admin/helpers/singularize", ["exports", "ember-inflector/lib/helpers/singularize"], function (_exports, _singularize) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = _singularize.default;
  _exports.default = _default;
});
;define("legacy-rp-admin/helpers/task", ["exports", "ember-concurrency/helpers/task"], function (_exports, _task) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _task.default;
    }
  });
});
;define("legacy-rp-admin/helpers/xor", ["exports", "ember-truth-helpers/helpers/xor"], function (_exports, _xor) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _xor.default;
    }
  });
  Object.defineProperty(_exports, "xor", {
    enumerable: true,
    get: function () {
      return _xor.xor;
    }
  });
});
;define("legacy-rp-admin/initializers/app-version", ["exports", "ember-cli-app-version/initializer-factory", "legacy-rp-admin/config/environment"], function (_exports, _initializerFactory, _environment) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  let name, version;

  if (_environment.default.APP) {
    name = _environment.default.APP.name;
    version = _environment.default.APP.version;
  }

  var _default = {
    name: 'App Version',
    initialize: (0, _initializerFactory.default)(name, version)
  };
  _exports.default = _default;
});
;define("legacy-rp-admin/initializers/container-debug-adapter", ["exports", "ember-resolver/resolvers/classic/container-debug-adapter"], function (_exports, _containerDebugAdapter) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = {
    name: 'container-debug-adapter',

    initialize() {
      let app = arguments[1] || arguments[0];
      app.register('container-debug-adapter:main', _containerDebugAdapter.default);
      app.inject('container-debug-adapter:main', 'namespace', 'application:main');
    }

  };
  _exports.default = _default;
});
;define("legacy-rp-admin/initializers/ember-concurrency", ["exports", "ember-concurrency/initializers/ember-concurrency"], function (_exports, _emberConcurrency) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _emberConcurrency.default;
    }
  });
});
;define("legacy-rp-admin/initializers/ember-data", ["exports", "ember-data/setup-container", "ember-data"], function (_exports, _setupContainer, _emberData) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /*
  
    This code initializes Ember-Data onto an Ember application.
  
    If an Ember.js developer defines a subclass of DS.Store on their application,
    as `App.StoreService` (or via a module system that resolves to `service:store`)
    this code will automatically instantiate it and make it available on the
    router.
  
    Additionally, after an application's controllers have been injected, they will
    each have the store made available to them.
  
    For example, imagine an Ember.js application with the following classes:
  
    ```app/services/store.js
    import DS from 'ember-data';
  
    export default DS.Store.extend({
      adapter: 'custom'
    });
    ```
  
    ```app/controllers/posts.js
    import { Controller } from '@ember/controller';
  
    export default Controller.extend({
      // ...
    });
  
    When the application is initialized, `ApplicationStore` will automatically be
    instantiated, and the instance of `PostsController` will have its `store`
    property set to that instance.
  
    Note that this code will only be run if the `ember-application` package is
    loaded. If Ember Data is being used in an environment other than a
    typical application (e.g., node.js where only `ember-runtime` is available),
    this code will be ignored.
  */
  var _default = {
    name: 'ember-data',
    initialize: _setupContainer.default
  };
  _exports.default = _default;
});
;define("legacy-rp-admin/initializers/ember-simple-auth", ["exports", "legacy-rp-admin/config/environment", "ember-simple-auth/configuration", "ember-simple-auth/initializers/setup-session", "ember-simple-auth/initializers/setup-session-service", "ember-simple-auth/initializers/setup-session-restoration"], function (_exports, _environment, _configuration, _setupSession, _setupSessionService, _setupSessionRestoration) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = {
    name: 'ember-simple-auth',

    initialize(registry) {
      const config = _environment.default['ember-simple-auth'] || {};
      config.rootURL = _environment.default.rootURL || _environment.default.baseURL;

      _configuration.default.load(config);

      (0, _setupSession.default)(registry);
      (0, _setupSessionService.default)(registry);
      (0, _setupSessionRestoration.default)(registry);
    }

  };
  _exports.default = _default;
});
;define("legacy-rp-admin/initializers/export-application-global", ["exports", "legacy-rp-admin/config/environment"], function (_exports, _environment) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.initialize = initialize;
  _exports.default = void 0;

  function initialize() {
    var application = arguments[1] || arguments[0];

    if (_environment.default.exportApplicationGlobal !== false) {
      var theGlobal;

      if (typeof window !== 'undefined') {
        theGlobal = window;
      } else if (typeof global !== 'undefined') {
        theGlobal = global;
      } else if (typeof self !== 'undefined') {
        theGlobal = self;
      } else {
        // no reasonable global, just bail
        return;
      }

      var value = _environment.default.exportApplicationGlobal;
      var globalName;

      if (typeof value === 'string') {
        globalName = value;
      } else {
        globalName = Ember.String.classify(_environment.default.modulePrefix);
      }

      if (!theGlobal[globalName]) {
        theGlobal[globalName] = application;
        application.reopen({
          willDestroy: function () {
            this._super.apply(this, arguments);

            delete theGlobal[globalName];
          }
        });
      }
    }
  }

  var _default = {
    name: 'export-application-global',
    initialize: initialize
  };
  _exports.default = _default;
});
;define("legacy-rp-admin/instance-initializers/ember-data", ["exports", "ember-data/initialize-store-service"], function (_exports, _initializeStoreService) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = {
    name: 'ember-data',
    initialize: _initializeStoreService.default
  };
  _exports.default = _default;
});
;define("legacy-rp-admin/instance-initializers/ember-simple-auth", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  // This is only needed for backwards compatibility and will be removed in the
  // next major release of ember-simple-auth. Unfortunately, there is no way to
  // deprecate this without hooking into Ember's internals…
  var _default = {
    name: 'ember-simple-auth',

    initialize() {}

  };
  _exports.default = _default;
});
;define("legacy-rp-admin/models/player", ["exports", "ember-data"], function (_exports, _emberData) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * A player model.
   */
  var _default = _emberData.default.Model.extend({
    /**
     * 
     */
    identifier: _emberData.default.attr('string'),

    /**
     * The name (also referred to as username).
     */
    name: _emberData.default.attr('string'),

    /**
     * A link to the players's steam profile.
     */
    steamProfile: _emberData.default.attr('string')
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/models/user", ["exports", "ember-data"], function (_exports, _emberData) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * A user model.
   */
  var _default = _emberData.default.Model.extend({
    /**
     * The account id. Usually equal to the user's steam 64 id.
     */
    accountId: _emberData.default.attr('string'),

    /**
     * A HEX-ified version of "accountId" with a special prefix.
     */
    identifier: _emberData.default.attr('string'),

    /**
     * The name (also referred to as username).
     */
    name: _emberData.default.attr('string'),

    /**
     * A link to an avatar (AKA profile picture).
     */
    avatar: _emberData.default.attr('string'),

    /**
     * A link to the user's steam profile.
     */
    steamProfile: _emberData.default.attr('string'),

    /**
     * Player associated with the user on the game-server.
     */
    player: _emberData.default.belongsTo('player')
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/resolver", ["exports", "ember-resolver"], function (_exports, _emberResolver) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = _emberResolver.default;
  _exports.default = _default;
});
;define("legacy-rp-admin/router", ["exports", "legacy-rp-admin/config/environment"], function (_exports, _environment) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  const Router = Ember.Router.extend({
    location: _environment.default.locationType,
    rootURL: _environment.default.rootURL
  });
  Router.map(function () {
    this.route('test');
    this.route('login', function () {
      this.route('callback');
    });
    this.route('players', function () {
      this.route('view', {
        path: '/:player_id'
      }, function () {
        this.route('warnings');
        this.route('logs');
      });
      this.route('search');
    });
  });
  var _default = Router;
  _exports.default = _default;
});
;define("legacy-rp-admin/routes/application", ["exports", "ember-simple-auth/mixins/application-route-mixin"], function (_exports, _applicationRouteMixin) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * Application route.
   */
  var _default = Ember.Route.extend(_applicationRouteMixin.default, {
    /**
     * The user that's currently logged in.
     */
    currentUser: Ember.inject.service(),

    beforeModel() {
      // Load the current user here so that it becomes available in all routes.
      return this._loadCurrentUser();
    },

    /**
     * Called when the session gets authenticated.
     */
    async sessionAuthenticated() {
      let _super = this._super;
      await this._loadCurrentUser();

      _super.call(this, ...arguments);
    },

    /**
     * Loads the currently logged in user. Invalidates the session if the loading fails.
     */
    _loadCurrentUser() {
      return this.currentUser.load().catch(() => this.session.invalidate());
    }

  });

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/index", ["exports", "ember-simple-auth/mixins/authenticated-route-mixin"], function (_exports, _authenticatedRouteMixin) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * Homepage route.
   */
  var _default = Ember.Route.extend(_authenticatedRouteMixin.default);

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/login/callback", ["exports", "ember-simple-auth/mixins/unauthenticated-route-mixin", "ember-simple-auth/mixins/oauth2-implicit-grant-callback-route-mixin"], function (_exports, _unauthenticatedRouteMixin, _oauth2ImplicitGrantCallbackRouteMixin) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * Callback for authenticating with oauth2 implicit grant.
   */
  var _default = Ember.Route.extend(_unauthenticatedRouteMixin.default, _oauth2ImplicitGrantCallbackRouteMixin.default, {
    /**
     * The authenticator to use for the oauth2 implicit grant.
     */
    authenticator: 'authenticator:oauth2'
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/login/index", ["exports", "ember-simple-auth/mixins/unauthenticated-route-mixin"], function (_exports, _unauthenticatedRouteMixin) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * Route to login with steam.
   */
  var _default = Ember.Route.extend(_unauthenticatedRouteMixin.default);

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/players", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Route.extend({});

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/players/index", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Route.extend();

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/players/search", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Route.extend({
    queryParams: {
      query: ''
    },

    model(params) {
      // Query for any records that matches the provided query.
      return this.store.query('player', {
        filter: {
          query: params.query
        }
      });
    }

  });

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/players/view", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Route.extend({
    afterModel(model) {
      // TODO 2019 - add model name to url to make a more user-friendly url.
      console.log(JSON.stringify(model));
    }

  });

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/players/view/logs", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Route.extend();

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/players/view/warnings", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.Route.extend();

  _exports.default = _default;
});
;define("legacy-rp-admin/routes/test", ["exports", "ember-simple-auth/mixins/authenticated-route-mixin"], function (_exports, _authenticatedRouteMixin) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * Just a test route for testing.
   */
  var _default = Ember.Route.extend(_authenticatedRouteMixin.default, {});

  _exports.default = _default;
});
;define("legacy-rp-admin/serializers/application", ["exports", "ember-data"], function (_exports, _emberData) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * The application serializer.
   */
  var _default = _emberData.default.JSONAPISerializer.extend({});

  _exports.default = _default;
});
;define("legacy-rp-admin/services/ajax", ["exports", "ember-ajax/services/ajax"], function (_exports, _ajax) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _ajax.default;
    }
  });
});
;define("legacy-rp-admin/services/cookies", ["exports", "ember-cookies/services/cookies"], function (_exports, _cookies) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = _cookies.default;
  _exports.default = _default;
});
;define("legacy-rp-admin/services/current-user", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  /**
   * A service to manage the currently logged in user.
   */
  var _default = Ember.Service.extend({
    session: Ember.inject.service(),
    store: Ember.inject.service(),

    load() {
      // Check if the session is authenticated.
      if (this.session.isAuthenticated) {
        // Query the store for myself and set it when found.
        return this.store.queryRecord('user', {
          me: true
        }).then(user => {
          this.set('user', user);
        });
      } else {
        // Just resolve.........
        return Ember.RSVP.resolve();
      }
    }

  });

  _exports.default = _default;
});
;define("legacy-rp-admin/services/session", ["exports", "ember-simple-auth/services/session"], function (_exports, _session) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;
  var _default = _session.default;
  _exports.default = _default;
});
;define("legacy-rp-admin/services/text-measurer", ["exports", "ember-text-measurer/services/text-measurer"], function (_exports, _textMeasurer) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  Object.defineProperty(_exports, "default", {
    enumerable: true,
    get: function () {
      return _textMeasurer.default;
    }
  });
});
;define("legacy-rp-admin/session-stores/application", ["exports", "ember-simple-auth/session-stores/adaptive"], function (_exports, _adaptive) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = _adaptive.default.extend();

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/application", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "Y8hGP6EU",
    "block": "{\"symbols\":[],\"statements\":[[7,\"div\"],[11,\"id\",\"app\"],[9],[0,\"\\n\"],[0,\"  \"],[7,\"header\"],[9],[0,\"\\n    \"],[7,\"div\"],[11,\"class\",\"container-outer navbar\"],[9],[0,\"\\n      \"],[4,\"link-to\",[\"index\"],null,{\"statements\":[[7,\"h2\"],[9],[0,\"Legacy Roleplay\"],[10]],\"parameters\":[]},null],[0,\"\\n\\n      \"],[7,\"div\"],[9],[0,\"\\n\"],[4,\"if\",[[23,[\"session\",\"isAuthenticated\"]]],null,{\"statements\":[[0,\"          \"],[7,\"a\"],[11,\"href\",\"#\"],[9],[0,\"\\n            \"],[1,[27,\"fa-icon\",[\"sign-out-alt\"],null],false],[0,\" Logout\\n          \"],[3,\"action\",[[22,0,[]],\"invalidateSession\"]],[10],[0,\"\\n\"]],\"parameters\":[]},{\"statements\":[[0,\"          \"],[7,\"a\"],[11,\"href\",\"/auth/login/steam\"],[9],[0,\"\\n            \"],[1,[27,\"fa-icon\",[\"steam-symbol\"],[[\"prefix\"],[\"fab\"]]],false],[0,\" Login with steam\\n          \"],[10],[0,\"\\n\"]],\"parameters\":[]}],[0,\"      \"],[10],[0,\"\\n    \"],[10],[0,\"\\n  \"],[10],[0,\"\\n\\n\"],[0,\"  \"],[7,\"main\"],[9],[0,\"\\n    \"],[1,[21,\"outlet\"],false],[0,\"\\n  \"],[10],[0,\"\\n\\n\"],[0,\"  \"],[7,\"footer\"],[9],[0,\"\\n    \"],[7,\"div\"],[11,\"class\",\"container-outer footer\"],[9],[0,\"\\n      \"],[7,\"h5\"],[9],[0,\"\\n        Made with \"],[7,\"span\"],[11,\"class\",\"red\"],[9],[1,[27,\"fa-icon\",[\"heart\"],null],false],[10],[0,\" by ExpDev (Marius) - \"],[7,\"a\"],[11,\"href\",\"https://github.com/ExpDev07/legacy-rp-admin-v3\"],[9],[0,\"https://github.com/ExpDev07/legacy-rp-admin-v3\"],[10],[0,\"\\n      \"],[10],[0,\"\\n    \"],[10],[0,\"\\n  \"],[10],[0,\"\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/application.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/index", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "cDOpH+bL",
    "block": "{\"symbols\":[],\"statements\":[[7,\"div\"],[11,\"class\",\"container container-outer\"],[9],[0,\"\\n  \"],[7,\"h1\"],[9],[0,\"Hey there, \"],[7,\"strong\"],[9],[1,[23,[\"currentUser\",\"user\",\"name\"]],false],[10],[0,\"! Start viewing our servers.\"],[10],[0,\"\\n\\n  \"],[7,\"p\"],[9],[0,\"\\n    \"],[7,\"p\"],[9],[0,\"Hello there, \"],[7,\"strong\"],[9],[1,[23,[\"currentUser\",\"user\",\"name\"]],false],[10],[0,\", your steam profile is \"],[7,\"a\"],[12,\"href\",[23,[\"currentUser\",\"user\",\"steamProfile\"]]],[9],[1,[23,[\"currentUser\",\"user\",\"steamProfile\"]],false],[10],[0,\"!\"],[10],[0,\"\\n  \"],[10],[0,\"\\n\\n  \"],[7,\"p\"],[9],[0,\"\\n    \"],[4,\"link-to\",[\"players.index\"],null,{\"statements\":[[0,\"Go to players\"]],\"parameters\":[]},null],[0,\".\\n  \"],[10],[0,\"\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/index.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/login/index", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "c1YXeAGT",
    "block": "{\"symbols\":[],\"statements\":[[7,\"div\"],[11,\"class\",\"container-outer container\"],[9],[0,\"\\n  \"],[7,\"h1\"],[9],[0,\"Oops...! It looks like you've just hit a wall.\"],[10],[0,\"\\n\\n  \"],[7,\"p\"],[9],[0,\"\\n    You must be logged in to use this website. That's not hard though. Just use the button below to login with your \"],[7,\"strong\"],[9],[0,\"Steam profile\"],[10],[0,\". It seamlessly connects\\n    with your account on our game-server. Easy, right?\\n  \"],[10],[0,\"\\n\\n  \"],[7,\"p\"],[9],[0,\"\\n    \"],[7,\"strong\"],[9],[7,\"a\"],[11,\"href\",\"/auth/login/steam\"],[9],[0,\"Login with steam now \"],[1,[27,\"fa-icon\",[\"steam-symbol\"],[[\"prefix\"],[\"fab\"]]],false],[10],[10],[0,\"\\n  \"],[10],[0,\"\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/login/index.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/players", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "/47npnRW",
    "block": "{\"symbols\":[\"player\"],\"statements\":[[7,\"div\"],[11,\"class\",\"section section-blue\"],[9],[0,\"\\n  \"],[7,\"div\"],[11,\"class\",\"container container-outer\"],[9],[0,\"\\n    \"],[7,\"div\"],[9],[0,\"\\n      \"],[7,\"h2\"],[9],[0,\"Search our servers for a player\"],[10],[0,\"\\n      \"],[7,\"p\"],[9],[0,\"\\n        Use the dropdown below to search our servers for a player. It accepts a \"],[7,\"strong\"],[9],[0,\"Steam Name\"],[10],[0,\" or \"],[7,\"strong\"],[9],[0,\"HEX-id\"],[10],[0,\". Using tools such\\n        as \"],[7,\"strong\"],[9],[7,\"a\"],[11,\"href\",\"http://vacbanned.com/\"],[9],[0,\"vacbanned.com\"],[10],[10],[0,\" might help you convert other types of \"],[7,\"strong\"],[9],[0,\"Steam IDs\"],[10],[0,\" to these.\\n      \"],[10],[0,\"\\n    \"],[10],[0,\"\\n\\n\"],[4,\"power-select\",null,[[\"placeholder\",\"selected\",\"search\",\"onchange\"],[\"Marius Truckster | steam:11000010df22c8b\",[23,[\"selected\"]],[27,\"action\",[[22,0,[]],\"search\"],null],[27,\"action\",[[22,0,[]],\"select\"],null]]],{\"statements\":[[0,\"\\n      \"],[1,[22,1,[\"name\"]],false],[0,\" (\"],[1,[22,1,[\"identifier\"]],false],[0,\")\\n\"]],\"parameters\":[1]},null],[0,\"  \"],[10],[0,\"\\n\"],[10],[0,\"\\n\\n\"],[1,[21,\"outlet\"],false]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/players.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/players/index", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "2P52qE/n",
    "block": "{\"symbols\":[],\"statements\":[[7,\"div\"],[11,\"class\",\"container container-outer\"],[9],[0,\"\\n  \"],[7,\"h1\"],[9],[0,\"Page still in progress.\"],[10],[0,\"\\n  \"],[7,\"p\"],[9],[0,\"\\n    This page is still being worked on. Please use the search bar above to find players.\\n  \"],[10],[0,\"\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/players/index.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/players/search", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "xbxfOIyJ",
    "block": "{\"symbols\":[\"player\"],\"statements\":[[7,\"div\"],[11,\"class\",\"container container-outer\"],[9],[0,\"\\n  \"],[7,\"ul\"],[9],[0,\"\\n\"],[4,\"each\",[[23,[\"model\"]]],null,{\"statements\":[[0,\"      \"],[7,\"li\"],[9],[1,[22,1,[\"name\"]],false],[0,\" (\"],[1,[22,1,[\"identifier\"]],false],[0,\")\"],[10],[0,\"\\n\"]],\"parameters\":[1]},{\"statements\":[[0,\"      \"],[7,\"h1\"],[9],[0,\"No players found.\"],[10],[0,\"\\n      \"],[7,\"p\"],[9],[0,\"\\n        There were no players matching your search. \"],[7,\"b\"],[9],[0,\"Hint:\"],[10],[0,\" use HEX-identifiers when searching-- they're more accurate compared to names.\\n      \"],[10],[0,\"\\n\"]],\"parameters\":[]}],[0,\"  \"],[10],[0,\"\\n\\n  \"],[7,\"p\"],[9],[0,\"\\n    \"],[4,\"link-to\",[\"index\"],null,{\"statements\":[[0,\"Go to index\"]],\"parameters\":[]},null],[0,\".\\n  \"],[10],[0,\"\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/players/search.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/players/view", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "xw5TIB5K",
    "block": "{\"symbols\":[],\"statements\":[[7,\"div\"],[11,\"class\",\"container container-outer\"],[9],[0,\"\\n\\n  \"],[7,\"div\"],[11,\"class\",\"card card-error\"],[9],[0,\"\\n    \"],[7,\"strong\"],[9],[1,[27,\"fa-icon\",[\"gavel\"],null],false],[0,\" Banned!\"],[10],[0,\" Currently banned by Marius Truckster for \"],[7,\"i\"],[9],[0,\"RDM and VDM + being disrespectful towards staff\"],[10],[0,\".\\n  \"],[10],[0,\"\\n\\n  \"],[7,\"h1\"],[9],[1,[23,[\"model\",\"name\"]],false],[0,\" (\"],[1,[23,[\"model\",\"identifier\"]],false],[0,\")\"],[10],[0,\"\\n  \"],[7,\"p\"],[9],[0,\"\\n    This player has a total playtime of \"],[7,\"strong\"],[9],[0,\"5 days, 23 hours, 20 minutes, and 55 seconds\"],[10],[0,\" spent on Legacy Roleplay. Visit their\\n    steam profile at \"],[7,\"a\"],[12,\"href\",[23,[\"model\",\"steamProfile\"]]],[11,\"target\",\"_blank\"],[9],[1,[23,[\"model\",\"steamProfile\"]],false],[10],[0,\".\\n  \"],[10],[0,\"\\n\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/players/view.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/players/view/logs", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "frTALeX2",
    "block": "{\"symbols\":[],\"statements\":[[7,\"h3\"],[9],[0,\"Viewing logs\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/players/view/logs.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/players/view/warnings", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "YCnv9RQx",
    "block": "{\"symbols\":[],\"statements\":[[7,\"h3\"],[9],[0,\"Warnings\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/players/view/warnings.hbs"
    }
  });

  _exports.default = _default;
});
;define("legacy-rp-admin/templates/test", ["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.default = void 0;

  var _default = Ember.HTMLBars.template({
    "id": "5S7OqIq4",
    "block": "{\"symbols\":[],\"statements\":[[7,\"div\"],[11,\"class\",\"container container-outer\"],[9],[0,\"\\n  \"],[7,\"h1\"],[9],[0,\"You're on test.\"],[10],[0,\"\\n\\n  \"],[7,\"p\"],[9],[0,\"\\n    \"],[4,\"link-to\",[\"index\"],null,{\"statements\":[[0,\"Go to index\"]],\"parameters\":[]},null],[0,\".\\n  \"],[10],[0,\"\\n\"],[10]],\"hasEval\":false}",
    "meta": {
      "moduleName": "legacy-rp-admin/templates/test.hbs"
    }
  });

  _exports.default = _default;
});
;

;define('legacy-rp-admin/config/environment', [], function() {
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

;
          if (!runningTests) {
            require("legacy-rp-admin/app")["default"].create({"LOG_RESOLVER":true,"LOG_ACTIVE_GENERATION":true,"LOG_TRANSITIONS":true,"LOG_TRANSITIONS_INTERNAL":true,"LOG_VIEW_LOOKUPS":true,"name":"legacy-rp-admin","version":"0.0.0+e7134932"});
          }
        
//# sourceMappingURL=legacy-rp-admin.map
