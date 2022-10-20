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

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('players.new.title') }}
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
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="player in players" :key="player.steamIdentifier">
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
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <pre class="whitespace-pre-wrap text-xs max-w-xl" v-if="player.character"><b>{{ player.character.name }}</b><br>{{ player.character.backstory.length > 250 ? player.character.backstory.substr(0, 250) + "..." : player.character.backstory }}</pre>
                            <span v-else>{{ t('players.new.no_character') }}</span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + player.steamIdentifier">
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
            isLoading: false
        };
    },
    methods: {
        formatSecondDiff(sec) {
            return this.$moment.duration(sec, 'seconds').format('d[d] h[h] m[m] s[s]');
        },
        refresh: async function () {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/players', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'players' ],
                });
            } catch(e) {}

            this.isLoading = false;
        }
    }
}
</script>
