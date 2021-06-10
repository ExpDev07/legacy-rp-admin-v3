<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('inventories.player.title') }}
            </h1>
            <p>
                {{ t('inventories.player.type.' + type) }}
            </p>
        </portal>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('logs.logs') }}
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('logs.player') }}</th>
                        <th class="px-6 py-4">{{ t('logs.details') }}</th>
                        <th class="px-6 py-4">{{ t('logs.timestamp') }}</th>
                        <th class="px-6 py-4">{{ t('inventories.player.from') }}</th>
                        <th class="px-6 py-4">{{ t('inventories.player.to') }}</th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="log in logs.data" :key="log.id">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" :href="'/players/' + log.steamIdentifier">
                                {{ log.playerName }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ log.details }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link v-if="inventoryDescriptor(log.details, 'from') !== 'unknown'" class="block px-2 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/inventory/' + inventoryDescriptor(log.details, 'from')">
                                {{ inventoryDescriptor(log.details, 'from') }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link v-if="inventoryDescriptor(log.details, 'to') !== 'unknown'" class="block px-2 py-1 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400" v-bind:href="'/inventory/' + inventoryDescriptor(log.details, 'to')">
                                {{ inventoryDescriptor(log.details, 'to') }}
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td class="px-4 py-6 text-center border-t" colspan="100%">
                            {{ t('logs.no_logs') }}
                        </td>
                    </tr>
                </table>
            </template>

            <template #footer>
                <pagination v-bind:links="logs.links" v-bind:meta="logs.meta" />
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
        inventoryDescriptor(details, type) {
            const match = (new RegExp(type + ' (inventory )?((character|trunk|glovebox|ground)-(\\d+-)?[^- \.]+)', 'gmi')).exec(details);
            return match && match.length >= 3 ? match[2] : 'unknown';
        }
    },
    props: {
        logs: {
            type: Object,
            required: true,
        },
        type: {
            type: String,
            required: true,
        }
    },
};
</script>
