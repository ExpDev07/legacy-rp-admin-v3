<template>
    <div>
        <div class="flex flex-grow items-start justify-between mb-10">
            <div class="prose">
                <h1>
                    {{ player.playerName }}
                </h1>
                <p>
                    Viewing player profile.
                </p>
            </div>
            <div>
                <!-- Unbanning -->
                <inertia-link class="rounded bg-green-500 hover:bg-green-600 text-white py-2 px-5" method="DELETE" v-bind:href="'/players/' + player.steamIdentifier + '/bans/' + player.ban.id" v-if="player.isBanned">
                    <i class="fas fa-lock-open mr-1"></i>
                    Unban
                </inertia-link>
                <!-- Banning -->
                <button class="rounded bg-red-500 hover:bg-red-600 text-white py-2 px-5" @click="creatingBan = true" v-else>
                    <i class="fas fa-gavel mr-1"></i>
                    Issue ban
                </button>
            </div>
        </div>

        <!-- Ban -->
        <div>
            <!-- Viewing -->
            <div class="rounded bg-red-500 text-white p-4 mb-10" v-if="player.isBanned">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold">
                        Banned by {{ player.ban.issuer }}
                    </h2>
                    <p>
                        {{ new Date(player.ban.timestamp).toLocaleString() }}
                    </p>
                </div>
                <p>
                    {{ player.ban.reason }}
                </p>
            </div>
            <!-- Issuing -->
            <div class="rounded bg-gray-100 p-5 mb-10" v-if="creatingBan">
                <h2 class="text-2xl font-semibold mb-4">
                    Issuing ban
                </h2>
                <p class="mb-6">
                    You are now issuing a ban for this player. Make sure you are <span class="font-semibold">well within reason</span> to do this. It's never a bad idea to double check with an additional staff member!
                </p>
                <form @submit.prevent="submitBan">
                    <label for="reason"></label>
                    <textarea class="w-full shadow rounded bg-gray-200 p-5 mb-5" id="reason" name="reason" rows="5" v-bind:placeholder="player.playerName + ' did a big oopsie.'" v-model="form.ban.reason" required></textarea>

                    <button class="rounded bg-red-500 hover:bg-red-600 text-white py-2 px-5 mr-1" type="submit">
                        <i class="fas fa-gavel mr-1"></i>
                        Ban player
                    </button>
                    <button class="rounded hover:bg-gray-200 py-2 px-5" type="button" @click="creatingBan = false">
                        Cancel
                    </button>
                </form>
            </div>
        </div>

        <!-- Useful links -->
        <div class="rounded bg-gray-100 p-5 mb-8">
            <div class="flex items-center">
                <inertia-link class="m-2 w-full bg-indigo-600 hover:bg-orange-500 text-white text-center rounded block px-5 py-2" v-bind:href="'/logs?identifier=' + player.steamIdentifier">
                    <i class="fas fa-toilet-paper mr-1"></i>
                    Logs
                </inertia-link>
                <a class="m-2 w-full bg-gray-800 hover:bg-gray-900 text-white text-center rounded block px-5 py-2" target="_blank" v-bind:href="player.steamProfileUrl">
                    <i class="fab fa-steam mr-1"></i>
                    Steam Profile
                </a>
            </div>
        </div>
        
        <!-- Characters -->
        <div class="rounded bg-gray-100 p-5 mb-8">
            <h2 class="text-2xl mx-3 mb-3">
                Characters
            </h2>
            <div class="grid grid-cols-3">
                <div class="flex flex-col bg-gray-200 shadow rounded p-5 m-3" v-for="character in characters" v-bind:key="character.id">
                    <div class="flex-grow">
                        <div class="text-center border-b border-gray-900 mb-5 pb-4">
                            <h1 class="text-xl font-semibold mb-2">
                                {{ character.name }} (#{{ character.id }})
                            </h1>
                            <h3 class="text-indigo-500">
                                DOB: {{ new Date(character.dateOfBirth).toLocaleString() }}
                            </h3>
                        </div>
                        <p class="text-gray-800 mb-8">
                            {{ character.backstory }}
                        </p>
                    </div>
                    <inertia-link class="bg-indigo-600 hover:bg-orange-500 text-white text-center rounded block px-4 py-3" v-bind:href="'/players/' + player.steamIdentifier + '/characters/' + character.id + '/edit'">
                        View
                    </inertia-link>
                </div>
            </div>
            <p class="px-4 py-6" v-if="characters.length === 0">
                This player has not created any characters yet.
            </p>
        </div>

        <!-- Warnings -->
        <div class="rounded bg-gray-100 p-5">
            <h2 class="text-2xl mb-5">
                Warnings ({{ player.warnings }})
            </h2>
            <div class="mb-8">
                <div class="flex-grow bg-gray-200 rounded p-5 mb-5" v-for="warning in warnings" v-bind:key="warning.id">
                    <div class="flex items-center justify-between border-b-2 border-gray-900 mb-5 pb-5">
                        <h1 class="text-lg font-semibold">
                            {{ warning.issuer.playerName }}
                        </h1>
                        <div class="flex items-center">
                            <p>
                                <span class="font-semibold">added @</span> {{ new Date(warning.createdAt).toLocaleString() }}
                            </p>
                            <inertia-link class="bg-red-500 hover:bg-red-600 text-white text-sm rounded py-1 px-4 ml-4" method="DELETE" v-bind:href="'/players/' + warning.player.steamIdentifier + '/warnings/' + warning.id">
                                <i class="fas fa-trash mr-1"></i>
                                Remove
                            </inertia-link>
                        </div>
                    </div>
                    <p>
                        {{ warning.message }}
                    </p>
                </div>
                <p v-if="warnings.length === 0">
                    This player has not received any warnings.
                </p>
            </div>
            <form @submit.prevent="submitWarning">
                <label for="message"></label>
                <textarea class="w-full shadow rounded bg-gray-200 p-5 mb-5" id="message" name="message" rows="5" v-bind:placeholder="player.playerName + ' did an oopsie.'" v-model="form.warning.message" required></textarea>

                <button class="rounded bg-indigo-600 hover:bg-indigo-600 text-white py-2 px-5" type="submit">
                    <i class="fas fa-exclamation mr-1"></i>
                    Warn player
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import Layout from './../../Layouts/App';

export default {
    layout: Layout,
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
            creatingBan: false,
            form: {
                ban: {
                    reason: null,
                },
                warning: {
                    message: null,
                }
            },
        };
    },
    methods: {
        async submitBan() {
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/bans', this.form.ban);
            this.creatingBan = false;
            this.form.ban.message = null;
        },
        async submitWarning() {
            await this.$inertia.post('/players/' + this.player.steamIdentifier + '/warnings', this.form.warning);
            this.form.warning.message = null;
        },
    },
};
</script>
