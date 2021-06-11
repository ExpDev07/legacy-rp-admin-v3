const Localization = {
    async install(Vue, options) {
        let lang = {};

        function searchObject(object, key) {
            if (!object) {
                return null;
            } else if (!key.includes('.')) {
                return key in object ? object[key] : null;
            }

            let path = key.split('.');
            const current = path.shift();

            return current in object ? searchObject(object[current], path.join('.')) : null;
        }

        Vue.prototype.loadLocale = function(locale) {
            try {
                console.info('Loading locale ' + locale);
                lang = require('../locales/' + locale + '.json');
            } catch(e) {
                console.error('Failed to load locale "' + locale + '", falling back to "en-us"');

                try {
                    lang = require('../locales/en-us.json');
                } catch(e) {
                    console.error('Failed to load fallback locale "en-us"');
                }
            }

            try {
                Vue.prototype.$moment.locale(locale);
            } catch(e) {
                console.error('Failed to load moment locale "' + locale + '"', e);
            }
        };
        Vue.prototype.t = function (key, ...params) {
            let val = lang ? searchObject(lang, key) : null;

            if (Array.isArray(params) && typeof val === 'string') {
                for (let x = 0; x < params.length; x++) {
                    val = val.replaceAll('{' + x + '}', params[x]);
                }
            }

            return val ? val : (() => {
                console.error('Locale "' + key + '" not found');
                console.debug('Loaded locale:', lang);
                return 'MISSING_LOCALE';
            })();
        };
    },
}

export default Localization;
