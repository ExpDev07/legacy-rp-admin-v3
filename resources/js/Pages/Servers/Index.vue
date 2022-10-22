<template>
    <div>
        <modal :show.sync="isAdding">
            <template #header>
                <h1 class="dark:text-white">
                    {{ t('servers.add.add') }}
                </h1>
                <p class="dark:text-dark-muted">
                    {{ t('servers.add.description') }}: <code class="inline dark:text-dark-muted">/api.json</code> & <code class="inline dark:text-dark-muted">/connections.json</code>.
                </p>
            </template>

            <template #default>
                <div>
                    <label class="block mb-3" for="url">
                        {{ t('servers.add.url') }}
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="url" id="url" placeholder="https://c2s1.op-framework.com/op-framework" v-model="form.url" required>
                </div>
                <div>
                    <label class="block mb-3" for="name">
                        {{ t('servers.add.name') }}
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="name" placeholder="Test Server" v-model="form.name" required>
                </div>
            </template>

            <template #actions>
                <button type="button" class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400" @click="isAdding = false">
                    {{ t('global.cancel') }}
                </button>
                <button type="submit" class="px-5 py-2 text-white bg-indigo-600 rounded dark:bg-indigo-400" @click="handleAdd">
                    <i class="mr-1 fa fa-plus"></i>
                    {{ t('servers.add.do') }}
                </button>
            </template>
        </modal>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('servers.title') }}
            </h1>
            <p>
                {{ t('servers.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button
                class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                type="button"
                @click="isAdding = true"
                v-if="$page.auth.player.isSuperAdmin"
            >
                <i class="mr-1 fa fa-plus"></i>
                {{ t('servers.add.add') }}
            </button>
        </portal>

        <v-section v-if="servers.data.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                <card
                    v-for="(server) in servers.data"
                    :key="server.id"
                >
                    <template #header>
                        <h3 class="mb-2" v-if="server.name">
                            {{ server.name }}
                        </h3>
                        <h3 class="mb-2" v-else>
                            {{ t('servers.list.server') }} #{{ server.id }}
                        </h3>
                        <h4 class="italic text-primary dark:text-dark-primary" v-if="server.information !== null">
                            {{ t('servers.list.running') }} {{ server.information.serverVersion }}
                        </h4>
                    </template>

                    <template #default>
                        <ul class="list-disc list-inside" v-if="server.information !== null">
                            <li>
                                {{ t('servers.list.uptime') }}: <span class="font-semibold">{{ server.information.serverUptime }}</span>
                            </li>
                            <li>
                                {{ t('servers.list.joined') }}: <span class="font-semibold">{{ server.information.joinedAmount }} / {{ server.information.maxClients }}</span>
                            </li>
                            <li>
                                {{ t('servers.list.queued') }}: <span class="font-semibold">{{ server.information.queueAmount }}</span>
                            </li>
                            <li>
                                {{ t('servers.list.loading') }}: <span class="font-semibold">{{ server.information.joiningAmount }}</span>
                            </li>
                        </ul>
                        <p class="mt-2" v-else>
                            {{ t('servers.no_data') }}
                        </p>
                        <p class="mt-6 italic text-muted dark:text-dark-muted">
                            {{ t('servers.list.retrieved', server.url) }}
                        </p>
                    </template>

                    <template #footer>
                        <inertia-link class="block px-4 py-3 text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/servers/' + server.id">
                            {{ t('global.view') }}
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
                name: '',
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
