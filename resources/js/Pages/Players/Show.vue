<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10 mobile:flex-wrap">
                <h1 class="dark:text-white">
                    {{ player.playerName }}
                </h1>
                <div
                    class="flex items-center space-x-5 mobile:flex-wrap mobile:w-full mobile:!mr-0 mobile:!ml-0 mobile:space-x-0">
                    <badge class="border-blue-200 bg-blue-100 dark:bg-blue-700 font-semibold cursor-pointer"
                           :click="copyLicense">
                        <span>{{ t('players.show.copy_license') }}</span>
                    </badge>

                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-if="player.isBanned">
                        <span class="font-semibold">{{ t('global.banned') }}</span>
                    </badge>
                    <badge class="border-purple-200 bg-purple-100 dark:bg-purple-700"
                           v-if="player.isTrusted && !player.isStaff">
                        <span class="font-semibold">{{ t('global.trusted') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale"
                           v-if="player.isStaff && !player.isSeniorStaff">
                        <span class="font-semibold">{{ t('global.staff') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale"
                           v-if="player.isSeniorStaff && !player.isSuperAdmin">
                        <span class="font-semibold">{{ t('global.senior_staff') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale"
                           v-if="player.isSuperAdmin">
                        <span class="font-semibold">{{ t('global.super') }}</span>
                    </badge>

                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="whitelisted">
                        <span class="font-semibold">{{ t('global.whitelisted') }}</span>
                    </badge>

                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-if="blacklisted">
                        <span class="font-semibold">{{ t('global.blacklisted') }}</span>
                    </badge>

                    <badge class="border-yellow-200 bg-yellow-400 dark:bg-yellow-600"
                           v-if="statusLoading">
                        <span class="font-semibold">{{ t('global.loading') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale"
                           v-else-if="status.status === 'online'">
                        <span class="font-semibold">{{ t('global.status.online') }}
                            <sup>[{{ status.serverId }}]</sup>
                        </span>

                        <span class="font-semibold cursor-pointer ml-1" @click="loadStatus()">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                    </badge>
                    <badge class="border-red-200 bg-warning-pale dark:bg-dark-warning-pale"
                           v-else-if="status.status === 'unavailable'"
                           :title="t('global.status.unavailable_info')">
                        <span class="font-semibold">{{ t('global.status.unavailable') }}</span>

                        <span class="font-semibold cursor-pointer ml-1" @click="loadStatus()">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                    </badge>
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-else>
                        <span class="font-semibold">{{ t('global.status.' + status.status) }}</span>

                        <span class="font-semibold cursor-pointer ml-1" @click="loadStatus()">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                    </badge>

                    <badge class="border-gray-200 bg-secondary dark:bg-dark-secondary"
                           :title="formatSecondDiff(player.playTime)" v-html="local.played"></badge>

                    <badge class="border-pink-300 bg-pink-200 dark:bg-pink-700" v-if="player.tag">
                        <span class="font-semibold">{{ player.tag }}</span>
                    </badge>
                </div>
            </div>
            <div class="text-sm italic">
                <span class="block mb-1" v-if="player.playerAliases && player.playerAliases.length > 0">
                    <span class="font-bold">{{ t('players.show.aliases') }}:</span>
                    {{ player.playerAliases.join(", ") }}
                </span>
                <span class="block mb-1" v-if="getPlayerMetadata()">
                    <span class="font-bold">{{ t('players.show.metadata') }}:</span>
                    {{ getPlayerMetadata() }}
                </span>
                <span class="block">
                    <span class="font-bold">{{ t('players.show.enabled_commands') }}:</span>
                    {{ player.enabledCommands.length > 0 ? player.enabledCommands.map(e => '/' + e).join(", ") : "N/A" }}

                    <a href="#" class="text-indigo-600 dark:text-indigo-400" @click="$event.preventDefault(); isEnablingCommands = true" v-if="$page.auth.player.isSuperAdmin">{{ t('players.show.edit') }}</a>
                </span>
            </div>
            <p class="dark:text-dark-muted">
                {{ t('players.show.description') }}
            </p>
        </portal>

        <div class="flex flex-wrap justify-between mb-6">
            <div class="mb-3 flex flex-wrap">
                <!-- Soft Ban -->
                <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale py-2 mr-3"
                       v-if="this.perm.check(this.perm.PERM_SOFT_BAN) && player.isSoftBanned">
                    <span class="font-semibold">{{ t('global.soft_banned') }}</span>
                    <a href="#" @click="removeSoftBan($event)" class="ml-1 text-white"
                       :title="t('players.show.remove_soft_ban')">
                        <i class="fas fa-times"></i>
                    </a>
                </badge>

                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="addSoftBan()" v-if="this.perm.check(this.perm.PERM_SOFT_BAN) && !player.isSoftBanned">
                    <i class="fas fa-user-lock"></i>
                    {{ t('players.show.add_soft_ban') }}
                </button>

                <!-- Panel drug department -->
                <badge class="border-orange-200 bg-warning-pale dark:bg-dark-warning-pale py-2 mr-3"
                       v-if="$page.auth.player.isSuperAdmin && player.panelDrugDepartment">
                    <span class="font-semibold">{{ t('global.panel_drug_department') }}</span>
                </badge>
            </div>

            <div class="absolute top-2 left-2 flex" v-if="this.perm.check(this.perm.PERM_LINKED)">
                <a
                    class="p-1 text-sm mr-2 font-bold leading-4 text-center w-7 rounded border-gray-200 bg-secondary dark:bg-dark-secondary border-2 block"
                    :href="'/linked_tokens?license=' + player.licenseIdentifier"
                    :title="t('players.show.show_link_token')"
                    target="_blank"
                >
                    <i class="fas fa-drumstick-bite"></i>
                </a>
                <a
                    class="p-1 text-sm mr-2 font-bold leading-4 text-center w-7 rounded border-gray-200 bg-secondary dark:bg-dark-secondary border-2 block"
                    :href="'/linked_ips?license=' + player.licenseIdentifier"
                    :title="t('players.show.show_link_ip')"
                    target="_blank"
                >
                    <i class="fas fa-ethernet"></i>
                </a>
                <a
                    class="p-1 text-sm mr-2 font-bold leading-4 text-center w-7 rounded border-gray-200 bg-secondary dark:bg-dark-secondary border-2 block"
                    :href="'/linked_identifiers?license=' + player.licenseIdentifier"
                    :title="t('players.show.show_link_identifier')"
                    target="_blank"
                >
                    <i class="fas fa-passport"></i>
                </a>
            </div>

            <div class="absolute top-2 right-2 flex">
                <!-- Edit Role -->
                <button
                    class="py-1 px-2 ml-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger block"
                    @click="isRoleEdit = true"
                    v-if="allowRoleEdit && !player.isSuperAdmin"
                    :title="t('players.show.edit_role')"
                >
                    <i class="fas fa-clipboard-list"></i>
                </button>
                <!-- Add Tag -->
                <button
                    class="py-1 px-2 ml-2 font-semibold text-white rounded bg-success dark:bg-dark-success block"
                    @click="isTagging = true"
                    :title="t('players.show.edit_tag')"
                    v-if="this.perm.check(this.perm.PERM_EDIT_TAG)"
                >
                    <i class="fas fa-tag"></i>
                </button>
                <!-- Create screen capture -->
                <button
                    class="py-1 px-2 ml-2 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 block"
                    @click="isScreenCapture = true"
                    :title="t('screenshot.screencapture')"
                    v-if="status.status === 'online' && this.perm.check(this.perm.PERM_SCREENSHOT)"
                >
                    <i class="fas fa-video"></i>
                </button>
                <!-- Create screenshot -->
                <button
                    class="py-1 px-2 ml-2 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 block"
                    @click="isScreenshot = true; createScreenshot()"
                    :title="t('screenshot.screenshot')"
                    v-if="status.status === 'online' && this.perm.check(this.perm.PERM_SCREENSHOT)"
                >
                    <i class="fas fa-camera"></i>
                </button>
                <!-- View on Map -->
                <a
                    class="py-1 px-2 ml-2 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 block"
                    :href="'/map#' + player.licenseIdentifier"
                    :title="t('global.view_map')"
                    v-if="this.perm.check(this.perm.PERM_LIVEMAP) && status.status === 'online'"
                    target="_blank"
                >
                    <i class="fas fa-map"></i>
                </a>
                <!-- Revive -->
                <button
                    class="py-1 px-2 ml-2 font-semibold text-white rounded bg-success dark:bg-dark-success block"
                    @click="revivePlayer()" v-if="status.status === 'online'"
                    :title="t('players.show.revive')"
                >
                    <i class="fas fa-heartbeat"></i>
                </button>
            </div>

            <div class="mb-3 flex flex-wrap justify-end">
                <!-- StaffPM -->
                <button
                    class="px-5 py-2 ml-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isStaffPM = true" v-if="status.status === 'online'">
                    <i class="fas fa-envelope-open-text"></i>
                    {{ t('players.show.staffpm') }}
                </button>
                <!-- Kicking -->
                <button
                    class="px-5 py-2 ml-3 font-semibold text-white rounded bg-yellow-600 dark:bg-yellow-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isKicking = true" v-if="status.status === 'online'">
                    <i class="fas fa-user-minus"></i>
                    {{ t('players.show.kick') }}
                </button>
                <!-- Edit Ban -->
                <inertia-link
                    class="px-5 py-2 font-semibold text-white rounded bg-yellow-500 ml-3 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    v-bind:href="'/players/' + player.licenseIdentifier + '/bans/' + player.ban.id + '/edit'"
                    v-if="player.isBanned && (!player.ban.locked || this.perm.check(this.perm.PERM_LOCK_BAN))">
                    <i class="mr-1 fas fa-edit"></i>
                    {{ t('players.show.edit_ban') }}
                </inertia-link>
                <!-- Unbanning -->
                <button
                    class="px-5 py-2 ml-3 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="unbanPlayer()"
                    v-if="player.isBanned && (!player.ban.locked || this.perm.check(this.perm.PERM_LOCK_BAN))">
                    <i class="mr-1 fas fa-lock-open"></i>
                    {{ t('players.show.unban') }}
                </button>
                <!-- Banning -->
                <button
                    class="px-5 py-2 ml-3 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isBanning = true" v-else-if="!player.isBanned">
                    <i class="mr-1 fas fa-gavel"></i>
                    {{ t('players.show.issue') }}
                </button>
                <!-- Lock ban -->
                <inertia-link
                    class="px-5 py-2 ml-3 font-semibold text-white rounded bg-success dark:bg-dark-success mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    method="POST"
                    v-bind:href="'/players/' + player.licenseIdentifier + '/bans/' + player.ban.id + '/lock'"
                    v-if="player.isBanned && !player.ban.locked && this.perm.check(this.perm.PERM_LOCK_BAN)">
                    <i class="mr-1 fas fa-lock"></i>
                    {{ t('players.show.lock_ban') }}
                </inertia-link>
                <!-- Lock ban -->
                <inertia-link
                    class="px-5 py-2 ml-3 font-semibold text-white rounded bg-success dark:bg-dark-success mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    method="POST"
                    v-bind:href="'/players/' + player.licenseIdentifier + '/bans/' + player.ban.id + '/unlock'"
                    v-if="player.isBanned && player.ban.locked && this.perm.check(this.perm.PERM_LOCK_BAN)">
                    <i class="mr-1 fas fa-lock-open"></i>
                    {{ t('players.show.unlock_ban') }}
                </inertia-link>
            </div>
        </div>

        <!-- Discord Accounts -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isShowingDiscord">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.discord_title') }}</h3>
                <div v-if="isShowingDiscordLoading">
                    <div class="flex justify-center items-center my-6 mt-12">
                        <div>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="w-full flex justify-between" v-for="(discord, id) in discordAccounts" :key="id">
                        <div class="w-full relative">
                            <a
                                class="flex-1 block p-5 m-2 font-semibold text-white bg-blue-800 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                                v-if="discord && discord.username"
                                href="#"
                                :title="t('players.show.discord_copy')"
                                @click="copyText($event, '<@' + discord.id + '> ' + discord.username + '#' + discord.discriminator)"
                            >
                                <avatar
                                    v-if="discord.avatar"
                                    class="mr-3"
                                    :src="discord.avatar"
                                    :alt="discord.username + ' Avatar'"
                                />
                                <span>
                                    {{ discord.username }}#{{ discord.discriminator }}
                                </span>
                            </a>
                            <a
                                class="flex-1 block p-5 m-2 font-semibold text-white bg-blue-800 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                                v-else
                                href="#"
                                :title="t('players.show.discord_copy')"
                                @click="copyText($event, '<@' + id + '>')"
                            >
                                <i class="mr-1 fab fa-discord"></i>
                                {{ t('players.show.discord', id) }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isShowingDiscord = false">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Linked Accounts -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isShowingLinked">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.linked_title') }}</h3>
                <div v-if="isShowingLinkedLoading">
                    <div class="flex justify-center items-center my-6 mt-12">
                        <div>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="w-full flex justify-between mb-2" v-for="(link, identifier) in linkedAccounts.linked"
                         :key="identifier">
                        <div class="p-3 w-1/2 relative">
                            <b class="block">{{ link.label }}</b>
                            <pre class="text-xs overflow-hidden overflow-ellipsis"
                                :class="{'dark:text-green-300 text-green-700' : link.last_used}"
                                :title="identifier"
                            >{{ identifier }}</pre>

                            <button
                                class="p-1 absolute top-0 right-0 text-xs font-semibold bg-transparent text-red-600 dark:text-red-400 rounded"
                                @click="removeIdentifier(identifier)"
                                :title="t('global.remove')"
                                v-if="$page.auth.player.isSuperAdmin"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="p-3 w-1/2" v-if="link.accounts.length > 0">
                            <a
                                class="px-5 py-1 mb-2 border-2 rounded block w-full border-blue-200 bg-primary-pale dark:bg-dark-primary-pale"
                                :href="'/players/' + account.license_identifier"
                                target="_blank"
                                v-for="account in link.accounts"
                                :key="account.license_identifier"
                            >
                                <span class="font-semibold">{{ account.player_name }}</span>
                            </a>
                        </div>
                        <div class="p-3 w-1/2" v-else>
                            <span class="italic text-sm">{{ t('players.show.no_link') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isShowingLinked = false">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Anti Cheat -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isShowingAntiCheat">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.anti_cheat_title') }}</h3>
                <div v-if="isShowingAntiCheatLoading">
                    <div class="flex justify-center items-center my-6 mt-12">
                        <div>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </div>
                    </div>
                </div>
                <div v-else>
                    <table class="w-full whitespace-no-wrap">
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="event in antiCheatEvents" :key="event.id">
                            <td class="px-3 py-2 border-t">{{ event.type }}</td>
                            <td class="px-3 py-2 border-t">
                                <a href="#" @click="showAntiCheatMetadata($event, event.metadata)" class="text-indigo-600 !no-underline dark:text-indigo-300 hover:text-yellow-500 dark:hover:text-yellow-300">
                                    {{ t("players.show.anti_cheat_metadata") }}
                                </a>
                            </td>
                            <td class="px-3 py-2 border-t">{{ event.timestamp * 1000 | formatTime(true) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button"
                            class="px-5 py-2 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isShowingAntiCheat = false">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <modal :show.sync="antiCheatMetadata">
			<template #header>
				<h1 class="dark:text-white">
					{{ t('players.show.anti_cheat_metadata') }}
				</h1>
			</template>

			<template #default>
				<pre class="block text-xs whitespace-pre-wrap break-words hljs px-3 py-2 rounded" v-html="antiCheatMetadataJSON"></pre>
			</template>

			<template #actions>
				<button type="button"
						class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400"
						@click="antiCheatMetadata = false">
					{{ t('global.close') }}
				</button>
			</template>
		</modal>

        <!-- Unloading -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isUnloading">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.unload') }}</h3>
                <form class="space-y-6" @submit.prevent="unloadCharacter">
                    <!-- Message -->
                    <div class="w-full p-3 flex justify-between">
                        <label class="mr-4 block w-1/4 text-center pt-2 font-bold">
                            {{ t('players.show.unload_msg') }}
                        </label>
                        <textarea class="block bg-gray-200 dark:bg-gray-600 rounded w-3/4 px-4 py-2" id="unload_message"
                                  v-model="form.unload.message"></textarea>
                    </div>

                    <p>
                        {{ t('players.show.unload_confirm') }}
                    </p>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                type="submit">
                            <i class="fas fa-bolt mr-1"></i>
                            {{ t('players.show.unload_do') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isUnloading = false">
                            {{ t('global.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Role Edit -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isRoleEdit">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-small-alert">
                <h3 class="mb-2">{{ t('players.show.edit_role') }}</h3>
                <form class="space-y-6">
                    <div class="w-full p-3 flex justify-between">
                        <label class="mr-4 block w-1/4 text-center pt-2 font-bold">
                            {{ t('players.show.role') }}
                        </label>
                        <select class="block bg-gray-200 dark:bg-gray-600 rounded w-3/4 px-4 py-2"
                                v-model="selectedRole">
                            <option value="player">{{ t('players.show.role_player') }}</option>
                            <option value="trusted">{{ t('players.show.role_trusted') }}</option>
                            <option value="staff">{{ t('players.show.role_staff') }}</option>
                            <option value="seniorStaff">{{ t('players.show.role_seniorStaff') }}</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isRoleEdit = false">
                            {{ t('global.cancel') }}
                        </button>
                        <button class="px-5 py-2 font-semibold text-white bg-green-500 rounded hover:bg-green-600"
                                type="button" @click="updateRole">
                            <i class="fas fa-clipboard-list mr-1"></i>
                            {{ t('players.show.edit_role') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tag -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isTagging">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.edit_tag') }}</h3>
                <form class="space-y-6">
                    <div class="flex">
                        <select class="px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600 w-1/2 mr-1"
                                v-model="tagCategory">
                            <option value="custom">{{ t('players.show.tag_custom') }}</option>
                            <option :value="tag.panel_tag" :key="tag.panel_tag" v-for="tag in tags">{{
                                    tag.panel_tag
                                }}
                            </option>
                        </select>

                        <input type="text" class="px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600 w-1/2 ml-1"
                               v-if="tagCategory === 'custom'" v-model="tagCustom"/>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-green-500 rounded hover:bg-green-600"
                                type="button" @click="addTag">
                            <i class="fas fa-tag mr-1"></i>
                            {{ t('players.show.edit_tag') }}
                        </button>
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                type="button" @click="removeTag">
                            {{ t('players.show.remove_tag') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isTagging = false">
                            {{ t('global.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enabled commands -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isEnablingCommands">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.update_commands') }}</h3>
                <form class="space-y-2">
                    <div class="flex items-center" v-for="command in commands" :key="command.name">
                        <input type="checkbox" v-model="command.enabled" :id="command.name" class="mr-2 outline-none">
                        <label>/{{ command.name }}</label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3 mt-4">
                        <button class="px-5 py-2 font-semibold text-white bg-green-500 rounded hover:bg-green-600"
                                type="button" @click="updateCommands">
                            <i class="fas fa-tag mr-1"></i>
                            {{ t('players.show.update_commands') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isEnablingCommands = false">
                            {{ t('global.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- StaffPM -->
        <div>
            <!-- Issuing -->
            <div class="p-8 mb-10 bg-gray-100 rounded dark:bg-dark-secondary" v-if="isStaffPM">
                <div class="mb-8 space-y-5">
                    <h2 class="text-2xl font-semibold">
                        {{ t('players.show.staffpm') }}
                    </h2>
                </div>
                <form class="space-y-6" @submit.prevent="pmPlayer">
                    <!-- Message -->
                    <div>
                        <label class="italic font-semibold" for="pm_message">
                            {{ t('players.show.pm_message') }}
                        </label>
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="pm_message"
                                  name="message" rows="5" placeholder="Please join waiting for support"
                                  v-model="form.pm.message"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.show.pm_confirm') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isStaffPM = false">
                            {{ t('global.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kick -->
        <div>
            <!-- Issuing -->
            <div class="p-8 mb-10 bg-gray-100 rounded dark:bg-dark-secondary" v-if="isKicking">
                <div class="mb-8 space-y-5">
                    <h2 class="text-2xl font-semibold">
                        {{ t('players.show.kick') }}
                    </h2>
                </div>
                <form class="space-y-6" @submit.prevent="kickPlayer">
                    <!-- Reason -->
                    <div>
                        <label class="italic font-semibold" for="kick_reason">
                            {{ t('players.show.kick_reason') }}
                        </label>
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="kick_reason"
                                  name="reason" rows="5" placeholder="You were kicked from the server."
                                  v-model="form.kick.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.show.kick') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isKicking = false">
                            {{ t('global.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mute -->
        <alert class="bg-rose-500 dark:bg-rose-500" v-if="player.mute">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-semibold" v-if="player.mute.expires">
                    {{ t('players.show.muted', formatTime(player.mute.expires*1000)) }}
                </h2>
                <h2 class="text-lg font-semibold" v-else>
                    {{ t('players.show.muted_forever') }}
                </h2>
                <div class="font-semibold" v-if="player.mute.creator">
                    {{ player.mute.creator }}
                </div>
            </div>

            <p class="text-gray-100">
                <span class="whitespace-pre-line">{{ player.mute.reason || t('players.show.no_reason') }}</span>
            </p>
        </alert>

        <!-- Removing system ban -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isConfirmingUnban">
            <div
                class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
                <h3 class="mb-2">{{ t('players.show.unban_system_title') }}</h3>
                <div>
                    <p>
                        {{ t('players.show.unban_system_message') }}
                    </p>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button"
                            class="px-5 py-2 font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary"
                            @click="isConfirmingUnban = false">
                        {{ t('global.close') }}
                    </button>
                    <button class="px-5 py-2 rounded bg-danger dark:bg-dark-danger"
                            type="button" @click="unbanPlayer()">
                        <i class="mr-1 fas fa-lock-open"></i>
                        {{ t('players.show.unban') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Ban -->
        <div>
            <!-- Viewing -->
            <alert class="bg-danger dark:bg-dark-danger" v-if="player.isBanned">

                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold" v-html="local.ban"></h2>
                    <div class="font-semibold">
                        <i class="mr-1 fas fa-lock" v-if="player.ban.locked" :title="t('players.show.ban_locked')"></i>
                        {{ player.ban.timestamp | formatTime }}
                    </div>
                </div>

                <p class="text-gray-100">
                    <span class="whitespace-pre-line">{{ player.ban.reason || t('players.show.no_reason') }}</span>
                </p>

                <p class="text-sm italic">{{ player.ban.banHash }}</p>

            </alert>
            <!-- Issuing -->
            <div class="p-8 mb-10 bg-gray-100 rounded dark:bg-dark-secondary" v-if="isBanning">
                <div class="mb-8 space-y-5">
                    <h2 class="text-2xl font-semibold">
                        {{ t('players.ban.issuing') }}
                    </h2>
                    <p class="text-gray-900 dark:text-gray-100" v-html="local.ban_warning"></p>
                </div>
                <form class="space-y-6" @submit.prevent="submitBan">
                    <!-- Deciding if ban is temporary -->
                    <div class="flex items-center space-x-3">
                        <input class="block p-3 bg-gray-200 rounded shadow" type="checkbox" id="tempban" name="tempban"
                               v-model="isTempBanning">
                        <label class="italic font-semibold" for="tempban">
                            {{ t('players.ban.temporary') }}
                        </label>
                    </div>

                    <!-- Expiration -->
                    <div class="flex flex-wrap" v-if="isTempBanning">
                        <div class="flex items-center space-x-3 w-full mb-3">
                            <input class="block p-3 bg-gray-200 rounded shadow" type="checkbox" id="tempselect"
                                   v-model="isTempSelect">
                            <label class="italic font-semibold" for="tempselect">
                                {{ t('players.ban.temp-select') }}
                            </label>
                        </div>

                        <div class="w-full" v-if="isTempSelect">
                            <label class="italic font-semibold block mb-1">
                                {{ t('players.ban.expiration') }}
                            </label>
                            <div class="flex items-center">
                                <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow mr-1" type="date"
                                       id="expireDate" name="expireDate" step="any"
                                       :min="$moment().format('YYYY-MM-DD')" v-model="form.ban.expireDate" required>
                                <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow" type="time"
                                       id="expireTime" name="expireTime" step="any" v-model="form.ban.expireTime"
                                       required>
                            </div>
                        </div>

                        <div class="mr-1" v-if="!isTempSelect">
                            <label class="italic font-semibold block mb-1">
                                {{ t('players.ban.temp-value') }}
                            </label>
                            <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow min-w-input"
                                   type="number" min="1" id="ban-value" step="1" required>
                        </div>
                        <div v-if="!isTempSelect">
                            <label class="italic font-semibold block mb-1">
                                {{ t('players.ban.temp-type') }}
                            </label>
                            <select class="px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600 min-w-input"
                                    id="ban-type">
                                <option value="hour">{{ t('players.ban.hour') }}</option>
                                <option value="day">{{ t('players.ban.day') }}</option>
                                <option value="week">{{ t('players.ban.week') }}</option>
                                <option value="month">{{ t('players.ban.month') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="italic font-semibold block mb-1" for="reason">
                            {{ t('players.ban.reason') }}
                        </label>
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="reason"
                                  name="reason" rows="5" :placeholder="player.playerName + ' did a big oopsie.'"
                                  v-model="form.ban.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.ban.do_ban') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                                type="button" @click="isBanning = false">
                            {{ t('global.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Useful links -->
        <v-section class="py-1 dark:bg-dark-secondary">
            <div class="flex flex-wrap items-center text-center">
                <inertia-link
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-indigo-600 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    :href="'/logs?identifier=' + player.licenseIdentifier"
                >
                    <i class="mr-1 fas fa-toilet-paper"></i>
                    {{ t('players.show.logs') }}
                </inertia-link>
                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-gray-800 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    target="_blank"
                    :href="player.steamProfileUrl"
                    v-if="player.steamProfileUrl"
                >
                    <i class="mr-1 fab fa-steam"></i>
                    {{ t('players.show.steam') }}
                </a>
                <button
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-indigo-600 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    @click="showAntiCheat"
                >
                    <i class="mr-1 fas fa-bullseye"></i>
                    <span>
                        {{ t('players.show.anti_cheat') }}
                    </span>
                </button>
            </div>
            <div class="flex flex-wrap items-center text-center">
                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-blue-800 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    v-if="player.discord.length > 0"
                    href="#"
                    @click="showDiscord($event)"
                >
                    <i class="mr-1 fab fa-discord"></i>
                    {{ t('players.show.discord_accounts', player.discord.length) }}
                </a>

                <button
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-indigo-600 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    @click="showLinked"
                >
                    <span>
                        {{ t('players.show.linked') }}
                    </span>
                </button>
            </div>
        </v-section>

        <!-- Characters -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.characters.characters') }}

                    <!-- Hiding deleted characters on click -->
                    <button class="px-3 py-2 font-semibold text-white rounded bg-gray-400 text-base float-right"
                            @click="hideDeleted">
                        <span v-if="isShowingDeletedCharacters">
                            <i class="fas fa-eye-slash"></i>
                            {{ t('players.characters.hide') }}
                        </span>
                        <span v-else>
                            <i class="fas fa-eye"></i>
                            {{ t('players.characters.show') }}
                        </span>
                    </button>
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 xl:grid-cols-2 3xl:grid-cols-3 gap-9">
                    <card
                        v-for="(character) in characters"
                        :key="character.id"
                        v-bind:deleted="character.characterDeleted"
                        class="relative"
                        :class="{ 'shadow-lg' : status.character === character.id }"
                    >
                        <template #header>
                            <div class="flex justify-between">
                                <div class="flex-shrink-0">
                                    <img class="w-32 h-32 rounded-3xl" src="/images/loading.svg" :data-lazy="character.mugshot" v-if="character.mugshot" />
                                    <img class="w-32 h-32 rounded-3xl" src="/images/no_mugshot.png" v-else :title="t('players.characters.no_mugshot')" />
                                </div>
                                <div class="w-full">
                                    <h3 class="mb-2">
                                        {{ character.name }} (#{{ character.id }})
                                    </h3>
                                    <h4 class="text-primary dark:text-dark-primary"
                                        :title="t('players.characters.created', $moment(character.characterCreationTimestamp).format('l'))">
                                        {{ t('players.characters.born') }} {{ $moment(character.dateOfBirth).format('l') }}
                                    </h4>
                                    <h4 class="text-red-700 dark:text-red-300" v-if="character.characterDeleted">
                                        {{ t('players.edit.deleted') }} {{ $moment(character.characterDeletionTimestamp).format('l') }}
                                    </h4>
                                    <h4 class="text-gray-700 dark:text-gray-300 text-sm italic font-mono mt-1">
                                        {{ pedModel(character.pedModelHash) }}
                                        <span v-if="character.danny !== false" :title="t('players.new.danny_percentage')">
                                            ({{ (character.danny * 100).toFixed(1) }}%)
                                        </span>
                                    </h4>
                                    <h4 class="text-gray-700 dark:text-gray-300 text-xs italic font-mono mt-1" v-if="character.playtime" :title="t('players.characters.playtime')">
                                        {{ formatSecondDiff(character.playtime) }}
                                    </h4>
                                </div>
                            </div>
                        </template>

                        <template>
                            <p class="break-words">
                                {{ character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + (player.overrideLicense ? player.overrideLicense : player.licenseIdentifier) + '/characters/' + character.id + '/edit?returnTo=' + player.licenseIdentifier">
                                {{ t('global.view') }}
                            </inertia-link>
                            <div class="flex justify-between flex-wrap">
                                <button
                                    class="block w-full px-4 py-3 2xl:w-split text-center text-white mt-3 bg-warning dark:bg-dark-warning rounded"
                                    v-if="status.status === 'online' && status.character === character.id"
                                    @click="form.unload.character = character.id; isUnloading = true">
                                    <i class="fas fa-bolt mr-1"></i>
                                    {{ t('players.show.unload') }}
                                </button>
                                <inertia-link
                                    class="block w-full px-4 py-3 text-center text-white mt-3 bg-blue-600 dark:bg-blue-400 rounded"
                                    :class="{ '2xl:w-split' : status.status === 'online' && status.character === character.id }"
                                    :href="'/inventories/character/' + character.id"
                                    v-if="!character.characterDeleted"
                                >
                                    <i class="fas fa-briefcase mr-1"></i>
                                    {{ t('inventories.view') }}
                                </inertia-link>
                                <inertia-link
                                    class="block px-2 py-1 text-center text-white absolute top-1 right-1 bg-blue-600 dark:bg-blue-400 rounded"
                                    :href="'/inventory/character-' + character.id + ':1'"
                                    :title="t('inventories.show_inv')"
                                >
                                    <i class="fas fa-box"></i>
                                </inertia-link>
                                <button
                                    class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute top-1 left-1 bg-green-500 dark:bg-green-400 rounded"
                                    :title="t('players.characters.loaded')"
                                    v-if="status.character === character.id"
                                >
                                    <i class="fas fa-plug"></i>
                                </button>
                                <button
                                    class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute top-1 left-1 bg-red-500 dark:bg-red-400 rounded"
                                    v-if="character.isDead"
                                    :class="{'left-10' : status.character === character.id}"
                                >
                                    <i class="fas fa-skull-crossbones"></i>
                                </button>

                                <button
                                    class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute top-1 left-1 bg-red-500 dark:bg-red-400 rounded"
                                    v-if="character.isDead"
                                    :class="{'left-10' : status.character === character.id}"
                                >
                                    <i class="fas fa-skull-crossbones"></i>
                                </button>

                                <button
                                    class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute font-bold top-1 right-10 bg-pink-500 dark:bg-pink-400 rounded"
                                    v-if="character.gender === 1"
                                    :title="t('players.characters.is_female')"
                                >
                                    <i class="fas fa-female"></i>
                                </button>
                                <button
                                    class="block px-2 cursor-default w-ch-button py-1 text-center text-white absolute font-bold top-1 right-10 bg-blue-600 dark:bg-blue-300 rounded"
                                    v-if="character.gender === 0"
                                    :title="t('players.characters.is_male')"
                                >
                                    <i class="fas fa-male"></i>
                                </button>

                                <inertia-link
                                    class="block w-full px-4 py-3 text-center text-white mt-3 bg-red-600 dark:bg-red-400 rounded"
                                    href="#"
                                    @click="deleteCharacter($event, character.id)"
                                    v-if="!character.characterDeleted && $page.auth.player.isSuperAdmin"
                                >
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    {{ t('players.characters.delete') }}
                                </inertia-link>
                            </div>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="characters.length === 0">
                    {{ t('players.characters.none') }}
                </p>
            </template>
        </v-section>

        <!-- Warnings -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.show.warnings') }}
                    <select class="inline-block ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                            id="warningFilter" @change="filterWarnings">
                        <option value="all" selected>{{ t('global.all') }}</option>
                        <option value="strike">{{ t('players.show.warning_type.strike') }}</option>
                        <option value="warning">{{ t('players.show.warning_type.warning') }}</option>
                        <option value="note">{{ t('players.show.warning_type.note') }}</option>
                        <option value="system">{{ t('players.show.warning_type.system') }}</option>
                        <option value="hidden">{{ t('players.show.warning_type.hidden') }}</option>
                    </select>
                </h2>
            </template>

            <template>
                <card
                    v-for="(warning) in filteredWarnings"
                    :key="warning.id"
                    class="relative"
                >
                    <template #header>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <h4>
                                    {{ warning.issuer.playerName }}
                                    -
                                    <span v-html="wrapWarningType(warning.warningType)"></span>
                                </h4>
                            </div>
                            <div class="flex items-center">
                                <span class="text-muted dark:text-dark-muted">
                                    {{ warning.createdAt | formatTime }}
                                </span>
                                <sup class="ml-2 italic text-sm text-gray-600 dark:text-gray-400"
                                     v-if="warning.updatedAt !== warning.createdAt"
                                     :title="t('players.show.warning_edited_title', formatTime(warning.updatedAt))">
                                    {{ t('players.show.warning_edited') }}
                                </sup>
                                <button
                                    class="px-3 py-1 ml-4 text-sm font-semibold text-white bg-yellow-500 rounded"
                                    @click="warningEditId = warning.id"
                                    v-if="warningEditId !== warning.id && $page.auth.player.licenseIdentifier === warning.issuer.licenseIdentifier && warning.warningType !== 'system'"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    class="px-3 py-1 ml-4 text-sm font-semibold text-white bg-success dark:bg-dark-success rounded"
                                    @click="editWarning(warning.id, warning.warningType)"
                                    v-if="warningEditId === warning.id"
                                >
                                    <i class="fas fa-save"></i>
                                </button>
                                <button
                                    class="px-3 py-1 ml-4 text-sm font-semibold text-white bg-muted dark:bg-dark-muted rounded"
                                    @click="warningEditId = 0"
                                    v-if="warningEditId === warning.id"
                                >
                                    <i class="fas fa-ban"></i>
                                </button>
                                <inertia-link
                                    class="px-3 py-1 ml-4 text-sm font-semibold text-white bg-red-500 rounded hover:bg-red-600"
                                    method="DELETE"
                                    v-bind:href="'/players/' + player.licenseIdentifier + '/warnings/' + warning.id"
                                    v-if="warning.canDelete || $page.auth.player.isSeniorStaff">
                                    <i class="fas fa-trash"></i>
                                </inertia-link>
                            </div>
                        </div>
                    </template>

                    <template>
                        <p class="text-muted dark:text-dark-muted" v-if="warningEditId !== warning.id">
                            <span class="whitespace-pre-line"
                                  v-html="formatWarning(warning.message)"></span>
                        </p>
                        <textarea class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600"
                                  :id="'warning_' + warning.id" v-else-if="warningEditId === warning.id">{{ warning.message }}</textarea>
                    </template>
                </card>
                <p class="text-muted dark:text-dark-muted" v-if="filteredWarnings.length === 0">
                    {{ t('players.show.no_warnings') }}
                </p>
            </template>

            <template #footer>
                <h3 class="mb-2">
                    {{ t('players.warning.give') }}
                </h3>
                <form @submit.prevent="submitWarning">
                    <label for="message"></label>
                    <textarea
                        class="w-full p-5 mb-5 bg-gray-200 rounded shadow dark:bg-gray-600"
                        id="message"
                        name="message"
                        rows="5"
                        :placeholder="t('players.warning.placeholder', player.playerName)"
                        v-model="form.warning.message"
                        required
                    >
                    </textarea>

                    <button class="px-5 py-2 font-semibold text-white bg-red-500 dark:bg-red-500 rounded"
                            @click="form.warning.warning_type = 'strike'" type="submit">
                        <i class="mr-1 fas fa-bolt"></i>
                        {{ t('players.warning.do_strike') }}
                    </button>
                    <button class="px-5 py-2 ml-2 font-semibold text-white bg-yellow-600 rounded"
                            @click="form.warning.warning_type = 'warning'" type="submit">
                        <i class="mr-1 fas fa-exclamation-triangle"></i>
                        {{ t('players.warning.do_warn') }}
                    </button>
                    <button class="px-5 py-2 ml-2 font-semibold text-white bg-yellow-400 dark:bg-yellow-500 rounded"
                            @click="form.warning.warning_type = 'note'" type="submit">
                        <i class="mr-1 fas fa-sticky-note"></i>
                        {{ t('players.warning.do_note') }}
                    </button>
                    <button class="px-5 py-2 ml-2 font-semibold text-white bg-pink-400 dark:bg-pink-500 rounded"
                            @click="form.warning.warning_type = 'hidden'" type="submit"
                            v-if="$page.auth.player.isSeniorStaff">
                        <i class="mr-1 fas fa-eye-slash"></i>
                        {{ t('players.warning.do_hidden_note') }}
                    </button>
                </form>
            </template>
        </v-section>

        <!-- Screenshots -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('screenshot.screenshots') }}
                </h2>
            </template>

            <template>
                <div class="flex justify-center items-center my-6 mt-12" v-if="!haveScreenshotsLoaded">
                    <div>
                        <i class="fas fa-cog animate-spin"></i>
                        {{ t('global.loading') }}
                    </div>
                </div>
                <table class="w-full whitespace-no-wrap" v-else>
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('screenshot.screenshot') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.note') }}</th>
                        <th class="px-6 py-4">{{ t('screenshot.created_at') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4"
                        v-for="screenshot in sortedScreenshots"
                        :key="screenshot.system ? screenshot.url : screenshot.filename">
                        <td class="px-6 py-3 border-t mobile:block" v-if="screenshot.system">
                            <a :href="screenshot.url" target="_blank" v-if="screenshot.url.endsWith('.jpg') || screenshot.url.endsWith('.png') || screenshot.url.endsWith('.jpeg')"
                               class="text-indigo-600 dark:text-indigo-400">{{ t('screenshot.view') }}</a>
                            <a :href="screenshot.url" target="_blank" v-else
                               class="text-indigo-600 dark:text-indigo-400">{{ t('screenshot.view_capture') }}</a>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" v-else>
                            <a :href="'/export/screenshot/' + screenshot.filename" target="_blank"
                               class="text-indigo-600 dark:text-indigo-400">{{ t('screenshot.view') }}</a>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <i class="fas fa-cogs mr-1" v-if="screenshot.system"></i>
                            {{ screenshot.note || 'N/A' }}
                            <a :href="cheatDocs(screenshot.note)" target="_blank"
                                v-if="cheatDocs(screenshot.note)"
                                class="text-yellow-600 dark:text-yellow-400 font-semibold"
                                :title="t('screenshot.documentation')">?</a>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" v-if="screenshot.created_at">
                            {{ screenshot.created_at * 1000 | formatTime(true) }}
                        </td>
                        <td class="px-6 py-3 border-t mobile:block" v-else>{{ t('global.unknown') }}</td>
                    </tr>
                    <tr v-if="sortedScreenshots.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('screenshot.no_screenshots') }}
                        </td>
                    </tr>
                </table>
            </template>

        </v-section>

        <!-- Panel Logs -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.show.panel_logs') }}
                </h2>
            </template>

            <template>
                <div class="flex justify-center items-center my-6 mt-12" v-if="!havePanelLogsLoaded">
                    <div>
                        <i class="fas fa-cog animate-spin"></i>
                        {{ t('global.loading') }}
                    </div>
                </div>
                <table class="w-full whitespace-no-wrap" v-else>
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('logs.action') }}</th>
                        <th class="px-6 py-4">{{ t('logs.timestamp') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="log in panelLogs"
                        :key="log.id">
                        <td class="px-6 py-3 border-t mobile:block">{{ log.log }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ log.timestamp | formatTime(true) }}</td>
                    </tr>
                    <tr v-if="panelLogs.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('players.show.no_panel_logs') }}
                        </td>
                    </tr>
                </table>
            </template>

        </v-section>

        <!-- Screenshot -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k"
             v-if="isScreenshot && this.perm.check(this.perm.PERM_SCREENSHOT)">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded"
                :class="continuouslyScreenshotting ? 'w-vlarge-alert' : 'w-alert'">
                <h3 class="mb-2">
                    {{ t('map.screenshot') }}
                    <span v-if="nextContinuousScreenshot > 0 && continuouslyScreenshotting"> - {{ nextContinuousScreenshot.toFixed(1) }}s</span>
                </h3>

                <p v-if="screenshotError" class="text-danger dark:text-dark-danger font-semibold mb-3">
                    {{ screenshotError }}
                </p>

                <div class="relative min-h-50">
                    <a v-if="screenshotImage && !screenshotError" class="w-full"
                       :class="{'blur-sm' : isScreenshotLoading && !continuouslyScreenshotting}" :href="screenshotImage" target="_blank">
                        <img :src="screenshotImage" alt="Screenshot" class="w-full"/>
                    </a>

                    <div class="flex justify-center absolute left-0 w-full top-1/2 transform -translate-y-1/2"
                        v-if="isScreenshotLoading && !continuouslyScreenshotting">
                        <i class="fas fa-cog animate-spin text-3xl"></i>
                    </div>
                </div>
                <p v-if="screenshotImage" class="mt-3 text-sm">
                    {{ t('map.screenshot_description') }}
                </p>

                <!-- Buttons -->
                <div class="flex justify-end mt-2">
                    <button class="px-5 py-2 rounded bg-warning dark:bg-dark-warning mr-2"
                            @click="isAttachingScreenshot = true"
                            v-if="screenshotImage && screenshotLicense">
                        {{ t('screenshot.attach') }}
                    </button>

                    <button class="px-5 py-2 rounded bg-primary dark:bg-dark-primary mr-2"
                            @click="startContinuousScreenshot()"
                            v-if="!continuouslyScreenshotting && !isScreenshotLoading">
                        {{ t('screenshot.continuous') }}
                    </button>
                    <button class="px-5 py-2 rounded bg-danger dark:bg-dark-danger mr-2"
                            @click="stopContinuousScreenshot()"
                            v-else-if="continuouslyScreenshotting">
                        <i class="fas fa-cog animate-spin mr-1" v-if="isScreenshotLoading"></i>

                        {{ t('screenshot.continuous_stop') }}
                    </button>

                    <button class="px-5 py-2 rounded bg-success dark:bg-dark-success mr-2"
                            @click="createScreenshot()"
                            v-if="!isScreenshotLoading && !continuouslyScreenshotting">
                        {{ t('global.refresh') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            v-if="!continuouslyScreenshotting"
                            @click="isScreenshot = false; screenshotImage = null; screenshotError = null; screenshotLicense = null">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Screen capture -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k"
             v-if="isScreenCapture && this.perm.check(this.perm.PERM_SCREENSHOT)">
            <div
                class="shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-6 rounded"
                :class="screenCaptureVideo ? 'w-large-alert' : 'w-alert'"
            >
                <h3 class="mb-2">
                    {{ t('screenshot.screencapture') }}
                </h3>

                <!-- Duration -->
                <div class="w-full p-3 flex justify-between px-0" v-if="!screenCaptureStatus && !screenCaptureVideo">
                    <label class="mr-4 block w-1/4 pt-2 font-bold" for="capture_duration">
                        {{ t('screenshot.capture_duration') }}
                    </label>
                    <input class="w-3/4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="capture_duration"
                           min="3" max="30" type="number"
                           v-model="captureData.duration"/>
                </div>

                <p v-if="screenCaptureError" class="text-danger dark:text-dark-danger font-semibold mb-3">
                    {{ screenCaptureError }}
                </p>

                <div class="relative min-h-50">
                    <video class="w-full" controls v-if="screenCaptureVideo">
                        <source :src="screenCaptureVideo" type="video/webm">
                    </video>

                    <div class="w-full" v-if="screenCaptureStatus === 'capturing'">
                        <span class="text-sm block mb-1">{{
                                t('screenshot.capturing', Math.ceil(captureRemaining / 10))
                            }}</span>
                        <div class="bg-green-700 dark:bg-green-400"
                             :style="`height: 4px; width: ${(1 - (captureRemaining / (captureData.duration * 10))) * 100}%`"></div>
                    </div>

                    <div
                        class="flex justify-center absolute left-0 w-full top-1/2 transform -translate-y-1/2 flex-wrap"
                        v-if="screenCaptureStatus === 'processing'">
                        <i class="fas fa-cog animate-spin text-3xl"></i>
                        <span class="text-sm block mt-1 text-center w-full">{{ t('screenshot.processing') }}</span>
                    </div>
                </div>

                <p v-if="screenCaptureStatus === 'processing'" class="mt-3 text-sm">
                    {{ t('screenshot.processing_description') }}
                </p>

                <p v-if="screenCaptureVideo" class="mt-3 text-sm">
                    {{ t('map.screecapture_description') }}
                </p>

                <!-- Buttons -->
                <div class="flex justify-end mt-2">
                    <a v-if="screenCaptureVideo" :href="screenCaptureVideo" target="_blank"
                       class="px-5 py-2 rounded bg-primary dark:bg-dark-primary mr-2">
                        {{ t('global.download') }}
                    </a>
                    <button class="px-5 py-2 rounded bg-primary dark:bg-dark-primary mr-2"
                            @click="createScreenCapture()"
                            v-if="!screenCaptureStatus && !screenCaptureVideo">
                        {{ t('global.create') }}
                    </button>
                    <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500"
                            v-if="!screenCaptureStatus"
                            @click="isScreenCapture = false; captureData.duration = 5; screenCaptureVideo = false; screenCaptureError = false">
                        {{ t('global.close') }}
                    </button>
                </div>
            </div>
        </div>

        <ScreenshotAttacher :close="screenshotAttached" :license="screenshotLicense" :url="screenshotImage"
                            v-if="isAttachingScreenshot"/>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Badge from './../../Components/Badge';
import Alert from './../../Components/Alert';
import Card from './../../Components/Card';
import Avatar from './../../Components/Avatar';
import ScreenshotAttacher from './../../Components/ScreenshotAttacher';
import Modal from './../../Components/Modal';

import models from '../../data/ped_models.js';

import hljs from 'highlight.js';

import json from 'highlight.js/lib/languages/json';

hljs.registerLanguage('json', json);

import 'highlight.js/styles/github-dark-dimmed.css';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        Alert,
        Card,
        Avatar,
        ScreenshotAttacher,
        Modal,
    },
    props: {
        player: {
            type: Object,
            required: true,
        },
        characters: {
            type: Array,
            required: true,
        },
        warnings: {
            type: Array,
            required: true,
        },
        tags: {
            type: Array,
            required: true,
        },
        enablableCommands: {
            type: Array,
            required: true,
        },
        kickReason: {
            type: String
        },
        whitelisted: {
            type: Boolean
        },
        blacklisted: {
            type: Boolean
        },
        allowRoleEdit: {
            type: Boolean
        }
    },
    data() {
        let selectedRole;

        if (this.player.isSeniorStaff) {
            selectedRole = 'seniorStaff';
        } else if (this.player.isStaff) {
            selectedRole = 'staff';
        } else if (this.player.isTrusted) {
            selectedRole = 'trusted';
        } else {
            selectedRole = 'player';
        }

        const commands = this.enablableCommands.sort().map(c => {
            return {
                name: c,
                enabled: this.player.enabledCommands.includes(c),
            };
        });

        return {
            local: {
                played: this.t('players.show.played', this.$options.filters.humanizeSeconds(this.player.playTime)),
                ban: this.localizeBan(),
                ban_warning: this.t('players.ban.ban_warning')
            },
            isBanning: false,
            isKicking: false,
            isStaffPM: false,
            isTempBanning: false,
            isTempSelect: true,
            warningEditId: 0,
            filteredWarnings: this.warnings,
            form: {
                ban: {
                    reason: null,
                    expire: null,
                    expireDate: null,
                    expireTime: null,
                },
                kick: {
                    reason: null,
                },
                pm: {
                    message: null,
                },
                warning: {
                    message: null,
                    warning_type: null,
                },
                unload: {
                    message: this.t('players.show.unload_default'),
                    character: null
                }
            },
            isShowingDeletedCharacters: false,
            isUnloading: false,
            isShowingLinked: false,
            isShowingLinkedLoading: false,
            linkedAccounts: {
                total: 0,
                linked: []
            },

            commands: commands,
            isEnablingCommands: false,

            isShowingDiscord: false,
            isShowingDiscordLoading: false,
            discordAccounts: [],

            isShowingAntiCheat: false,
            isShowingAntiCheatLoading: false,
            antiCheatEvents: [],

            antiCheatMetadata: false,
            antiCheatMetadataJSON: '',


            isTagging: false,
            tagCategory: this.player.tag ? this.player.tag : 'custom',
            tagCustom: '',

            isRoleEdit: false,
            selectedRole: selectedRole,

            isConfirmingUnban: false,

            isScreenCapture: false,
            screenCaptureStatus: false,
            screenCaptureVideo: false,
            screenCaptureError: false,
            captureRemaining: false,
            captureData: {
                duration: 5
            },

            nextContinuousScreenshot: 0,
            continuousScreenshotThread: false,
            continuouslyScreenshotting: false,

            isScreenshot: false,
            isScreenshotLoading: false,
            screenshotImage: null,
            screenshotLicense: null,
            screenshotError: null,
            isAttachingScreenshot: false,

            haveScreenshotsLoaded: false,
            sortedScreenshots: [],

            havePanelLogsLoaded: false,
            panelLogs: [],

            statusLoading: true,
            status: {}
        }
    },
    methods: {
        formatSecondDiff(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        showAntiCheatMetadata(event, eventMetadata) {
            event.preventDefault();

            this.antiCheatMetadata = true;
            this.antiCheatMetadataJSON = hljs.highlight(JSON.stringify(eventMetadata, null, 4), {language: 'json'}).value;
        },
        getPlayerMetadata() {
            const statusMetadata = this.status?.metadata;

            if (!statusMetadata) {
                return false;
            }

            const metadata = Object.keys(statusMetadata).map(key => {
                return statusMetadata[key] ? this.t("players.show.meta_" + key) : false;
            }).filter(Boolean);

            return metadata.length ? metadata.join(', ') : false;
        },
        async unbanPlayer() {
            if (!this.player.ban.issuer && !this.isConfirmingUnban) {
                this.isConfirmingUnban = true;

                return;
            }

            this.isConfirmingUnban = false;

            // Send request.
            await this.$inertia.delete('/players/' + this.player.licenseIdentifier + '/bans/' + this.player.ban.id);
        },
        async updateCommands() {
            this.isEnablingCommands = false;

            const enabledCommands = this.commands.filter(c => c.enabled).map(c => c.name);

            // Send request.
            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/updateEnabledCommands', {
                enabledCommands: enabledCommands,
            });
        },
        cheatDocs(pNote) {
            pNote = pNote.trim();

            if (pNote.startsWith('Anti-Cheat: Invalid Damage Modifier')) {
                return '/cheat/damage_modifier';
            }

            return false;
        },
        async loadScreenshots() {
            try {
                const data = await axios.get('/players/' + this.player.licenseIdentifier + '/screenshots');

                if (data.data && data.data.status) {
                    const screenshots = data.data.data;

                    const sortedScreenshots = screenshots.sort((a, b) => b.created_at - a.created_at);

                    this.sortedScreenshots = sortedScreenshots;
                }
            } catch (e) {
            }

            this.haveScreenshotsLoaded = true;
        },
        async loadPanelLogs() {
            try {
                const data = await axios.get('/players/' + this.player.licenseIdentifier + '/panelLogs');

                if (data.data && data.data.status) {
                    this.panelLogs = data.data.data;
                }
            } catch (e) {
            }

            this.havePanelLogsLoaded = true;
        },
        async loadStatus() {
            this.statusLoading = true;

            try {
                const data = await axios.get('/players/' + this.player.licenseIdentifier + '/status');

                if (data.data && data.data.status) {
                    this.status = data.data.data;
                    this.statusLoading = false;

                    return;
                }
            } catch (e) {
            }

            this.status = {
                status: "unavailable"
            };
            this.statusLoading = false;
        },
        async createScreenCapture() {
            if (this.screenCaptureStatus) {
                return;
            }

            if (!Number.isInteger(this.captureData.duration) && this.captureData.duration < 3 && this.captureData.duration > 30) {
                alert(this.t("screenshot.invalid_duration"));

                return;
            }

            let interval = setInterval(() => {
                this.captureRemaining--;

                if (this.captureRemaining === 0) {
                    this.screenCaptureStatus = "processing";

                    clearInterval(interval);
                }
            }, 100);

            this.captureRemaining = this.captureData.duration * 10;
            this.screenCaptureStatus = "capturing";

            try {
                const result = await axios({
                    method: 'post',
                    url: '/api/capture/' + this.status.serverName + '/' + this.status.serverId + '/' + this.captureData.duration,
                    timeout: this.captureData.duration + 20000
                });

                clearInterval(interval);

                if (result.data) {
                    if (result.data.status) {
                        console.info('Screen capture of ID ' + this.status.serverId, result.data.data.url, result.data.data.license);

                        this.screenCaptureVideo = result.data.data.url;
                    } else {
                        this.screenshotError = result.data.message ? result.data.message : this.t('screenshot.screencapture_failed');
                    }
                }
            } catch (e) {
                clearInterval(interval);

                this.screenCaptureError = this.t('screenshot.screencapture_failed');
            }

            this.screenCaptureStatus = false;
        },
        stopContinuousScreenshot() {
            this.continuouslyScreenshotting = false;
        },
        startContinuousScreenshot() {
            if (this.continuousScreenshotThread) {
                return;
            }

            this.continuouslyScreenshotting = true;
            this.continuousScreenshotThread = true;

            const wait = ms => new Promise(resolve => setTimeout(resolve, ms));

            const doScreenshot = () => {
                this.createScreenshot(async success => {
                    if (!success || !this.continuouslyScreenshotting) {
                        this.continuouslyScreenshotting = false;
                        this.continuousScreenshotThread = false;

                        return;
                    }

                    this.nextContinuousScreenshot = 3.0;

                    while (this.nextContinuousScreenshot > 0) {
                        await wait(100);

                        this.nextContinuousScreenshot -= 0.1;

                        if (!this.continuouslyScreenshotting) {
                            this.continuousScreenshotThread = false;

                            return;
                        }
                    }

                    doScreenshot();
                });
            };

            doScreenshot();
        },
        async createScreenshot(cb, shortLifespan) {
            if (this.isScreenshotLoading) {
                cb && cb(false);

                return;
            }
            this.isScreenshotLoading = true;
            this.screenshotError = null;

            this.screenshotLicense = null;

            try {
                const result = await axios.post('/api/screenshot/' + this.status.serverName + '/' + this.status.serverId + (shortLifespan ? '?short=1' : ''));
                this.isScreenshotLoading = false;

                if (result.data) {
                    if (result.data.status) {
                        console.info('Screenshot of ID ' + this.status.serverId, result.data.data.url, result.data.data.license);

                        this.screenshotImage = result.data.data.url;
                        this.screenshotLicense = result.data.data.license;

                        cb && cb(true);
                    } else {
                        this.screenshotError = result.data.message ? result.data.message : this.t('map.screenshot_failed');

                        cb && cb(false);
                    }
                }
            } catch (e) {
                this.screenshotError = this.t('map.screenshot_failed');

                this.isScreenshotLoading = false;

                cb && cb(false);
            }
        },
        screenshotAttached(status, message) {
            this.isAttachingScreenshot = false;

            if (message) {
                alert(message);
            }

            if (status) {
                this.isScreenshot = false;
                this.screenshotImage = null;
                this.screenshotError = null;
                this.screenshotLicense = null;
            }
        },
        async showDiscord(e) {
            e.preventDefault();
            this.isShowingDiscordLoading = true;
            this.isShowingDiscord = true;

            this.discordAccounts = [];

            try {
                const data = await axios.get('/players/' + this.player.licenseIdentifier + '/discord');

                if (data.data && data.data.status) {
                    const accounts = data.data.data;

                    this.discordAccounts = accounts;
                } else {
                    this.discordAccounts = [];
                }
            } catch (e) {
                this.discordAccounts = [];
            }

            this.isShowingDiscordLoading = false;
        },
        async showLinked() {
            this.isShowingLinkedLoading = true;
            this.isShowingLinked = true;

            this.linkedAccounts.total = 0;
            this.linkedAccounts.linked = [];

            try {
                const data = await axios.get('/players/' + this.player.licenseIdentifier + '/linked');

                if (data.data && data.data.status) {
                    const linked = data.data.data;

                    this.linkedAccounts.total = linked.total;
                    this.linkedAccounts.linked = linked.linked;
                } else {
                    this.linkedAccounts.total = 0;
                    this.linkedAccounts.linked = [];
                }
            } catch (e) {
                this.linkedAccounts.total = 0;
                this.linkedAccounts.linked = [];
            }

            this.isShowingLinkedLoading = false;
        },
        async showAntiCheat() {
            this.isShowingAntiCheatLoading = true;
            this.isShowingAntiCheat = true;

            this.antiCheatEvents = [];

            try {
                const data = await axios.get('/players/' + this.player.licenseIdentifier + '/antiCheat');

                if (data.data && data.data.status) {
                    this.antiCheatEvents = data.data.data;
                }
            } catch (e) {}

            this.isShowingAntiCheatLoading = false;
        },
        wrapWarningType(type) {
            const label = this.t('players.show.warning_type.' + type);

            switch (type) {
                case 'strike':
                    return '<span class="italic text-red-500"><i class="fas fa-bolt"></i> ' + label + '</span>';
                case 'warning':
                    return '<span class="italic text-yellow-600"><i class="fas fa-exclamation-triangle"></i> ' + label + '</span>';
                case 'note':
                    return '<span class="italic text-yellow-400"><i class="fas fa-sticky-note"></i> ' + label + '</span>';
                case 'system':
                    return '<span class="italic text-blue-500"><i class="fas fa-robot"></i> ' + label + '</span>';
                case 'hidden':
                    return '<span class="italic text-pink-500"><i class="fas fa-eye-slash"></i> ' + label + '</span>';
            }

            return '';
        },
        filterWarnings() {
            const filter = $('#warningFilter').val();

            this.filteredWarnings = this.warnings.filter((w) => !filter || filter === 'all' || w.warningType === filter);
        },
        pedModel(hash) {
            if (!hash) {
                return 'unknown';
            }

            // convert signed to unsigned
            const checkHash = Uint32Array.from(Int32Array.of(hash))[0];

            for (let x = 0; x < models.length; x++) {
                const name = models[x],
                    calcHash = this.joaat(name);

                if (calcHash === checkHash || Uint32Array.from(Int32Array.of(calcHash))[0] === checkHash) {
                    return name;
                }
            }

            return hash;
        },
        joaat(key) {
            let hash = 0;
            for (let i = 0, length = key.length; i < length; i++) {
                hash += key.charCodeAt(i);
                hash += (hash << 10);
                hash ^= (hash >>> 6);
            }
            hash += (hash << 3);
            hash ^= (hash >>> 11);
            hash += (hash << 15);

            return hash;
        },
        localizeBan() {
            if (!this.player.ban) {
                return '';
            }
            return this.player.ban.expireAt
                ? this.t('players.show.ban', this.formatBanCreator(this.player.ban.issuer), this.$options.filters.formatTime(this.player.ban.expireAt))
                : this.t('players.ban.forever', this.formatBanCreator(this.player.ban.issuer));
        },
        formatTime(t) {
            return this.$options.filters.formatTime(t);
        },
        async pmPlayer() {
            // Send request.
            await this.$inertia.post('/players/' + (this.player.overrideLicense ? this.player.overrideLicense : this.player.licenseIdentifier) + '/staffPM', this.form.pm);

            // Reset.
            this.isStaffPM = false;
            this.form.pm.message = null;
        },
        async removeSoftBan() {
            if (!confirm(this.t('players.show.soft_ban_confirm'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/updateSoftBanStatus/0');
        },
        async addSoftBan() {
            if (!confirm(this.t('players.show.soft_ban_confirm'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/updateSoftBanStatus/1');
        },
        async removeTag() {
            this.isTagging = false;

            // Send request.

            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/updateTag', {
                tag: false
            });
        },
        async updateRole() {
            this.isRoleEdit = false;

            // Send request.
            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/updateRole', {
                role: this.selectedRole ? this.selectedRole : 'player'
            });
        },
        async addTag() {
            const tag = this.tagCategory === 'custom' ? this.tagCustom.trim() : this.tagCategory;

            if (!tag) {
                return;
            }

            this.isTagging = false;

            // Send request.

            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/updateTag', {
                tag: tag
            });
        },
        async kickPlayer() {
            if (!confirm(this.t('players.show.kick_confirm'))) {
                this.isKicking = false;
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + (this.player.overrideLicense ? this.player.overrideLicense : this.player.licenseIdentifier) + '/kick', this.form.kick);

            // Reset.
            this.isKicking = false;
            this.form.kick.reason = null;
        },
        async revivePlayer() {
            if (!confirm(this.t('players.show.revive_confirm'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + (this.player.overrideLicense ? this.player.overrideLicense : this.player.licenseIdentifier) + '/revivePlayer');
        },
        async unloadCharacter() {
            if (!confirm(this.t('players.show.unload_confirm'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + (this.player.overrideLicense ? this.player.overrideLicense : this.player.licenseIdentifier) + '/unloadCharacter', this.form.unload);

            this.form.unload.message = this.t('players.show.unload_default');
            this.form.unload.character = null;
            this.isUnloading = false;
        },
        async deleteCharacter(e, characterId) {
            e.preventDefault();

            if (!confirm(this.t('players.show.delete_character'))) {
                return;
            }

            // Send request.
            await this.$inertia.delete('/players/' + this.player.licenseIdentifier + '/characters/' + characterId);
        },
        async submitBan() {
            // Default expiration.
            let expire = null;

            // Calculate expire relative to now in seconds if temp ban.
            if (this.isTempBanning) {
                const nowUnix = this.$moment().unix();

                if (this.isTempSelect) {
                    const expireUnix = this.$moment(this.form.ban.expireDate + ' ' + this.form.ban.expireTime).unix();
                    expire = expireUnix - nowUnix;
                } else {
                    let val = parseInt($('#ban-value').val());

                    if (val <= 0) {
                        return;
                    }

                    switch ($('#ban-type').val()) {
                        case 'hour':
                            val *= 60 * 60;
                            break;
                        case 'day':
                            val *= 60 * 60 * 24;
                            break;
                        case 'week':
                            val *= 60 * 60 * 24 * 7;
                            break;
                        case 'month':
                            val *= 60 * 60 * 24 * 7 * 30;
                            break;
                        default:
                            return;
                    }

                    expire = val;
                }
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/bans', {...this.form.ban, expire});

            this.local.ban = this.localizeBan();
            this.filterWarnings();

            // Reset.
            this.isBanning = false;
            this.isTempBanning = false;
            this.isTempSelect = true;
            this.form.ban.reason = null;
            this.form.ban.expire = null;
            this.form.ban.expireDate = null;
            this.form.ban.expireTime = null;
        },
        async submitWarning() {
            // Send request.
            await this.$inertia.post('/players/' + this.player.licenseIdentifier + '/warnings', this.form.warning);

            this.filterWarnings();

            // Reset.
            this.form.warning.message = null;
        },
        async editWarning(id, warningType) {
            // Send request.
            await this.$inertia.put('/players/' + this.player.licenseIdentifier + '/warnings/' + id, {
                message: $('#warning_' + id).val(),
                warning_type: warningType,
            });

            this.filterWarnings();

            // Reset.
            this.warningEditId = 0;
        },
        hideDeleted(e) {
            e.preventDefault();

            this.isShowingDeletedCharacters = !this.isShowingDeletedCharacters;
            if (this.isShowingDeletedCharacters) {
                $('.card-deleted').removeClass('hidden');
            } else {
                $('.card-deleted').addClass('hidden');
            }
        },
        copyLicense(e) {
            const _this = this,
                button = $(e.target).closest('.badge');

            this.copyToClipboard(this.player.licenseIdentifier);

            $('span', button).text(this.t('global.copied'));

            setTimeout(function () {
                $('span', button).text(_this.t('players.show.copy_license'));
            }, 1500);
        },
        copyText(e, text) {
            e.preventDefault();
            const button = $(e.target).closest('a');

            this.copyToClipboard(text)

            button.removeClass('bg-blue-800');
            button.addClass('bg-green-600');

            setTimeout(function () {
                button.removeClass('bg-green-600');
                button.addClass('bg-blue-800');
            }, 500);
        },
        urlify(text, callback) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, function (url) {
                return callback(url);
            });
        },
        formatWarning(warning) {
            warning = warning.replace(/(https?:\/\/(.+?)\/players\/)?(steam:\w{15})/gmi, (full, _ignore, host, steam) => {
                const url = full && full.startsWith("http") ? full : "/players/" + steam,
                    cluster = host ? host.split(".")[0].replace("localhost", "c1") : this.$page?.auth?.cluster;

                return `<a href="${url}" target="_blank" class="text-yellow-600 dark:text-yellow-400">${cluster.toLowerCase()}/${steam.toLowerCase()}</a>`;
            });

            warning = warning.replace(/(https?:\/\/(.+?)\/players\/)?(license:\w{40})/gmi, (full, _ignore, host, license) => {
                const url = full && full.startsWith("http") ? full : "/players/" + license,
                    cluster = host ? host.split(".")[0].replace("localhost", "c1") : this.$page?.auth?.cluster;

                return `<a href="${url}" target="_blank" class="text-yellow-600 dark:text-yellow-400">${cluster.toLowerCase()}/${license.toLowerCase()}</a>`;
            });

            return this.urlify(warning, function (url) {
                const ext = url.split(/[#?]/)[0].split('.').pop().trim();
                let extraClass = 'user-link';

                if (url.match(/(https?:\/\/(.+?)\/players\/)?(steam:\w{15}|license:\w{40})/gmi)) return url;

                switch (ext) {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'webp':
                    case 'gif':
                        extraClass = 'user-image';
                        break;
                    case 'mp4':
                    case 'mov':
                    case 'webm':
                    case 'avi':
                    case 'mkv':
                        extraClass = 'user-video';
                        break;
                }

                if (url.startsWith("https://www.twitch.tv/videos/")) {
                    extraClass = 'user-twitch';
                } else if (url.startsWith("https://tickettool.xyz/direct?url=")) {
                    extraClass = 'user-iframe';
                }

                return '<a href="' + url + '" target="_blank" class="text-indigo-600 dark:text-indigo-400 ' + extraClass + '">' + url + '</a>';
            });
        },
        viewImage(el, url) {
            $(el).replaceWith('<div class="user-close relative">' +
                '<a href="#" class="absolute top-0 left-0 z-10 bg-gray-100 text-gray-900 p-2" data-original="' + url + '">&#10006;</a>' +
                '<img class="block max-h-96 max-w-full" src="' + url + '" />' +
                '</div>');
        },
        viewVideo(el, url) {
            $(el).replaceWith('<div class="user-close relative">' +
                '<a href="#" class="absolute top-0 left-0 z-10 bg-gray-100 text-gray-900 p-2" data-original="' + url + '">&#10006;</a>' +
                '<video class="block max-h-96 max-w-full" controls autoplay><source src="' + url + '"></video>' +
                '</div>');
        },
        viewTwitch(el, url) {
            const rgx = /https:\/\/www\.twitch\.tv\/videos\/(\d+)(\?.*?t=(\w+))?/gmi,
                match = rgx.exec(url);

            if (!match || match.length < 2) return;

            const videoId = match[1],
                time = match[3] || '00h00m00s';

            const twitchUrl = 'https://player.twitch.tv/?video=' + videoId + '&t=' + time + '&parent=' + window.location.hostname;

            $(el).replaceWith('<div class="user-close relative">' +
                '<a href="#" class="absolute top-0 left-0 z-10 bg-gray-100 text-gray-900 p-2" data-original="' + url + '">&#10006;</a>' +
                '<iframe class="block h-96 w-iframe max-w-full" src="' + twitchUrl + '" frameborder="0" allowfullscreen="true" scrolling="no"></video>' +
                '</div>');
        },
        viewIFrame(el, url) {
            $(el).replaceWith('<div class="user-close relative">' +
                '<a href="#" class="absolute top-0 left-0 z-10 bg-gray-100 text-gray-900 p-2" data-original="' + url + '">&#10006;</a>' +
                '<iframe class="block h-96 w-iframe max-w-full" src="' + url + '" frameborder="0" allowfullscreen="true"></video>' +
                '</div>');
        },
        async removeIdentifier(identifier) {
            if (!confirm(this.t('players.show.identifier_remove'))) {
                return;
            }

            this.isShowingLinked = false;
            this.isShowingLinkedLoading = false;

            // Send request.
            await this.$inertia.delete('/players/' + this.player.licenseIdentifier + '/removeIdentifier/' + identifier);
        },
        formatBanCreator(creator) {
            if (!creator) {
                return this.t('global.system');
            }
            return creator;
        },
        asyncLoadImage(url) {
            return new Promise((resolve, reject) => {
                const img = new Image();

                img.onload = resolve;
                img.onerror = reject;

                img.src = url;
            });
        }
    },
    mounted() {
        if (this.kickReason) {
            this.isKicking = true;
            this.form.kick.reason = this.kickReason;
        }

        this.loadScreenshots();
        this.loadPanelLogs();
        this.loadStatus();

        const _this = this;

        $(document).ready(function () {
            $('body').on('click', 'a.user-image', function (e) {
                e.preventDefault();

                _this.viewImage(this, $(this).attr('href'));
            }).on('click', 'a.user-video', function (e) {
                e.preventDefault();

                _this.viewVideo(this, $(this).attr('href'));
            }).on('click', 'a.user-twitch', function (e) {
                e.preventDefault();

                _this.viewTwitch(this, $(this).attr('href'));
            }).on('click', 'a.user-iframe', function (e) {
                e.preventDefault();

                _this.viewIFrame(this, $(this).attr('href'));
            }).on('click', '.user-close a', function (e) {
                e.preventDefault();

                $(this).closest('.user-close').replaceWith(_this.formatWarning($(this).data('original')));
            });
        });

        this.$nextTick(() => {
            $("img[data-lazy]").each((i, img) => {
                const url = $(img).data("lazy");

                if (url.includes("screenshot-undefined")) {
                    $(img).attr("src", "/images/no_mugshot.png");

                    return;
                }

                this.asyncLoadImage(url).then(() => {
                    $(img).attr("src", url);
                }).catch(() => {
                    $(img).attr("src", "/images/no_mugshot.png");
                });
            });
        });

        $(document).on("visibilitychange", e => {
            if (document.visibilityState !== "visible") {
                this.continuouslyScreenshotting = false;
            }
        });
    }
};
</script>
