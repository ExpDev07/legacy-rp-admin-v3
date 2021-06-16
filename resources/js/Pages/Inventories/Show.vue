<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white" v-if="inventory">
                {{ t('inventories.show.title') }} {{ inventory.title }}
            </h1>
            <h1 class="dark:text-white" v-else>
                {{ t('inventories.show.error') }}
            </h1>
        </portal>

        <!-- Table -->
        <v-section class="overflow-x-auto" v-if="inventory">
            <template #header>
                <h2>
                    {{ t('global.info') }}
                </h2>
                <p>
                    {{ t('inventories.show.type.' + inventory.type) }}
                </p>
            </template>

            <template>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-9 max-w-screen-lg" v-if="inventory.type === 'character'">
                    <card v-if="inventory.character">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.character') }}
                            </h3>
                            <h4>
                                {{ inventory.character.first_name }} {{ inventory.character.last_name }} (#{{ inventory.character.character_id }})
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span> {{ $moment(inventory.character.date_of_birth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="inventory.character.character_deleted">
                                <span>{{ t('players.edit.deleted') }}:</span> {{ $moment(inventory.character.character_deletion_timestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ inventory.character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link class="px-4 py-3 mb-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/players/' + inventory.character.steam_identifier + '/characters/' + inventory.character.character_id + '/edit'">
                                {{ t('inventories.show.view_character') }}
                            </inertia-link>
                            <inertia-link class="px-4 py-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/players/' + inventory.character.steam_identifier">
                                {{ t('inventories.show.view_player') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-9 max-w-screen-lg" v-else-if="inventory.type === 'trunk' || inventory.type === 'glovebox'">
                    <card v-if="inventory.vehicle">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.vehicle') }}
                            </h3>
                            <h4>
                                {{ inventory.vehicle.model_name }}
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.vehicles.plate') }}:</span> {{ inventory.vehicle.plate }}
                            </h4>
                        </template>

                        <template>
                            <p v-html="t('players.vehicles.parked', inventory.vehicle.garage_identifier)">
                                {{ t('players.vehicles.parked', inventory.vehicle.garage_identifier) }}
                            </p>
                        </template>
                    </card>
                    <card v-if="inventory.character">
                        <template #header>
                            <h3 class="mb-2">
                                {{ t('inventories.show.owner') }}
                            </h3>
                            <h4>
                                {{ inventory.character.first_name }} {{ inventory.character.last_name }} (#{{ inventory.character.character_id }})
                            </h4>
                            <h4 class="text-primary dark:text-dark-primary">
                                <span>{{ t('players.edit.dob') }}:</span> {{ $moment(inventory.character.date_of_birth).format('l') }}
                            </h4>
                            <h4 class="text-red-700 dark:text-red-300" v-if="inventory.character.character_deleted">
                                <span>{{ t('players.edit.deleted') }}:</span> {{ $moment(inventory.character.character_deletion_timestamp).format('l') }}
                            </h4>
                        </template>

                        <template>
                            <p>
                                {{ inventory.character.backstory }}
                            </p>
                        </template>

                        <template #footer>
                            <inertia-link class="px-4 py-3 mb-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/players/' + inventory.character.steam_identifier + '/characters/' + inventory.character.character_id + '/edit'">
                                {{ t('inventories.show.view_character') }}
                            </inertia-link>
                            <inertia-link class="px-4 py-3 block text-center text-white bg-indigo-600 dark:bg-indigo-400 rounded" :href="'/players/' + inventory.character.steam_identifier">
                                {{ t('inventories.show.view_player') }}
                            </inertia-link>
                        </template>
                    </card>
                </div>
            </template>
        </v-section>

    </div>
</template>

<script>
import Layout from '../../Layouts/App';
import VSection from '../../Components/Section';
import Pagination from '../../Components/Pagination';
import Card from './../../Components/Card';

export default {
    layout: Layout,
    components: {
        Pagination,
        VSection,
        Card,
    },
    props: {
        inventory: {
            type: Object,
            required: true,
        }
    },
};
</script>
