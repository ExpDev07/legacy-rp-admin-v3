<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('suspicious.title') }}
            </h1>
            <p>
                {{ t('suspicious.description') }}
            </p>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    {{ t('suspicious.filter') }}
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4 max-w-screen-sm">
                        <!-- Type -->
                        <div class="w-2/3 px-3">
                            <label class="block mb-2" for="logType">
                                {{ t('suspicious.type') }}
                            </label>
                            <select class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-600 border rounded" id="logType" v-model="filters.logType">
                                <option value="" disabled>{{ t('suspicious.types.none') }}</option>
                                <option value="items">{{ t('suspicious.types.items') }}</option>
                                <option value="characters">{{ t('suspicious.types.characters') }}</option>
                                <option value="pawn">{{ t('suspicious.types.pawn') }}</option>
                                <option value="warehouse">{{ t('suspicious.types.warehouse') }}</option>
                            </select>
                        </div>
                        <!-- Search button -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2">&nbsp;</label>
                            <button class="px-5 block py-2 w-full font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" @click="refresh">
                                <span v-if="!isLoading">
                                    <i class="fas fa-search"></i>
                                    {{ t('suspicious.search') }}
                                </span>
                                    <span v-else>
                                    <i class="fas fa-cog animate-spin"></i>
                                    {{ t('global.loading') }}
                                </span>
                            </button>
                        </div>
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
                <p class="mb-2 text-sm" v-if="logType === 'items' || logType === 'pawn' || logType === 'warehouse'">
                    {{ t('suspicious.cached') }}
                </p>
                <p class="text-muted dark:text-dark-muted text-xs">
                    {{ t('global.results', time) }}
                </p>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left" v-if="logType === 'items' || logType === 'pawn' || logType === 'warehouse'">
                        <th class="px-6 py-4">{{ t('suspicious.items.player') }}</th>
                        <th class="px-6 py-4">{{ t('suspicious.items.details') }}</th>
                        <th class="px-6 py-4">{{ t('suspicious.items.time') }}</th>
                    </tr>
                    <tr class="font-semibold text-left" v-if="logType === 'characters'">
                        <th class="px-6 py-4">{{ t('suspicious.items.player') }}</th>
                        <th class="px-6 py-4">{{ t('suspicious.characters.character') }}</th>
                        <th class="px-6 py-4">{{ t('suspicious.characters.cash') }}</th>
                        <th class="px-6 py-4">{{ t('suspicious.characters.bank') }}</th>
                        <th class="px-6 py-4">{{ t('suspicious.characters.stocks_balance') }}</th>
                    </tr>
                    <tr class="font-semibold text-left" v-else>
                        <th class="px-6 py-4"></th>
                    </tr>

                    <!-- Items -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in logs" v-if="logType === 'items'">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.identifier">
                                {{ playerName(log.identifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t" v-html="parseLog(log.details)">{{ parseLog(log.details) }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                    </tr>

                    <!-- Pawn -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in logs" v-if="logType === 'pawn' || logType === 'warehouse'">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.identifier">
                                {{ playerName(log.identifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ log.details }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                    </tr>

                    <!-- Characters -->
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in logs" v-if="logType === 'characters'">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.steam_identifier">
                                {{ playerName(log.steam_identifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.steam_identifier + '/characters/' + log.character_id + '/edit'">
                                {{ log.first_name + ' ' + log.last_name }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">
                            {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(log.cash) }}
                        </td>
                        <td class="px-6 py-3 border-t">
                            {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(log.bank) }}
                        </td>
                        <td class="px-6 py-3 border-t">
                            {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(log.stocks_balance) }}
                        </td>
                    </tr>

                    <!-- Nothing -->
                    <tr v-if="logs.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('suspicious.no_logs') }}
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
        playerMap: {
            type: Object,
            required: true,
        },
        filters: {
            logType: String,
        },
        links: {
            type: Object,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        },
        logType: {
            type: String,
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
                await this.$inertia.replace('/suspicious', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'logs', 'time', 'links', 'page', 'logType' ],
                });
            } catch(e) {}

            this.isLoading = false;
        },
        parseLog(details) {
            const regex = /(to|from) (inventory )?((trunk|glovebox|character|property)-(\d+-)?\d+:\d+)/gmi;

            let inventories = [];

            let m;
            while ((m = regex.exec(details)) !== null) {
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }

                if (m.length > 3 && m[3] && !inventories.includes(m[3])) {
                    inventories.push(m[3]);
                }
            }

            for (let x = 0; x < inventories.length; x++) {
                details = details.replaceAll(inventories[x], '<a title="' + this.t('inventories.view') + '" class="text-indigo-600 dark:text-indigo-400" href="/inventory/' + inventories[x] + '">' + inventories[x] + '</a>');
            }

            return details;
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        }
    }
};
</script>
