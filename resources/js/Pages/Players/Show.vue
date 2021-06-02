<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1 class="dark:text-white">
                    {{ player.playerName }}
                </h1>
                <div class="flex items-center space-x-5">
                    <badge class="border-red-200 bg-danger-pale dark:bg-dark-danger-pale" v-if="player.isBanned">
                        <span class="font-semibold">Banned</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.isStaff">
                        <span class="font-semibold">Staff Member</span>
                    </badge>
                    <badge class="border-green-200 bg-success-pale dark:bg-dark-success-pale" v-if="player.isSuperAdmin">
                        <span class="font-semibold">Super Admin</span>
                    </badge>
                    <badge class="border-gray-200 bg-secondary dark:bg-dark-secondary">
                        <span class="font-semibold">{{ player.playTime | humanizeSeconds }}</span> played
                    </badge>
                </div>
            </div>
            <p class="dark:text-dark-muted">
                Viewing player profile.
            </p>
        </portal>

        <portal to="actions">
            <div>
                <!-- Unbanning -->
                <inertia-link class="px-5 py-2 font-semibold text-white rounded bg-success dark:bg-dark-success" method="DELETE" v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id" v-if="player.isBanned">
                    <i class="mr-1 fas fa-lock-open"></i>
                    Unban
                </inertia-link>
                <!-- Banning -->
                <button class="px-5 py-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger" @click="isBanning = true" v-else>
                    <i class="mr-1 fas fa-gavel"></i>
                    Issue ban
                </button>
            </div>
        </portal>

        <!-- Ban -->
        <div>
            <!-- Viewing -->
            <alert class="bg-danger dark:bg-dark-danger" v-if="player.isBanned">

                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold">
                        Banned by <span class="italic">{{ player.ban.issuer }}</span> <span class="italic" v-if="player.ban.expire">until {{ player.ban.expireAt | formatTime }}</span>
                    </h2>
                    <div class="font-semibold">
                        {{ player.ban.timestamp | formatTime }}
                    </div>
                </div>

                <p class="text-gray-100">
                    {{ player.ban.reason || 'No reason.' }}
                </p>

            </alert>
            <!-- Issuing -->
            <div class="p-8 mb-10 bg-gray-100 rounded dark:bg-dark-secondary" v-if="isBanning">
                <div class="mb-8 space-y-5">
                    <h2 class="text-2xl font-semibold">
                        Issuing ban
                    </h2>
                    <p class="text-gray-900 dark:text-gray-100">
                        You are now issuing a ban for this player. Make sure you are <span class="font-semibold">well within reason</span> to do this. It's never a bad idea to double check with an additional staff member!
                    </p>
                </div>
                <form class="space-y-6" @submit.prevent="submitBan">
                    <!-- Deciding if ban is temporary -->
                    <div class="flex items-center space-x-3">
                        <input class="block p-3 bg-gray-200 rounded shadow" type="checkbox" id="tempban" name="tempban" v-model="isTempBanning">
                        <label class="italic font-semibold" for="tempban">
                            This is a temporary ban
                        </label>
                    </div>

                    <!-- Expiration -->
                    <div v-if="isTempBanning">
                        <label class="italic font-semibold">
                            Expiration
                        </label>
                        <div class="flex items-center">
                            <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow" type="date" id="expireDate" name="expireDate" step="any" :min="$moment().format('YYYY-MM-DD')" v-model="form.ban.expireDate" required>
                            <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow" type="time" id="expireTime" name="expireTime" step="any" v-model="form.ban.expireTime" required>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="italic font-semibold" for="reason">
                            Reason
                        </label>
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="reason" name="reason" rows="5" :placeholder="player.playerName + ' did a big oopsie.'" v-model="form.ban.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600" type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            Ban player
                        </button>
                        <button class="px-5 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-500 dark:bg-gray-500" type="button" @click="isBanning = false">
                            Cancel
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
                    Check player's logs
                </inertia-link>
                <a
                    class="flex-1 block p-5 m-2 font-semibold text-white bg-gray-800 rounded"
                    target="_blank"
                    :href="player.steamProfileUrl"
                >
                    <i class="mr-1 fab fa-steam"></i>
                    Open Steam profile
                </a>

            </div>
        </v-section>

        <!-- Characters -->
        <v-section>
            <template #header>
                <h2>
                    Characters

                    <!-- Hiding deleted characters on click -->
                    <button class="px-3 py-2 font-semibold text-white rounded bg-gray-400 text-base float-right" @click="hideDeleted">
                        <span v-if="isShowingDeletedCharacters">
                            <i class="fas fa-eye-slash"></i>
                            Hide Deleted
                        </span>
                        <span v-else>
                            <i class="fas fa-eye"></i>
                            Show Deleted
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
                                <span>Date of birth:</span> {{ $moment(character.dateOfBirth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="character.characterDeleted">
                                <span>Deleted at:</span> {{ $moment(character.characterDeletionTimestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link class="block px-4 py-3 text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/players/' + player.steamIdentifier + '/characters/' + character.id + '/edit'">
                                View
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted dark:text-dark-muted" v-if="characters.length === 0">
                    This player has not created any characters yet.
                </p>
            </template>
        </v-section>

        <!-- Warnings -->
        <v-section>
            <template #header>
                <h2>
                    Warnings ({{ player.warnings }})
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
                    This player has not received any warnings.
                </p>
            </template>

            <template #footer>
                <h3 class="mb-2">
                   Give warning
                </h3>
                <form @submit.prevent="submitWarning">
                    <label for="message"></label>
                    <textarea
                        class="w-full p-5 mb-5 bg-gray-200 rounded shadow dark:bg-gray-600"
                        id="message"
                        name="message"
                        rows="5"
                        :placeholder="player.playerName + ' did an oopsie.'"
                        v-model="form.warning.message"
                        required
                    >
                    </textarea>

                    <button class="px-5 py-2 font-semibold text-white bg-indigo-600 dark:bg-indigo-400 rounded" type="submit">
                        <i class="mr-1 fas fa-exclamation"></i>
                        Warn player
                    </button>
                </form>
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
        warnings: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            isBanning: false,
            isTempBanning: false,
            form: {
                ban: {
                    reason: null,
                    expire: null,
                    expireDate: null,
                    expireTime: null,
                },
                warning: {
                    message: null,
                },
            },
            isShowingDeletedCharacters: true,
        }
    },
    methods: {
        async submitBan() {
            // Default expiration.
            let expire = null;

            // Calculate expire relative to now in seconds if temp ban.
            if (this.isTempBanning) {
                const nowUnix    = this.$moment().unix();
                const expireUnix = this.$moment(this.form.ban.expireDate + ' ' + this.form.ban.expireTime).unix();
                expire           = expireUnix - nowUnix;
            }

            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/bans', { ...this.form.ban, expire });

            // Reset.
            this.isBanning = false;
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
