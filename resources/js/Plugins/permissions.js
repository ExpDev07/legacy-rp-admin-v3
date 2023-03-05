const Permissions = {
    async install(Vue, options) {
        const permissions = options.props.auth.permissions;

        function getPermissionLevel() {
            if (!options.props.auth || !options.props.auth.player) {
                return 0;
            }

            if (options.props.auth.player.isRoot) {
                return 4;
            } else if (options.props.auth.player.isSuperAdmin) {
                return 3;
            } else if (options.props.auth.player.isSeniorStaff) {
                return 2;
            } else if (options.props.auth.player.isStaff) {
                return 1;
            }
            return 0;
        }

        const permissionLevel = getPermissionLevel();

        Vue.prototype.perm = {
            PERM_SOFT_BAN:       'soft_ban',
            PERM_LIVEMAP:        'livemap',
            PERM_SCREENSHOT:     'screenshot',
            PERM_SUSPICIOUS:     'suspicious',
            PERM_ADVANCED:       'advanced',
            PERM_LOCK_BAN:       'lock_ban',
            PERM_EDIT_TAG:       'edit_tag',
            PERM_LOADING_SCREEN: 'loading_screen',
            PERM_VIEW_QUEUE:     'view_queue',
            PERM_TWITTER:        'twitter',
            PERM_LINKED:         'linked',
            PERM_ANNOUNCEMENT:   'announcement',

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
