<template>
    <div>
        <portal to="title">
            <div class="flex items-start space-x-10">
                <h1 class="dark:text-white" v-if="server.name">
                    {{ server.name }}
                </h1>
                <h1 class="dark:text-white" v-else>
                    {{ t('servers.list.server') }} #{{ server.id }}
                </h1>
                <div class="flex items-center space-x-5">
                    <badge class="border-gray-200 bg-secondary dark:bg-dark-secondary">
                        {{ t('servers.show.players', players.length) }}
                    </badge>
                </div>
            </div>
        </portal>

        <portal to="actions">
            <div>
                <button class="px-5 py-2 font-semibold text-white rounded bg-warning dark:bg-dark-warning mr-2"
                        @click="isEditing = true" v-if="$page.auth.player.isSuperAdmin">
                    <i class="mr-1 fas fa-edit"></i>
                    {{ t('servers.edit.edit') }}
                </button>

                <inertia-link class="px-5 py-2 font-semibold text-white rounded bg-danger dark:bg-dark-danger"
                              method="DELETE" v-bind:href="'/servers/' + server.id"
                              v-if="$page.auth.player.isSuperAdmin">
                    <i class="mr-1 fas fa-trash-alt"></i>
                    {{ t('servers.show.delete') }}
                </inertia-link>
            </div>
        </portal>

        <modal :show.sync="isEditing">
            <template #header>
                <h1 class="dark:text-white">
                    {{ t('servers.edit.edit') }}
                </h1>
                <p class="dark:text-dark-muted">
                    {{ t('servers.add.description') }}: <code class="inline dark:text-dark-muted">/api.json</code> &
                    <code class="inline dark:text-dark-muted">/connections.json</code>.
                </p>
            </template>

            <template #default>
                <div>
                    <label class="block mb-3" for="url">
                        {{ t('servers.add.url') }}
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="url"
                           id="url" placeholder="https://c2s1.op-framework.com/op-framework" v-model="form.url"
                           required>
                </div>
                <div>
                    <label class="block mb-3" for="name">
                        {{ t('servers.add.name') }}
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" id="name"
                           placeholder="Test Server" v-model="form.name" required>
                </div>
            </template>

            <template #actions>
                <button type="button"
                        class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400"
                        @click="isEditing = false">
                    {{ t('global.cancel') }}
                </button>
                <button type="submit" class="px-5 py-2 text-white bg-indigo-600 rounded dark:bg-indigo-400"
                        @click="handleEdit">
                    <i class="mr-1 fa fa-plus"></i>
                    {{ t('global.confirm') }}
                </button>
            </template>
        </modal>

        <v-section class="overflow-x-auto">
            <template #header>
                <h2>
                    {{ t('servers.show.table') }}
                </h2>
            </template>

            <template>
                <table class="w-full whitespace-no-wrap">
                    <tr class="font-semibold text-left">
                        <th class="px-6 py-4">{{ t('players.form.identifier') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.name') }}</th>
                        <th class="px-6 py-4">{{ t('servers.show.joined') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.playtime') }}</th>
                        <th class="px-6 py-4">{{ t('players.form.warnings') }}</th>
                        <th class="w-64 px-6 py-4">{{ t('players.form.banned') }}?</th>
                        <th class="w-24 px-6 py-4"></th>
                    </tr>
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-600" v-for="player in players"
                        v-bind:key="player.id">
                        <td class="px-6 py-3 border-t">{{ player.steamIdentifier }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playerName }}</td>
                        <td class="px-6 py-3 border-t">{{ player.lastConnection | formatTime }}</td>
                        <td class="px-6 py-3 border-t">{{ player.playTime | humanizeSeconds }}</td>
                        <td class="px-6 py-3 border-t">{{ player.warnings }}</td>
                        <td class="px-6 py-3 text-center border-t">
                            <span class="block px-4 py-2 text-white bg-red-500 rounded dark:bg-red-600"
                                  v-if="player.isBanned">
                                {{ t('global.banned') }}
                            </span>
                            <span class="block px-4 py-2 text-white bg-green-500 rounded dark:bg-green-600" v-else>
                                {{ t('global.not_banned') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 border-t">
                            <inertia-link
                                class="block px-4 py-2 font-semibold text-center text-white bg-indigo-600 rounded dark:bg-indigo-400"
                                v-bind:href="'/players/' + player.steamIdentifier">
                                <i class="fas fa-chevron-right"></i>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr v-if="players.length === 0">
                        <td class="px-6 py-6 text-center border-t" colspan="100%">
                            {{ t('players.none') }}
                        </td>
                    </tr>
                </table>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Badge from './../../Components/Badge';
import Alert from './../../Components/Alert';
import Card from './../../Components/Card';
import Avatar from './../../Components/Avatar';
import Modal from '../../Components/Modal';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        Alert,
        Card,
        Avatar,
        Modal,
    },
    props: {
        server: {
            type: Object,
            required: true,
        },
        players: {
            type: Array,
            required: true,
        },
    },
    methods: {
        async handleEdit() {
            this.isEditing = false;

            await this.$inertia.put('/servers/' + this.server.id, this.form);
        }
    },
    data() {
        return {
            isEditing: false,
            form: {
                url: this.server.url,
                name: this.server.name,
            },
        };
    },
};
</script>
