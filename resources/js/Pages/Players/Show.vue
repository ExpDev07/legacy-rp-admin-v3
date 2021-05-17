<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1>
                    {{ player.playerName }}
                </h1>
                <div class="flex items-center space-x-5">
                    <badge class="bg-danger-pale border-red-200" v-if="player.isBanned">
                        <span class="font-semibold">Banned</span>
                    </badge>
                    <badge class="bg-success-pale border-green-200" v-if="player.isStaff">
                        <span class="font-semibold">Staff Member</span>
                    </badge>
                    <badge class="bg-success-pale border-green-200" v-if="player.isSuperAdmin">
                        <span class="font-semibold">Super Admin</span>
                    </badge>
                    <badge class="bg-secondary border-gray-200">
                        <span class="font-semibold">{{ player.playTime | formatSeconds }}</span> played
                    </badge>
                </div>
            </div>
            <p>
                Viewing player profile.
            </p>
        </portal>

        <portal to="actions">
            <div>
                <!-- Unbanning -->
                <inertia-link class="rounded bg-success font-semibold text-white py-2 px-5" method="DELETE" v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id" v-if="player.isBanned">
                    <i class="fas fa-lock-open mr-1"></i>
                    Unban
                </inertia-link>
                <!-- Banning -->
                <button class="rounded bg-danger font-semibold text-white py-2 px-5" @click="isBanning = true" v-else>
                    <i class="fas fa-gavel mr-1"></i>
                    Issue ban
                </button>
            </div>
        </portal>

        <!-- Ban -->
        <div>
            <!-- Viewing -->
            <alert class="bg-danger" v-if="player.isBanned">

                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold">
                        Banned by <span class="italic">{{ player.ban.issuer }}</span> <span class="italic" v-if="player.ban.expire">until {{ $moment(player.ban.expireAt).format('lll') }}</span>
                    </h2>
                    <div class="font-semibold">
                        {{ $moment(player.ban.timestamp).format('lll') }}
                    </div>
                </div>

                <p class="text-gray-100">
                    {{ player.ban.reason || 'No reason.' }}
                </p>

            </alert>
            <!-- Issuing -->
            <div class="rounded bg-gray-100 p-8 mb-10" v-if="isBanning">
                <div class="space-y-5 mb-8">
                    <h2 class="text-2xl font-semibold">
                        Issuing ban
                    </h2>
                    <p class="text-gray-900">
                        You are now issuing a ban for this player. Make sure you are <span class="font-semibold">well within reason</span> to do this. It's never a bad idea to double check with an additional staff member!
                    </p>
                </div>
                <form class="space-y-6" @submit.prevent="submitBan">
                    <!-- Deciding if ban is temporary -->
                    <div class="flex items-center space-x-3">
                        <input class="block shadow rounded bg-gray-200 p-3" type="checkbox" id="tempban" name="tempban" v-model="isTempBanning">
                        <label class="font-semibold italic" for="tempban">
                            This is a temporary ban
                        </label>
                    </div>

                    <!-- Expiration -->
                    <div v-if="isTempBanning">
                        <label class="font-semibold italic">
                            Expiration
                        </label>
                        <div class="flex items-center">
                            <input class="block shadow rounded bg-gray-200 p-3" type="date" id="expireDate" name="expireDate" step="any" :min="$moment().format('YYYY-MM-DD')" v-model="form.ban.expireDate" required>
                            <input class="block shadow rounded bg-gray-200 p-3" type="time" id="expireTime" name="expireTime" step="any" v-model="form.ban.expireTime" required>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="font-semibold italic" for="reason">
                            Reason
                        </label>
                        <textarea class="block w-full shadow rounded bg-gray-200 p-5" id="reason" name="reason" rows="5" :placeholder="player.playerName + ' did a big oopsie.'" v-model="form.ban.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="rounded bg-red-500 hover:bg-red-600 font-semibold text-white py-2 px-5" type="submit">
                            <i class="fas fa-gavel mr-1"></i>
                            Ban player
                        </button>
                        <button class="rounded hover:bg-gray-200 py-2 px-5" type="button" @click="isBanning = false">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Useful links -->
        <v-section class="py-1">
            <div class="flex flex-wrap items-center text-center">

                <inertia-link
                    class="flex-1 m-2 bg-indigo-600 font-semibold text-white rounded block p-5"
                    :href="'/logs?identifier=' + player.steamIdentifier"
                >
                    <i class="fas fa-toilet-paper mr-1"></i>
                    Check player's logs
                </inertia-link>
                <a
                    class="flex-1 m-2 bg-gray-800 font-semibold text-white rounded block p-5"
                    target="_blank"
                    :href="player.steamProfileUrl"
                >
                    <i class="fab fa-steam mr-1"></i>
                    Open Steam profile
                </a>

            </div>
        </v-section>

        <!-- Characters -->
        <v-section>
            <template #header>
                <h2>
                    Characters
                </h2>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                    <card
                        v-for="(character, index) in characters"
                        :key="character.id"
                        data-aos="fade-up"
                        :data-aos-delay="index * 100"
                    >
                        <template #header>
                            <div class="text-center">
                                <h3 class="mb-2">
                                    {{ character.name }} (#{{ character.id }})
                                </h3>
                                <h4 class="text-primary">
                                    <span>Date of birth:</span> {{ $moment(character.dateOfBirth).format('l') }}
                                </h4>
                            </div>
                        </template>

                        <template>
                            <p>
                                {{ character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link class="bg-indigo-600 text-white text-center rounded block px-4 py-3" :href="'/players/' + player.steamIdentifier + '/characters/' + character.id + '/edit'">
                                View
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <p class="text-muted" v-if="characters.length === 0">
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
                    v-for="(warning, index) in warnings"
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
                                <span class="text-muted">{{ $moment(warning.createdAt).format('lll') }}</span>
                                <inertia-link class="bg-red-500 hover:bg-red-600 font-semibold text-white text-sm rounded py-1 px-3 ml-4" method="DELETE" v-bind:href="'/players/' + warning.player.steamIdentifier + '/warnings/' + warning.id">
                                    <i class="fas fa-trash"></i>
                                </inertia-link>
                            </div>
                        </div>
                    </template>

                    <template>
                        <p class="text-muted">
                            {{ warning.message }}
                        </p>
                    </template>
                </card>
                <p class="text-muted" v-if="warnings.length === 0">
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
                        class="w-full shadow rounded bg-gray-200 p-5 mb-5"
                        id="message"
                        name="message"
                        rows="5"
                        :placeholder="player.playerName + ' did an oopsie.'"
                        v-model="form.warning.message"
                        required
                    ></textarea>

                    <button class="rounded bg-indigo-600 font-semibold text-white py-2 px-5" type="submit">
                        <i class="fas fa-exclamation mr-1"></i>
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
        };
    },
    methods: {
        async submitBan () {
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
        async submitWarning () {
            // Send request.
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/warnings', this.form.warning);

            // Reset.
            this.form.warning.message = null;
        },
    },
};
</script>
