<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('players.new.title') }}
            </h1>
            <p>
                {{ t('players.new.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                    type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('global.refresh') }}
            </button>
        </portal>

        <v-section class="overflow-x-auto" :noFooter="true">
            <template #header>
                <h2 class="relative">
                    {{ t('players.new.title') }}

                    <div class="absolute top-1/2 right-0 transform -translate-y-1/2 h-7 w-48 rounded-sm bg-rose-800 dark:bg-rose-400 shadow-sm" v-if="isLoadingClassifier">
                        <div class="h-full rounded-sm bg-rose-900 dark:bg-rose-500" :class="{'bg-green-900 dark:bg-green-500' : progress === 100}" :style="'width: ' + progress + '%'"></div>
                        <div class="absolute top-1/2 left-0 w-full text-center transform -translate-y-1/2 text-xs monospace">{{ t('players.new.loading', progress) }}</div>
                    </div>
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('global.server_id') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.playtime') }}</th>
                        <th class="px-6 py-4">{{ t('players.new.danny_percentage') }}</th>
                        <th class="px-6 py-4">{{ t('players.new.character') }}</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="player in players"
                        :key="player.licenseIdentifier">
                        <td class="px-6 py-3 border-t mobile:block" :title="t('global.server_timeout')">
                            <span class="font-semibold" v-if="player.serverId">
                                {{ player.serverId }}
                            </span>
                            <span class="font-semibold" v-else>
                                {{ t('global.status.offline') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.playerName }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ formatSecondDiff(player.playTime) }}</td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <span v-if="player.character && player.character.danny !== false">
                                {{ (player.character.danny * 100).toFixed(1) }}% Default Danny
                            </span>
                            <span v-else>
                                {{ t('players.new.no_character') }}
                            </span>

                            <template v-if="player.character" :set="prediction = classify(player.character)">
                                <span class="block text-xs italic text-blue-800 dark:text-blue-200" v-if="prediction === 'loading'" :title="t('players.new.prediction')">{{ t("players.new.prediction_loading") }}</span>
                                <span class="block text-xs italic text-red-800 dark:text-red-200" v-else-if="prediction === 'negative'" :title="t('players.new.prediction')">{{ t("players.new.prediction_negative") }}</span>
                                <span class="block text-xs italic text-green-800 dark:text-green-200" v-else :title="t('players.new.prediction')">{{ t("players.new.prediction_positive") }}</span>
                            </template>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <pre class="whitespace-pre-wrap text-xs max-w-xl"
                                v-if="player.character"><b>{{ player.character.name }}</b><br>{{ player.character.backstory.length > 250 ? player.character.backstory.substr(0, 250) + "..." : player.character.backstory }}</pre>
                            <span v-else>{{ t('players.new.no_character') }}</span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link
                                class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400"
                                :href="'/players/' + player.licenseIdentifier">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="players.length === 0">
                        <td class="px-6 py-6 text-center border-t mobile:block" colspan="100%">
                            {{ t('players.none') }}
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
        players: {
            type: Array,
            required: true,
        }
    },
    data() {
        return {
            isLoading: false,

            isLoadingClassifier: false,
            progress: 0
        };
    },
    methods: {
        formatSecondDiff(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        classify(character) {
            const prediction = this.classifyCharacter(character);

            if (prediction === false) return 'loading';

            return prediction;
        },
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/new_players', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['players'],
                });
            } catch (e) { }

            this.isLoading = false;
        }
    },
    async mounted() {
        this.isLoadingClassifier = true;

        await this.loadClassifier(percentage => {
            this.progress = percentage;
        });

        setTimeout(() => {
            this.progress = 100;
            this.isLoadingClassifier = false;
        }, 1500);
    },
}
</script>
