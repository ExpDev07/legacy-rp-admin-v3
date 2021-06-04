<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('players.title') }}
            </h1>
            <p>
                {{ t('players.description') }}
            </p>
        </portal>

        <!-- Search -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('players.search') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <label class="block mb-4 font-semibold" for="name">
                        {{ t('players.search_label') }}
                    </label>
                    <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="name" name="name" placeholder="Marius Truckster | steam:11000010df22c8b | 150219115892703232" v-model="filters.query">
                </form>
            </template>
        </v-section>

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('players.title') }}
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('players.form.identifier') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.playtime') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.warnings') }}</th>
                        <th class="w-64 px-6 py-4">{{ t('players.form.banned') }}?</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="player in players.data" v-bind:key="player.id">
                        <td class="px-6 py-3 border-t">{{ player.steamIdentifier }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playerName }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playTime | humanizeSeconds }}</td>
                        <td class="px-6 py-3 border-t">{{ player.warnings }}</td>
                        <td class="px-6 py-3 text-center border-t">
                            <span class="block px-4 py-2 text-white bg-red-500 rounded dark:bg-red-600" v-if="player.isBanned">
                                {{ t('global.banned') }}
                            </span>
                            <span class="block px-4 py-2 text-white bg-green-500 rounded dark:bg-green-600" v-else>
                                {{ t('global.not_banned') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + player.steamIdentifier">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="players.data.length === 0">
                        <td class="px-6 py-6 text-center border-t" colspan="100%">
                            {{ t('players.none') }}
                        </td>
                    </tr>
                </table>
            </template>

            <template #footer>
                <pagination v-bind:links="players.links" v-bind:meta="players.meta" />
            </template>
        </v-section>
    </div>
</template>

<script>
import throttle from 'lodash/throttle';
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Pagination from './../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        VSection,
        Pagination,
    },
    props: {
        players: {
            type: Object,
            required: true,
        },
        filters: {
            query: String,
        },
    },
    watch: {
        filters: {
            handler: throttle(function () {
                this.$inertia.replace('/players', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'players' ],
                });
            }, 150),
            deep: true,
        },
    },
}
</script>
