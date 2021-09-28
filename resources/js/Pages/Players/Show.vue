<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10 mobile:flex-wrap">
                <h1 class="dark:text-white">
                    {{ player.playerName }}
                </h1>
                <div
                    class="flex items-center space-x-5 mobile:flex-wrap mobile:w-full mobile:!mr-0 mobile:!ml-0 mobile:space-x-0">
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-if="player.isBanned">
                        <span class="font-semibold">{{ t('global.banned') }}</span>
                    </badge>
                    <badge class="border-purple-200 bg-purple-100 dark:bg-purple-700" v-if="player.isTrusted">
                        <span class="font-semibold">{{ t('global.trusted') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.isStaff">
                        <span class="font-semibold">{{ t('global.staff') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.isSuperAdmin">
                        <span class="font-semibold">{{ t('global.super') }}</span>
                    </badge>

                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale"
                           v-if="player.status.status === 'online'">
                        <span class="font-semibold">{{ t('global.status.online') }}
                            <sup>[{{ player.status.serverId }}]</sup>
                        </span>
                    </badge>
                    <badge class="border-red-200 bg-warning-pale dark:bg-dark-warning-pale"
                           v-else-if="player.status.status === 'unavailable'"
                           :title="t('global.status.unavailable_info')">
                        <span class="font-semibold">{{ t('global.status.unavailable') }}</span>
                    </badge>
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-else>
                        <span class="font-semibold">{{ t('global.status.' + player.status.status) }}</span>
                    </badge>

                    <badge class="border-gray-200 bg-secondary dark:bg-dark-secondary" :title="formatSecondDiff(player.playTime)" v-html="local.played">
                        {{ local.played }}
                    </badge>
                </div>
            </div>
            <p class="dark:text-dark-muted">
                {{ t('players.show.description') }}
            </p>
        </portal>

        <portal to="actions">
            <div>
                <!-- View on Map -->
                <inertia-link
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    :href="'/map#' + player.steamIdentifier" v-if="player.status.status === 'online'">
                    <i class="fas fa-envelope-open-text"></i>
                    {{ t('global.view_map') }}
                </inertia-link>
                <!-- StaffPM -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isStaffPM = true" v-if="player.status.status === 'online'">
                    <i class="fas fa-envelope-open-text"></i>
                    {{ t('players.show.staffpm') }}
                </button>
                <!-- Kicking -->
                <button
                    class="px-5 py-2 mr-3 font-semibold text-white rounded bg-yellow-600 dark:bg-yellow-500 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isKicking = true" v-if="player.status.status === 'online'">
                    <i class="fas fa-user-minus"></i>
                    {{ t('players.show.kick') }}
                </button>
                <!-- Edit Ban -->
                <inertia-link
                    class="px-5 py-2 font-semibold text-white rounded bg-yellow-500 mr-3 mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id + '/edit'"
                    v-if="player.isBanned">
                    <i class="mr-1 fas fa-edit"></i>
                    {{ t('players.show.edit_ban') }}
                </inertia-link>
                <!-- Unbanning -->
                <inertia-link
                    class="px-5 py-2 font-semibold text-white rounded bg-success dark:bg-dark-success mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    method="DELETE" v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id"
                    v-if="player.isBanned">
                    <i class="mr-1 fas fa-lock-open"></i>
                    {{ t('players.show.unban') }}
                </inertia-link>
                <!-- Banning -->
                <button
                    class="px-5 py-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger mobile:block mobile:w-full mobile:m-0 mobile:mb-3"
                    @click="isBanning = true" v-else>
                    <i class="mr-1 fas fa-gavel"></i>
                    {{ t('players.show.issue') }}
                </button>
            </div>
        </portal>

        <!-- Linked Accounts -->
        <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-30" v-if="isShowingLinked">
            <div class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
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
                    <div class="w-full flex justify-between mb-2" v-for="(link, identifier) in linkedAccounts.linked" :key="identifier">
                        <div class="p-3 w-1/2 relative">
                            <b class="block">{{ link.label }}</b>
                            <pre class="text-xs overflow-hidden overflow-ellipsis" :title="identifier">{{ identifier }}</pre>

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
                                :href="'/players/' + account.steam_identifier"
                                target="_blank"
                                v-for="account in link.accounts"
                                :key="account.steam_identifier"
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
                    <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="isShowingLinked = false">
                        {{ t('global.close') }}
                    </button>
                </div>
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

        <!-- Ban -->
        <div>
            <!-- Viewing -->
            <alert class="bg-danger dark:bg-dark-danger" v-if="player.isBanned">

                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold" v-html="local.ban">
                        {{ local.ban }}
                    </h2>
                    <div class="font-semibold">
                        {{ player.ban.timestamp | formatTime }}
                    </div>
                </div>

                <p class="text-gray-100">
                    <span class="whitespace-pre-line">{{ player.ban.reason || t('players.show.no_reason') }}</span>
                </p>

            </alert>
            <!-- Issuing -->
            <div class="p-8 mb-10 bg-gray-100 rounded dark:bg-dark-secondary" v-if="isBanning">
                <div class="mb-8 space-y-5">
                    <h2 class="text-2xl font-semibold">
                        {{ t('players.ban.issuing') }}
                    </h2>
                    <p class="text-gray-900 dark:text-gray-100" v-html="local.ban_warning">
                        {{ local.ban_warning }}
                    </p>
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
                    :href="'/logs?identifier=' + player.steamIdentifier"
                >
                    <i class="mr-1 fas fa-toilet-paper"></i>
                    {{ t('players.show.logs') }}
                </inertia-link>
                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-gray-800 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    target="_blank"
                    :href="player.steamProfileUrl"
                >
                    <i class="mr-1 fab fa-steam"></i>
                    {{ t('players.show.steam') }}
                </a>

            </div>
            <div class="flex flex-wrap items-center text-center">

                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-blue-800 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    v-if="discord"
                    href="#"
                    :title="t('players.show.discord_copy')"
                    @click="copyText($event, '<@' + discord.id + '> ' + discord.username + '#' + discord.discriminator)"
                >
                    <avatar
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
                    v-else-if="player.discord"
                    href="#"
                    :title="t('players.show.discord_copy')"
                    @click="copyText($event, '<@' + player.discord + '>')"
                >
                    <i class="mr-1 fab fa-discord"></i>
                    {{ t('players.show.discord') }}
                </a>

                <button
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-indigo-600 rounded mobile:w-full mobile:m-0 mobile:mb-3 mobile:flex-none"
                    @click="showLinked"
                >
                    <avatar
                        class="mr-3"
                        :src="player.avatar"
                        :alt="player.playerName + ' Avatar'"
                    />
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
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                    <card
                        v-for="(character) in characters"
                        :key="character.id"
                        v-bind:deleted="character.characterDeleted"
                        class="relative"
                        :class="{ 'shadow-lg' : player.status.character === character.id }"
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ character.name }} (#{{ character.id }})
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span> {{
                                    $moment(character.dateOfBirth).format('l')
                                }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="character.characterDeleted">
                                <span>{{ t('players.edit.deleted') }}:</span>
                                {{ $moment(character.characterDeletionTimestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded"
                                :href="'/players/' + player.steamIdentifier + '/characters/' + character.id + '/edit'">
                                {{ t('global.view') }}
                            </inertia-link>
                            <div class="flex justify-between flex-wrap">
                                <button
                                    class="block w-full px-4 py-3 2xl:w-split text-center text-white mt-3 bg-warning dark:bg-dark-warning rounded"
                                    v-if="player.status.status === 'online'" @click="unloadCharacter(character.id)">
                                    <i class="fas fa-bolt mr-1"></i>
                                    {{ t('players.show.unload') }}
                                </button>
                                <inertia-link
                                    class="block w-full px-4 py-3 text-center text-white mt-3 bg-blue-600 dark:bg-blue-400 rounded"
                                    :class="{ '2xl:w-split' : player.status.status === 'online' }"
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
                                    v-if="player.status.character === character.id"
                                >
                                    <i class="fas fa-plug"></i>
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
                    {{ t('players.show.warnings') }} ({{ player.warnings }})
                    <select class="inline-block ml-4 px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded"
                            id="warningFilter" @change="filterWarnings">
                        <option value="all" selected>{{ t('global.all') }}</option>
                        <option value="warning">{{ t('players.show.warning_type.warning') }}</option>
                        <option value="note">{{ t('players.show.warning_type.note') }}</option>
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
                                <avatar
                                    class="mr-3"
                                    :src="warning.issuer.avatar"
                                    :alt="warning.issuer.playerName + ' Avatar'"
                                />
                                <h4>
                                    {{ warning.issuer.playerName }}
                                    -
                                    <span v-html="wrapWarningType(warning.warningType)">{{ wrapWarningType(warning.warningType) }}</span>
                                </h4>
                            </div>
                            <div class="flex items-center">
                                <span class="text-muted dark:text-dark-muted">
                                    {{ warning.createdAt | formatTime }}
                                </span>
                                <sup class="ml-2 italic text-sm text-gray-600 dark:text-gray-400" v-if="warning.updatedAt !== warning.createdAt" :title="t('players.show.warning_edited_title', formatTime(warning.updatedAt))">
                                    {{ t('players.show.warning_edited') }}
                                </sup>
                                <button
                                    class="px-3 py-1 ml-4 text-sm font-semibold text-white bg-yellow-500 rounded"
                                    @click="warningEditId = warning.id"
                                    v-if="warningEditId !== warning.id && $page.auth.player.steamIdentifier === warning.issuer.steamIdentifier"
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
                                    v-bind:href="'/players/' + warning.player.steamIdentifier + '/warnings/' + warning.id">
                                    <i class="fas fa-trash"></i>
                                </inertia-link>
                            </div>
                        </div>
                    </template>

                    <template>
                        <p class="text-muted dark:text-dark-muted" v-if="warningEditId !== warning.id">
                            <span class="whitespace-pre-line" v-html="formatWarning(warning.message)">{{ formatWarning(warning.message) }}</span>
                        </p>
                        <textarea class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" :id="'warning_' + warning.id" v-else-if="warningEditId === warning.id">{{ warning.message }}</textarea>
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

                    <button class="px-5 py-2 font-semibold text-white bg-red-500 dark:bg-red-500 rounded" @click="form.warning.warning_type = 'warning'" type="submit">
                        <i class="mr-1 fas fa-exclamation-triangle"></i>
                        {{ t('players.warning.do_warn') }}
                    </button>
                    <button class="px-5 py-2 ml-2 font-semibold text-white bg-yellow-400 dark:bg-yellow-500 rounded" @click="form.warning.warning_type = 'note'" type="submit">
                        <i class="mr-1 fas fa-sticky-note"></i>
                        {{ t('players.warning.do_note') }}
                    </button>
                </form>
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
                <table class="w-full whitespace-no-wrap">
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

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Badge from './../../Components/Badge';
import Alert from './../../Components/Alert';
import Card from './../../Components/Card';
import Avatar from './../../Components/Avatar';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        Alert,
        Card,
        Avatar,
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
        panelLogs: {
            type: Array,
            required: true,
        },
        warnings: {
            type: Array,
            required: true,
        },
        discord: {
            type: Object,
        },
        kickReason: {
            type: String
        }
    },
    data() {
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
            },
            isShowingDeletedCharacters: false,
            isShowingLinked: false,
            isShowingLinkedLoading: false,
            linkedAccounts: {
                total: 0,
                linked: []
            }
        }
    },
    methods: {
        formatSecondDiff(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        async showLinked() {
            this.isShowingLinkedLoading = true;
            this.isShowingLinked = true;

            this.linkedAccounts.total = 0;
            this.linkedAccounts.linked = [];

            try {
                const data = await axios.get('/players/' + this.player.steamIdentifier + '/linked');

                if (data.data && data.data.status) {
                    const linked = data.data.data;

                    this.linkedAccounts.total = linked.total;
                    this.linkedAccounts.linked = linked.linked;
                }
            } catch(e) {}

            this.isShowingLinkedLoading = false;
        },
        wrapWarningType(type) {
            const label = this.t('players.show.warning_type.' + type);

            switch (type) {
                case 'warning':
                    return '<span class="italic text-red-500"><i class="fas fa-exclamation-triangle"></i> ' + label + '</span>';
                case 'note':
                    return '<span class="italic text-yellow-400"><i class="fas fa-sticky-note"></i> ' + label + '</span>';
            }

            return '';
        },
        filterWarnings() {
            const filter = $('#warningFilter').val();

            this.filteredWarnings = this.warnings.filter((w) => !filter || filter === 'all' || w.warningType === filter);
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
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/staffPM', this.form.pm);

            // Reset.
            this.isStaffPM = false;
            this.form.pm.message = null;
        },
        async kickPlayer() {
            if (!confirm(this.t('players.show.kick_confirm'))) {
                this.isKicking = false;
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/kick', this.form.kick);

            // Reset.
            this.isKicking = false;
            this.form.kick.reason = null;
        },
        async unloadCharacter(character) {
            if (!confirm(this.t('players.show.unload_confirm'))) {
                return;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/unloadCharacter', {
                character: character
            });
        },
        async deleteCharacter(e, characterId) {
            e.preventDefault();

            if (!confirm(this.t('players.show.delete_character'))) {
                return;
            }

            // Send request.
            await this.$inertia.delete('/players/' + this.player.steamIdentifier + '/characters/' + characterId);
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
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/bans', {...this.form.ban, expire});

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
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/warnings', this.form.warning);

            this.filterWarnings();

            // Reset.
            this.form.warning.message = null;
        },
        async editWarning(id, warningType) {
            // Send request.
            await this.$inertia.put('/players/' + this.player.steamIdentifier + '/warnings/' + id, {
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
            return this.urlify(warning, function (url) {
                const ext = url.split(/[#?]/)[0].split('.').pop().trim();
                let extraClass = 'user-link';

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
                '<video class="block max-h-96 max-w-full" controls autoplay><source src="' + url + '">Your browser does not support the video tag.</video>' +
                '</div>');
        },
        async removeIdentifier(identifier) {
            if (!confirm(this.t('players.show.identifier_remove'))) {
                return;
            }

            this.isShowingLinked = false;
            this.isShowingLinkedLoading = false;

            // Send request.
            await this.$inertia.delete('/players/' + this.player.steamIdentifier + '/removeIdentifier/' + identifier);
        },
        formatBanCreator(creator) {
            if (!creator) {
                return this.t('global.system');
            }
            return creator;
        }
    },
    mounted() {
        if (this.kickReason) {
            this.isKicking = true;
            this.form.kick.reason = this.kickReason;
        }

        const _this = this;
        $(document).ready(function () {
            $('body').on('click', 'a.user-image', function (e) {
                e.preventDefault();

                _this.viewImage(this, $(this).attr('href'));
            }).on('click', 'a.user-video', function (e) {
                e.preventDefault();

                _this.viewVideo(this, $(this).attr('href'));
            }).on('click', '.user-close a', function (e) {
                e.preventDefault();

                $(this).closest('.user-close').replaceWith(_this.formatWarning($(this).data('original')));
            });
        });
    }
};
</script>
