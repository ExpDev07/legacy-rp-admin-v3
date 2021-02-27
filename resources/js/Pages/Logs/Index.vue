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
        <div class="rounded bg-gray-100 p-8 mb-8">
            <form class="w-full" @submit.prevent>
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
        </div>

        <!-- Table -->
        <div class="bg-gray-100 rounded shadow overflow-x-auto">
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
                    <td class="px-6 py-3 border-t">{{ new Date(log.timestamp).toLocaleString() }}</td>
                    <td class="px-6 py-3 border-t">{{ log.server }}</td>
                </tr>
                <tr v-if="logs.data.length === 0">
                    <td class="px-4 py-6 border-t text-center" colspan="100%">
                        No logged actions were found.
                    </td>
                </tr>
            </table>
        </div>
        <pagination v-bind:links="logs.links" v-bind:meta="logs.meta" />
    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import Pagination from './../../Components/Pagination';
import throttle from 'lodash/throttle';

export default {
    layout: Layout,
    components: {
        Pagination,
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
