<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('logs.logs') }}
            </h1>
            <p>
                {{ t('logs.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('logs.refresh') }}
            </button>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('logs.filter') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <!-- Identifier -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="identifier">
                                {{ t('logs.identifier') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="identifier" placeholder="steam:11000010df22c8b" v-model="filters.identifier">
                        </div>
                        <!-- Action -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="action">
                                {{ t('logs.action') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="action" :placeholder="t('logs.placeholder_action')" v-model="filters.action">
                        </div>
                        <!-- Server -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="server">
                                {{ t('logs.server_id') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="server" placeholder="3" type="number" v-model="filters.server">
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="w-full px-3">
                        <label class="block mb-3" for="details">
                            {{ t('logs.details') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                        </label>
                        <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="details" :placeholder="t('logs.placeholder_details')" v-model="filters.details">
                    </div>
                    <!-- Description -->
                    <div class="w-full px-3 mt-3">
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">* {{ t('global.search.exact') }}</small>
                        <small class="text-muted dark:text-dark-muted mt-1 leading-4 block">** {{ t('global.search.like') }} {{ t('global.search.like_prepend') }}</small>
                    </div>
                    <!-- Search button -->
                    <div class="w-full px-3 mt-3">
                        <button class="px-5 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                            <span v-if="!isLoading">
                                <i class="fas fa-search"></i>
                                {{ t('logs.search') }}
                            </span>
                            <span v-else>
                                <i class="fas fa-cog animate-spin"></i>
                                {{ t('global.loading') }}
                            </span>
                        </button>
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('logs.logs') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('logs.player') }}</th>
                        <th class="px-6 py-4">{{ t('logs.action') }}</th>
                        <th class="px-6 py-4">{{ t('logs.details') }}</th>
                        <th class="px-6 py-4">{{ t('logs.timestamp') }}</th>
                        <th class="px-6 py-4">{{ t('logs.server_id') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in logs" :key="log.id">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.steamIdentifier">
                                {{ playerName(log.steamIdentifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ log.action }}</td>
                        <td class="px-6 py-3 border-t">{{ log.details }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t">{{ log.server }}</td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('logs.no_logs') }}
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
                            v-if="page > 1"
                        >
                            <i class="mr-1 fas fa-arrow-left"></i>
                            {{ t("pagination.previous") }}
                        </inertia-link>
                        <inertia-link
                            class="px-4 py-2 mr-3 font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                            v-if="logs.length !== 0"
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
import Pagination from './../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        Pagination,
        VSection,
    },
    props: {
        logs: {
            type: Array,
            required: true,
        },
        filters: {
            identifier: String,
            action: String,
            server: Number,
            details: String,
        },
        playerMap: {
            type: Object,
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
        time: {
            type: Number,
            required: true,
        }
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
                await this.$inertia.replace('/logs', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'logs', 'playerMap', 'time' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        }
    }
};
</script>
