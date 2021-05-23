<template>
    <div>

        <modal :show.sync="isAdding">
            <template #header>
                <h1>
                   Add server
                </h1>
                <p>
                    Provide the server URL where the data should be fetched from. The URL should expose two
                    endpoints: <code class="inline">/api.json</code> and <code class="inline">/connections.json</code>.
                </p>
            </template>

            <template #default>
                <div>
                    <label class="block mb-3" for="url">
                        URL
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded" id="url" placeholder="https://c3s1.op-framework.com/op-framework" v-model="form.url" required>
                </div>
            </template>

            <template #actions>
                <button type="button" class="px-5 py-2 rounded hover:bg-gray-200" @click="isAdding = false">
                    Cancel
                </button>
                <button type="submit"  class="px-5 py-2 text-white bg-indigo-600 rounded" @click="handleAdd">
                    <i class="mr-1 fa fa-plus"></i>
                    Add
                </button>
            </template>
        </modal>

        <portal to="title">
            <h1>
                Servers
            </h1>
            <p>
                An overview of the game servers.
            </p>
        </portal>

        <portal to="actions">
            <button 
                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded" 
                type="button"
                @click="isAdding = true" 
                v-if="$page.auth.player.isSuperAdmin"
            >
                <i class="mr-1 fa fa-plus"></i>
                Add server
            </button>
        </portal>

        <v-section v-if="servers.data.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                <card
                    v-for="(server) in servers.data"
                    :key="server.id"
                >
                    <template #header>
                        <h3 class="mb-2">
                            Server #{{ server.id }}
                        </h3>
                        <h4 class="italic text-primary">
                            Running {{ server.information.serverVersion }}
                        </h4>
                    </template>

                    <template #default>
                        <ul class="list-disc list-inside">
                            <li>
                                Uptime: <span class="font-semibold">{{ server.information.serverUptime }}</span>
                            </li>
                            <li>
                                Joined: <span class="font-semibold">{{ server.information.joinedAmount }} / {{ server.information.maxClients }}</span>
                            </li>
                            <li>
                                Queued: <span class="font-semibold">{{ server.information.queueAmount }}</span>
                            </li>
                            <li>
                                Loading: <span class="font-semibold">{{ server.information.joiningAmount }}</span>
                            </li>
                        </ul>
                        <p class="mt-6 italic text-muted">
                            Retrieved from {{ server.url }}
                        </p>
                    </template>

                    <template #footer>
                        <inertia-link class="block px-4 py-3 text-center text-white bg-indigo-600 rounded" :href="'/'">
                            View
                        </inertia-link>
                    </template>
                </card>
            </div>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Modal from  '../../Components/Modal';
import Card from  '../../Components/Card';

export default {
    layout: Layout,
    components: {
        VSection,
        Modal,
        Card,
    },
    props: {
        servers: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            isAdding: false,
            form: {
                url: '',
            },
        }
    },
    methods: {
        /**
         * Handles adding of server.
         */
        async handleAdd() {
            this.isAdding = false;
            await this.$inertia.post('/servers', this.form);
        }
    },
}
</script>
