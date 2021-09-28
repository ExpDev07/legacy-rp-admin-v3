<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('inventories.logs.title') }}
            </h1>
            <p>
                {{ t('inventories.logs.description') }}
            </p>
        </portal>

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
                        <th class="px-6 py-4">{{ t('inventories.character.item') }}</th>
                        <th class="px-6 py-4">{{ t('logs.timestamp') }}</th>
                        <th class="px-6 py-4">{{ t('inventories.character.movement') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in logs" :key="log.id">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.steamIdentifier">
                                {{ playerName(log.steamIdentifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ log.itemMoved }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t">
                            <div class="flex">
                                <inertia-link v-if="log.inventoryFrom && log.inventoryFrom.type" :class="'w-inventory block px-2 py-2 font-semibold text-center ' + inventoryColor(log.inventoryFrom.type)" v-bind:href="'/inventory/' + log.inventoryFrom.descriptor">
                                    {{ log.inventoryFrom.title }}
                                </inertia-link>
                                <span class="font-semibold w-inventory block px-2 py-2 bg-gray-500 text-center text-white" v-else-if="log.inventoryFrom && log.inventoryFrom.descriptor !== 'unknown'">
                                    {{ log.inventoryFrom.descriptor }}
                                </span>
                                <span class="font-semibold w-inventory block px-2 py-2 bg-gray-500 text-center text-white" v-else>
                                    {{ t('inventories.character.unknown') }}
                                </span>

                                <span class="font-semibold block px-2 py-2 bg-gray-600 text-center text-white">
                                    &#11166;
                                </span>

                                <inertia-link v-if="log.inventoryTo && log.inventoryTo.type" :class="'block w-inventory px-2 py-2 font-semibold text-center ' + inventoryColor(log.inventoryTo.type)" v-bind:href="'/inventory/' + log.inventoryTo.descriptor">
                                    {{ log.inventoryTo.title }}
                                </inertia-link>
                                <span class="font-semibold w-inventory block px-2 py-2 bg-gray-500 text-center text-white" v-else-if="log.inventoryTo && log.inventoryTo.descriptor !== 'unknown'">
                                    {{ log.inventoryTo.descriptor }}
                                </span>
                                <span class="font-semibold w-inventory block px-2 py-2 bg-gray-500 text-center text-white" v-else>
                                    {{ t('inventories.character.unknown') }}
                                </span>
                            </div>
                        </td>
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
import Layout from '../../Layouts/App';
import VSection from '../../Components/Section';
import Pagination from '../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        Pagination,
        VSection,
    },
    methods: {
        inventoryColor(type) {
            switch(type) {
                case 'character':
                    return 'bg-green-600 hover:bg-green-500 text-white';
                case 'trunk':
                    return 'bg-blue-700 hover:bg-blue-600 text-white';
                case 'glovebox':
                    return 'bg-indigo-700 hover:bg-indigo-600 text-white';
                case 'motel':
                case 'property':
                    return 'bg-yellow-400 hover:bg-yellow-400 text-black';
                case 'locker-police':
                case 'locker-ems':
                case 'locker-mechanic':
                    return 'bg-pink-400 hover:bg-pink-400 text-black';
                case 'evidence':
                    return 'bg-purple-400 hover:bg-purple-400 text-black';
                default:
                    return 'bg-gray-800 hover:bg-gray-700 text-white';
            }
        },
        playerName(steamIdentifier) {
            return steamIdentifier in this.playerMap ? this.playerMap[steamIdentifier] : steamIdentifier;
        }
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
};
</script>
