<template>
    <div>

        <!-- Editing -->
        <v-section>
            <template #header>
                <h2 v-html="local.title">
                    {{ local.title }}
                </h2>
                <h3>
                    {{ local.time }}
                </h3>
            </template>

            <template>
                <form class="space-y-6" @submit.prevent="submit">
                    <!-- Deciding if ban is temporary -->
                    <div class="flex items-center space-x-3">
                        <input class="block p-3 bg-gray-200 rounded shadow" type="checkbox" id="tempban" name="tempban" v-model="isTempBanning">
                        <label class="italic font-semibold" for="tempban">
                            {{ t('players.ban.temporary') }}
                        </label>
                    </div>

                    <!-- Expiration -->
                    <div v-if="isTempBanning">
                        <label class="italic font-semibold">
                            {{ t('players.ban.expiration') }}
                        </label>
                        <div class="flex items-center">
                            <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow" type="date" id="expireDate" name="expireDate" step="any" :min="$moment().format('YYYY-MM-DD')" v-model="form.expireDate" required>
                            <input class="block p-3 bg-gray-200 dark:bg-gray-600 rounded shadow" type="time" id="expireTime" name="expireTime" step="any" v-model="form.expireTime" required>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="italic font-semibold">
                            {{ t('players.ban.reason') }}
                        </label>
                        <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow" id="reason" name="reason" rows="5" :placeholder="player.playerName + ' did a big oopsie.'" v-model="form.reason"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center space-x-3">
                        <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600" type="submit">
                            <i class="mr-1 fas fa-gavel"></i>
                            {{ t('players.ban.update_ban') }}
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../../Layouts/App';
import VSection from './../../../Components/Section';
import Card from './../../../Components/Card';
import Badge from './../../../Components/Badge';

export default {
    layout: Layout,
    components: {
        VSection,
        Card,
        Badge,
    },
    props: {
        player: {
            type: Object,
            required: true,
        },
        ban: {
            type: Object,
            required: true,
        },
    },
    data() {
        const banTime = this.ban.expireAt ? this.$options.filters.humanizeSeconds(this.$moment(this.ban.expireAt).unix() - this.$moment(this.ban.timestamp).unix()) : null;

        return {
            local: {
                title: this.t('players.ban.banned_by', this.player.playerName, this.ban.issuer),
                time: this.ban.expireAt ? this.t('players.ban.banned_for', banTime) : this.t('players.ban.banned_forever')
            },
            form: {
                reason: this.ban.reason,
                expire: null,
                expireDate: this.ban.expireAt ? this.$moment(this.ban.expireAt).local().format('YYYY-MM-DD') : null,
                expireTime: this.ban.expireAt ? this.$moment(this.ban.expireAt).local().format('HH:mm') : null,
            },
            isTempBanning: !!this.ban.expireAt,
            banTime: this.ban.expireAt ? this.$options.filters.humanizeSeconds(this.$moment(this.ban.expireAt).unix() - this.$moment(this.ban.timestamp).unix()) : this.t('players.ban.forever_edit')
        };
    },
    methods: {
        async submit() {
            // Default expiration.
            let expire = null;

            // Calculate expire relative to now in seconds if temp ban.
            if (this.isTempBanning) {
                const nowUnix    = this.$moment().unix();
                const expireUnix = this.$moment(this.form.expireDate + ' ' + this.form.expireTime).unix();
                expire           = expireUnix - nowUnix;
            }

            // Send request.
            await this.$inertia.put('/players/' + this.player.steamIdentifier + '/bans/' + this.ban.id, { ...this.form, expire });

            // Go back to player page
            window.location.href = '/players/' + this.player.steamIdentifier;
        }
    },
}
</script>
