<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('panel_logs.title') }}
            </h1>
            <p>
                {{ t('panel_logs.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400" type="button" @click="refresh">
                <i class="mr-1 fa fa-refresh"></i>
                {{ t('global.refresh') }}
            </button>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('panel_logs.filter') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <!-- Source -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2">
                                {{ t('panel_logs.source') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <select class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="source" v-model="filters.source">
                                <option value="">{{ t('global.all') }}</option>
                                <option :value="source.source_identifier" v-for="source in sources">{{ playerName(source.source_identifier) }}</option>
                            </select>
                        </div>
                        <!-- Target -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="target">
                                {{ t('panel_logs.target') }} <sup class="text-muted dark:text-dark-muted">*</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="target" placeholder="steam:11000010d322da9" v-model="filters.target">
                        </div>
                        <!-- Action -->
                        <div class="w-1/3 px-3 mobile:w-full mobile:mb-3">
                            <label class="block mb-2" for="action">
                                {{ t('panel_logs.action') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                            </label>
                            <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="action" :placeholder="t('panel_logs.placeholder_action')" v-model="filters.action">
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="w-full px-3">
                        <label class="block mb-3" for="log">
                            {{ t('panel_logs.log') }} <sup class="text-muted dark:text-dark-muted">**</sup>
                        </label>
                        <input class="block w-full px-4 py-3 bg-gray-200 border rounded dark:bg-gray-600" id="log" :placeholder="t('panel_logs.placeholder_log')" v-model="filters.log">
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
                                {{ t('panel_logs.search') }}
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
                    {{ t('panel_logs.logs') }}
                </h2>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left mobile:hidden">
                        <th class="px-6 py-4">{{ t('panel_logs.source') }}</th>
                        <th class="px-6 py-4">{{ t('panel_logs.target') }}</th>
                        <th class="px-6 py-4">{{ t('panel_logs.action') }}</th>
                        <th class="px-6 py-4">{{ t('panel_logs.log') }}</th>
                        <th class="px-6 py-4">{{ t('panel_logs.timestamp') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 mobile:border-b-4" v-for="log in logs" :key="log.id">
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.source_identifier">
                                {{ playerName(log.source_identifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.target_identifier">
                                {{ playerName(log.target_identifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t mobile:block">{{ log.action }}</td>
                        <td class="px-6 py-3 border-t mobile:block" >{{ log.log }}</td>
                        <td class="px-6 py-3 border-t mobile:block">{{ log.timestamp | formatTime(true) }}</td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('panel_logs.no_logs') }}
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
                            v-if="logs.length === 15"
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
        sources: {
            type: Array,
            required: true,
        },
        filters: {
            source: String,
            target: String,
            action: String,
            log: String,
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
                await this.$inertia.replace('/panel_logs', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'logs', 'sources', 'playerMap', 'time', 'links', 'page' ],
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
