<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('inventories.history.title', id, itemName) }}
            </h1>
            <p>
                {{ t('inventories.history.description') }}
            </p>
        </portal>

        <!-- Table -->
        <v-section class="overflow-x-auto" :noFooter="true">
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
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.licenseIdentifier">
                                {{ playerName(log.licenseIdentifier) }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ log.itemMoved }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t">
                            <div class="flex">
                                <inertia-link v-if="log.inventoryFrom" :class="'w-inventory block px-2 py-2 font-semibold text-center ' + inventoryColor(log.inventoryFrom)" v-bind:href="'/inventory/' + log.inventoryFrom">
                                    {{ log.inventoryFrom }}
                                </inertia-link>
                                <span class="font-semibold w-inventory block px-2 py-2 bg-gray-500 text-center text-white" v-else>
                                    {{ t('inventories.character.unknown') }}
                                </span>

                                <span class="font-semibold block px-2 py-2 bg-gray-600 text-center text-white">
                                    &#11166;
                                </span>

                                <inertia-link v-if="log.inventoryTo" :class="'block w-inventory px-2 py-2 font-semibold text-center ' + inventoryColor(log.inventoryTo)" v-bind:href="'/inventory/' + log.inventoryTo">
                                    {{ log.inventoryTo }}
                                </inertia-link>
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
        inventoryColor(descriptor) {
            const type = descriptor.split('-')[0];

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
        playerName(licenseIdentifier) {
            return licenseIdentifier in this.playerMap ? this.playerMap[licenseIdentifier] : licenseIdentifier;
        }
    },
    props: {
        id: {
            type: Number,
            required: true,
        },
        itemName: {
            type: String,
            required: true,
        },
        logs: {
            type: Array,
            required: true,
        },
        playerMap: {
            type: Object,
            required: true,
        },
        time: {
            type: Number,
            required: true,
        }
    },
};
</script>
