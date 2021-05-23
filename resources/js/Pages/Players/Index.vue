<template>
    <div>

        <portal to="title">
            <h1>
                Players
            </h1>
            <p>
                Search players!
            </p>
        </portal>

        <!-- Search -->
        <v-section>
            <template #header>
                <h2>
                    Search
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <label class="block mb-4 font-semibold" for="name">
                        Search by name, steam identifier, or any other identifier such as Discord id.
                    </label>
                    <input class="w-full px-4 py-2 bg-gray-200" id="name" name="name" placeholder="Marius Truckster | steam:11000010df22c8b | 150219115892703232" v-model="filters.query">
                </form>
            </template>
        </v-section>

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    Players
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">Identifier</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Playtime</th>
                        <th class="px-6 py-4">Warnings</th>
                        <th class="w-64 px-6 py-4">Banned?</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100" v-for="player in players.data" v-bind:key="player.id">
                        <td class="px-6 py-3 border-t">{{ player.steamIdentifier }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playerName }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playTime | humanizeSeconds }}</td>
                        <td class="px-6 py-3 border-t">{{ player.warnings }}</td>
                        <td class="px-6 py-3 text-center border-t">
                            <span class="block px-4 py-2 text-white bg-red-500 rounded" v-if="player.isBanned">
                                Banned
                            </span>
                            <span class="block px-4 py-2 text-white bg-green-500 rounded" v-else>
                                Not banned
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded" v-bind:href="'/players/' + player.steamIdentifier">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="players.data.length === 0">
                        <td class="px-6 py-6 text-center border-t" colspan="100%">
                            No players was found.
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
