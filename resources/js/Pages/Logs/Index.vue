<template>
    <div>

        <portal to="title">
            <h1>
                Logs
            </h1>
            <p>
                On this page you can view and filter the server logs.
            </p>
        </portal>

        <portal to="actions">
            <button class="bg-indigo-600 font-semibold text-white text-sm rounded px-4 py-2" type="button" @click="refresh">
                <i class="fa fa-refresh mr-1"></i>
                Refresh
            </button>
        </portal>

        <!-- Querying -->
        <v-section>
            <template #header>
                <h2>
                    Filter logs
                </h2>
            </template>

            <template>
                <form @submit.prevent>
                    <div class="flex flex-wrap mb-4">
                        <!-- Identifier -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="identifier">
                                Player Identifier
                            </label>
                            <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="identifier" placeholder="steam:11000010df22c8b" v-model="filters.identifier">
                        </div>
                        <!-- Action -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="action">
                                Action
                            </label>
                            <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="action" placeholder="Death" v-model="filters.action">
                        </div>
                        <!-- Server -->
                        <div class="w-1/3 px-3">
                            <label class="block mb-2" for="server">
                                Server ID
                            </label>
                            <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="server" placeholder="3" type="number" v-model="filters.server">
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="w-full px-3">
                        <label class="block mb-3" for="details">
                            Details
                        </label>
                        <input class="block w-full border rounded bg-gray-200 py-3 px-4 mb-3" id="details" placeholder="Marius killed jax with an AK-47" v-model="filters.details">
                    </div>
                </form>
            </template>
        </v-section>

        <!-- Table -->
        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    Logs
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="text-left font-semibold">
                        <th class="px-6 py-4">Player</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Details</th>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Server ID</th>
                    </tr>
                    <tr class="hover:bg-gray-100" v-for="log in logs.data" :key="log.id">
                        <td class="px-6 py-3 border-t">
                            <inertia-link class="block bg-indigo-600 font-semibold text-white text-center rounded px-4 py-2" :href="'/players/' + log.player.steamIdentifier">
                                {{ log.player.playerName }}
                            </inertia-link>
                        </td>
                        <td class="px-6 py-3 border-t">{{ log.action }}</td>
                        <td class="px-6 py-3 border-t">{{ log.details }}</td>
                        <td class="px-6 py-3 border-t">{{ log.timestamp | formatTime(true) }}</td>
                        <td class="px-6 py-3 border-t">{{ log.server }}</td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td class="px-4 py-6 border-t text-center" colspan="100%">
                            No logged actions were found.
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
import throttle from 'lodash/throttle';
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
            type: Object,
            required: true,
        },
        filters: {
            identifier: String,
            action: String,
            server: Number,
            details: String,
        }
    },
    methods: {
        refresh: function () {
            this.$inertia.replace('/logs', {
                data: this.filters,
                preserveState: true,
                preserveScroll: true,
                only: [ 'logs' ],
            });
        },
    },
    watch: {
        filters: {
            deep: true,
            handler: throttle(function () {
                this.refresh();
            }, 150),
        }
    }
};
</script>
