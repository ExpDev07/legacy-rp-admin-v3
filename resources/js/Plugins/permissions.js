const Permissions = {
    async install(Vue, options) {
        const permissions = options.props.auth.permissions;

        function getPermissionLevel() {
            if (!options.props.auth || !options.props.auth.player) {
                return 0;
            }

            if (options.props.auth.player.isSuperAdmin) {
                return 3;
            } else if (options.props.auth.player.isPanelTrusted) {
                return 2;
            } else if (options.props.auth.player.isStaff) {
                return 1;
            }
            return 0;
        }

        const permissionLevel = getPermissionLevel();

        Vue.prototype.perm = {
            PERM_LIVEMAP:    'livemap',
            PERM_SCREENSHOT: 'screenshot',
            PERM_SUSPICIOUS: 'suspicious',
            PERM_ADVANCED:   'advanced',
            PERM_LOCK_BAN:   'lock_ban',

            check(permission) {
                if (!(permission in permissions)) {
                    return true;
                }

                return permissions[permission] <= permissionLevel;
            }
        };
    },
}

export default Permissions;
