<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1 class="dark:text-white">
                    {{ player.playerName }}
                </h1>
                <div class="flex items-center space-x-5">
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-if="player.isBanned">
                        <span class="font-semibold">{{ t('global.banned') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.isStaff">
                        <span class="font-semibold">{{ t('global.staff') }}</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.isSuperAdmin">
                        <span class="font-semibold">{{ t('global.super') }}</span>
                    </badge>

                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.status.status === 'online'">
                        <span class="font-semibold">{{ t('global.status.online') }} <sup>[{{ player.status.serverId }}]</sup></span>
                    </badge>
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-else>
                        <span class="font-semibold">{{ t('global.status.' + player.status.status) }}</span>
                    </badge>

                    <badge class="border-gray-200 bg-secondary dark:bg-dark-secondary" v-html="local.played">
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
                <!-- StaffPM -->
                <button class="px-5 py-2 mr-3 font-semibold text-white rounded bg-blue-600 dark:bg-blue-500" @click="isStaffPM = true" v-if="player.status.status === 'online'">
                    <i class="fas fa-envelope-open-text"></i>
                    {{ t('players.show.staffpm') }}
                </button>
                <!-- Kicking -->
                <button class="px-5 py-2 mr-3 font-semibold text-white rounded bg-yellow-600 dark:bg-yellow-500" @click="isKicking = true" v-if="player.status.status === 'online'">
                    <i class="fas fa-user-minus"></i>
                    {{ t('players.show.kick') }}
                </button>
                <!-- Edit Ban -->
                <inertia-link class="px-5 py-2 font-semibold text-white rounded bg-yellow-500 mr-3" v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id + '/edit'" v-if="player.isBanned">
                    <i class="mr-1 fas fa-edit"></i>
                    {{ t('players.show.edit_ban') }}
                </inertia-link>
                <!-- Unbanning -->
                <inertia-link class="px-5 py-2 font-semibold text-white rounded bg-success dark:bg-dark-success" method="DELETE" v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id" v-if="player.isBanned">
                    <i class="mr-1 fas fa-lock-open"></i>
                    {{ t('players.show.unban') }}
                </inertia-link>
                <!-- Banning -->
                <button class="px-5 py-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger" @click="isBanning = true" v-else>
                    <i class="mr-1 fas fa-gavel"></i>
                    {{ t('players.show.issue') }}
                </button>
            </div>
        </portal>

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
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="pm_message" name="message" rows="5" placeholder="Please join waiting for support" v-model="form.pm.message"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600" type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.show.pm_confirm') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500" type="button" @click="isStaffPM = false">
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
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="kick_reason" name="reason" rows="5" placeholder="You were kicked from the server." v-model="form.kick.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600" type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.show.kick') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500" type="button" @click="isKicking = false">
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
                    {{ player.ban.reason || t('players.show.no_reason') }}
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
                        <input class="block p-3 bg-gray-200 rounded shadow" type="checkbox" id="tempban" name="tempban" v-model="isTempBanning">
                        <label class="italic font-semibold" for="tempban">
                            {{ t('players.ban.temporary') }}
                        </label>
                    </div>

                    <!-- Expiration -->
                    <div class="flex flex-wrap" v-if="isTempBanning">
                        <div class="flex items-center space-x-3 w-full mb-3">
                            <input class="block p-3 bg-gray-200 rounded shadow" type="checkbox" id="tempselect" v-model="isTempSelect">
                            <label class="italic font-semibold" for="tempselect">
                                {{ t('players.ban.temp-select') }}
                            </label>
                        </div>

                        <div class="w-full" v-if="isTempSelect">
                            <label class="italic font-semibold block mb-1">
                                {{ t('players.ban.expiration') }}
                            </label>
                            <div class="flex items-center">
                                <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow mr-1" type="date" id="expireDate" name="expireDate" step="any" :min="$moment().format('YYYY-MM-DD')" v-model="form.ban.expireDate" required>
                                <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow" type="time" id="expireTime" name="expireTime" step="any" v-model="form.ban.expireTime" required>
                            </div>
                        </div>

                        <div class="mr-1" v-if="!isTempSelect">
                            <label class="italic font-semibold block mb-1">
                                {{ t('players.ban.temp-value') }}
                            </label>
                            <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow min-w-input" type="number" min="1" id="ban-value" step="1" required>
                        </div>
                        <div v-if="!isTempSelect">
                            <label class="italic font-semibold block mb-1">
                                {{ t('players.ban.temp-type') }}
                            </label>
                            <select class="px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600 min-w-input" id="ban-type">
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
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="reason" name="reason" rows="5" :placeholder="player.playerName + ' did a big oopsie.'" v-model="form.ban.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600" type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.ban.do_ban') }}
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500" type="button" @click="isBanning = false">
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
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-indigo-600 rounded"
                    :href="'/logs?identifier=' + player.steamIdentifier"
                >
                    <i class="mr-1 fas fa-toilet-paper"></i>
                    {{ t('players.show.logs') }}
                </inertia-link>
                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-gray-800 rounded"
                    target="_blank"
                    :href="player.steamProfileUrl"
                >
                    <i class="mr-1 fab fa-steam"></i>
                    {{ t('players.show.steam') }}
                </a>
                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-blue-800 rounded"
                    target="_blank"
                    :href="'https://discordapp.com/users/' + player.discord"
                    v-if="player.discord"
                >
                    <i class="mr-1 fab fa-discord"></i>
                    {{ t('players.show.discord') }}
                </a>

            </div>
        </v-section>

        <!-- Characters -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.characters.characters') }}

                    <!-- Hiding deleted characters on click -->
                    <button class="px-3 py-2 font-semibold text-white rounded bg-gray-400 text-base float-right" @click="hideDeleted">
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
                    >
                        <template #header>
                            <h3 class="mb-2">
                                {{ character.name }} (#{{ character.id }})
                            </h3>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span> {{ $moment(character.dateOfBirth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="character.characterDeleted">
                                <span>{{ t('players.edit.deleted') }}:</span> {{ $moment(character.characterDeletionTimestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link class="block px-4 py-3 text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/players/' + player.steamIdentifier + '/characters/' + character.id + '/edit'">
                                {{ t('global.view') }}
                            </inertia-link>
                            <inertia-link
                                class="block px-4 py-3 text-center text-white mt-3 bg-blue-600 dark:bg-blue-400 rounded"
                                :href="'/inventories/character/' + character.id"
                                v-if="!character.characterDeleted"
                            >
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ t('inventories.view') }}
                            </inertia-link>
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
                    {{ t('players.form.warnings') }} ({{ player.warnings }})
                </h2>
            </template>

            <template>
                <card
                    v-for="(warning) in warnings"
                    :key="warning.id"
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
                                </h4>
                            </div>
                            <div class="flex items-center">
                                <span class="text-muted dark:text-dark-muted">
                                    {{ warning.createdAt | formatTime }}
                                </span>
                                <inertia-link class="px-3 py-1 ml-4 text-sm font-semibold text-white bg-red-500 rounded hover:bg-red-600" method="DELETE" v-bind:href="'/players/' + warning.player.steamIdentifier + '/warnings/' + warning.id">
                                    <i class="fas fa-trash"></i>
                                </inertia-link>
                            </div>
                        </div>
                    </template>

                    <template>
                        <p class="text-muted dark:text-dark-muted">
                            {{ warning.message }}
                        </p>
                    </template>
                </card>
                <p class="text-muted dark:text-dark-muted" v-if="warnings.length === 0">
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

                    <button class="px-5 py-2 font-semibold text-white bg-indigo-600 dark:bg-indigo-400 rounded" type="submit">
                        <i class="mr-1 fas fa-exclamation"></i>
                        {{ t('players.warning.do') }}
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
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('logs.action') }}</th>
                        <th class="px-6 py-4">{{ t('logs.timestamp') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in panelLogs" :key="log.id">
                        <td class="px-6 py-3 border-t">{{ log.log }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
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
                },
            },
            isShowingDeletedCharacters: false,
        }
    },
    methods: {
        localizeBan() {
            if (!this.player.ban) {
                return '';
            }
            return this.player.ban.expireAt
                ? this.t('players.show.ban', this.player.ban.issuer, this.$options.filters.formatTime(this.player.ban.expireAt))
                : this.t('players.ban.forever', this.player.ban.issuer);
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

            // Reset.
            this.form.warning.message = null;
        },
        hideDeleted(e) {
            e.preventDefault();

            this.isShowingDeletedCharacters = !this.isShowingDeletedCharacters;
            if (this.isShowingDeletedCharacters) {
                $('.card-deleted').removeClass('hidden');
            } else {
                $('.card-deleted').addClass('hidden');
            }
        }
    },
};
</script>
