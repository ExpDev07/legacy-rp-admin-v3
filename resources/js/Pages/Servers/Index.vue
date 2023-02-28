<template>
    <div>
        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('servers.title') }}
            </h1>
            <p>
                {{ t('servers.description') }}
            </p>
        </portal>

        <v-section v-if="serverList.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-9">
                <card v-for="server in serverList" :no_footer="true">
                    <template #header>
                        <h3>
                            {{ server.name }}
                        </h3>
                        <h3 class="italic text-xs text-gray-600 dark:text-gray-300" v-if="server.version">
                            {{ server.version }}
                        </h3>
                    </template>

                    <template #default>
                        <div v-html="server.info"></div>
                        <div class="mt-3 italic text-xs text-gray-600 dark:text-gray-400">
                            {{ server.url }}
                        </div>
                    </template>
                </card>
            </div>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Card from  '../../Components/Card';

export default {
    layout: Layout,
    components: {
        VSection,
        Card,
    },
    data() {
        const serverList = this.servers.map(server => {
            server.info = server.information
                ? this.t('servers.server_data', server.information.serverUptime, server.information.joinedAmount, server.information.maxClients, server.information.queueAmount)
                : this.t('servers.no_server_data');

            server.version = server.information ? server.information.serverVersion : false;

            return server;
        });

        return {
            serverList: serverList
        }
    },
    props: {
        servers: {
            type: Array,
            required: true
        }
    }
}
</script>
