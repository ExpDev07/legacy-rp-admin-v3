<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1 class="dark:text-white">
                    {{ t('servers.list.server') }} #{{ server.id }}
                </h1>
                <div class="flex items-center space-x-5">
                    <badge class="border-gray-200 bg-secondary dark:bg-dark-secondary">
                        {{ t('servers.show.players', players.length) }}
                    </badge>
                </div>
            </div>
        </portal>

        <portal to="actions">
            <div>

            </div>
        </portal>

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('servers.show.table') }}
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('players.form.identifier') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('servers.show.joined') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.playtime') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.warnings') }}</th>
                        <th class="w-64 px-6 py-4">{{ t('players.form.banned') }}?</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="player in players" v-bind:key="player.id">
                        <td class="px-6 py-3 border-t">{{ player.steamIdentifier }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playerName }}</td>
                        <td class="px-6 py-3 border-t">{{ player.lastConnection | formatTime }}</td>
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
                    <tr v-if="players.length === 0">
                        <td class="px-6 py-6 text-center border-t" colspan="100%">
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
        server: {
            type: Object,
            required: true,
        },
        players: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {}
    },
};
</script>
