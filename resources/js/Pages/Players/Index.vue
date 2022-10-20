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
                    <div class="flex flex-wrap mb-4">
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="name">
                                {{ t('players.name') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="name" name="name" placeholder="Marius Truckster" v-model="filters.name">
                        </div>
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="steam">
                                {{ t('players.steam') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="steam" name="steam" placeholder="steam:11000010df22c8b" v-model="filters.steam">
                        </div>
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="discord">
                                {{ t('players.discord') }}
                                <sup class="text-muted dark:text-dark-muted">
                                    <a class="dark:text-blue-300 text-blue-500" href="https://support.discord.com/hc/en-us/articles/206346498-Where-can-I-find-my-User-Server-Message-ID" target="_blank" :title="t('players.discord_description')">[?]</a>
                                    *
                                </sup>
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="discord" name="discord" placeholder="150219115892703232" v-model="filters.discord">
                        </div>
                        <div class="w-1/4 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-4 font-semibold" for="server_id">
                                {{ t('players.server_id') }}
                                <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="server_id" name="server" type="number" min="0" max="9999" placeholder="123" v-model="filters.server">
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="w-full px-3 mt-3">
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">* {{ t('global.search.exact') }}</small>
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">** {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                    </div>
                </form>
                <!-- Search button -->
                <div class="w-full mt-3">
                    <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                        <span v-if="!isLoading">
                            <i class="fas fa-search"></i>
                            {{ t('players.search_btn') }}
                        </span>
                        <span v-else>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </span>
                    </button>
                </div>
            </template>
        </v-section>

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('players.title') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('global.server_id') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.identifier') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.playtime') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.warnings') }}</th>
                        <th class="w-64 px-6 py-4">{{ t('players.form.banned') }}?</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="player in players" v-bind:key="player.id">
                        <td class="px-6 py-3 border-t mobile:block" :title="t('global.server_timeout')">
                            <span class="font-semibold" v-if="player.status.status === 'online'">
                                {{ player.status.serverId }} <sup>{{ player.status.serverName }}</sup>
                            </span>
                            <span class="font-semibold" v-else-if="player.status.status === 'unavailable'" :title="t('global.status.unavailable_info')">
                                {{ t('global.status.unavailable') }}
                            </span>
                            <span class="font-semibold" v-else>
                                {{ t('global.status.' + player.status.status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.steamIdentifier }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.playerName }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.playTime | humanizeSeconds }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ player.warnings }}</td>
                        <td class="px-6 py-3 text-center border-t mobile:block">
                            <span
                                class="block px-4 py-2 text-white rounded"
                                :class="getBanInfo(player.steamIdentifier, 'reason') ? 'bg-red-600 dark:bg-red-700' : 'bg-red-500 dark:bg-red-600'"
                                :title="getBanInfo(player.steamIdentifier, 'reason') ? getBanInfo(player.steamIdentifier, 'reason') : t('players.ban.no_reason')"
                                v-if="player.isBanned"
                            >
                                {{ t('global.banned') }}
                                <span class="block text-xxs">
                                    {{ t('global.by', formatBanCreator(getBanInfo(player.steamIdentifier, 'creator_name'))) }}
                                </span>
                            </span>
                            <span class="block px-4 py-2 text-white bg-green-500 rounded dark:bg-green-600" v-else>
                                {{ t('global.not_banned') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/players/' + player.steamIdentifier">
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

            <template #footer>
                <div class="flex items-center justify-between mt-6 mb-1">

                    <!-- Navigation -->
                    <div class="flex flex-wrap">
                        <inertia-link
                            class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                            :href="links.prev"
                            v-if="page >= 2"
                        >
                            <i class="mr-1 fas fa-arrow-left"></i>
                            {{ t("pagination.previous") }}
                        </inertia-link>
                        <inertia-link
                            class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                            v-if="players.length === 15"
                            :href="links.next"
                        >
                            {{ t("pagination.next") }}
                            <i class="ml-1 fas fa-arrow-right"></i>
                        </inertia-link>
                    </div>

                    <!-- Meta -->
                    <div class="font-semibold">
                        {{ t("pagination.page", page) }}
                    </div>

                </div>
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
        },
        banMap: {
            type: Object,
            required: true,
        },
        filters: {
            name: String,
            steam: String,
            discord: String,
            server: Number,
        },
        time: {
            type: Number,
            required: true,
        },
        links: {
            type: Object,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false
        };
    },
    methods: {
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
                    only: [ 'players', 'time', 'banMap', 'links', 'page' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        getBanInfo(steamIdentifier, key) {
            const ban = steamIdentifier in this.banMap ? this.banMap[steamIdentifier] : null;

            if (key) {
                return ban && key in ban ? ban[key] : null;
            }
            return ban;
        },
        formatBanCreator(creator) {
            if (!creator) {
                return this.t('global.system');
            }
            return creator;
        }
    }
}
</script>
