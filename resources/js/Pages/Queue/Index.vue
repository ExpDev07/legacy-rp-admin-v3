<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white" id="queueTitle">
                {{ t('queue.title') }}
            </h1>
            <p>
                {{ t('queue.description') }}
            </p>
        </portal>

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ server }}
                </h2>
                <p v-if="responseLabel" :class="'text-base font-bold ' + (responseIsError ? 'text-danger dark:text-dark-danger' : 'text-success dark:text-success-danger')" id="responseLabel">
                    {{ responseLabel }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('queue.queuePosition') }}</th>
                        <th class="px-6 py-4">{{ t('queue.steamIdentifier') }}</th>
                        <th class="px-6 py-4">{{ t('queue.consoleName') }}</th>
                        <th class="px-6 py-4">{{ t('queue.priorityName') }}</th>
                        <th class="px-6 py-4">{{ t('queue.queueTime') }}</th>
                        <th class="w-24 px-6 py-4" v-if="$page.auth.player.isSuperAdmin">{{ t('queue.skipQueue') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="(player, index) in queue" :key="player.steamIdentifier">
                        <td class="px-6 py-3 border-t mobile:block">{{ index+1 }}.</td>

                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + player.steamIdentifier">
                                {{ playerName(player.steamIdentifier) }}
                            </inertia-link>
                        </td>

                        <td class="px-6 py-3 border-t mobile:block">{{ player.consoleName }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.priorityName || t('queue.no_prio') }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ formatSeconds(player.queueTime) }}</td>

                        <td class="px-6 py-3 border-t mobile:block" v-if="$page.auth.player.isSuperAdmin">
                            <button class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :title="t('queue.skip')" @click="skipQueue(player.steamIdentifier)">
                                <i class="fas fa-ticket-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="queue.length === 0">
                        <td class="px-6 py-6 text-center border-t mobile:block" colspan="100%" v-if="isLoading">
                            {{ t('global.loading') }}
                        </td>
                        <td class="px-6 py-6 text-center border-t mobile:block" colspan="100%" v-else>
                            {{ t('queue.none') }}
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
import Pagination from './../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        Pagination,
    },
    props: {
        server: {
            type: String,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false,
            isSkipping: false,

            responseLabel: '',
            responseIsError: '',

            queue: [],
            playerMap: {},

            responseTimeout: false
        };
    },
    methods: {
        async refresh() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                const data = await axios.get('/api/queue/' + this.server);

                if (data.data && data.data.status) {
                    this.queue = data.data.data.queue;
                    this.playerMap = data.data.data.playerMap;
                }
            } catch(e) {}

            this.isLoading = false;
        },
        formatSeconds(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        sleep(ms) {
            return new Promise(function(resolve) {
                setTimeout(resolve, ms);
            });
        },
        async skipQueue(steamIdentifier) {
            if (this.isSkipping || !confirm(this.t('queue.skip_confirm'))) {
                return;
            }

            this.isSkipping = true;
            try {
                const data = await axios.post('/skip_queue/' + this.server + '/' + steamIdentifier);

                clearTimeout(this.responseTimeout);

                if (data.data && data.data.status) {
                    this.responseLabel = data.data.data;
                    this.responseIsError = false;
                } else {
                    this.responseLabel = data.data.message;
                    this.responseIsError = true;
                }

                this.$nextTick(async () => {
                    $('#queueTitle')[0].scrollIntoView();

                    await this.refresh();

                    await this.sleep(5000);

                    this.responseLabel = '';
                    $('#responseLabel').text('');
                })
            } catch(e) {}

            this.isSkipping = false;
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        }
    },
    mounted() {
        const _this = this;

        async function update() {
            await _this.refresh();

            setTimeout(update, 3000);
        }

        this.$nextTick(function () {
            update();
        });
    }
}
</script>
